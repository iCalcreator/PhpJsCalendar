<?php
/**
 * PhpJsCalendar is the PHP implementation of rfc8984, A JSON Representation of Calendar Data
 *
 * This file is a part of PhpJsCalendar.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2022 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @license   Subject matter of licence is the software PhpJsCalendar.
 *            The above copyright, link, package and version notices,
 *            this licence notice and the invariant [rfc5545] PRODID result use
 *            as implemented and invoked in PhpJsCalendar shall be included in
 *            all copies or substantial portions of the PhpJsCalendar.
 *
 *            PhpJsCalendar is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *            PhpJsCalendar is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public License
 *            along with PhpJsCalendar. If not, see <https://www.gnu.org/licenses/>.
 */
declare( strict_types = 1 );
namespace Kigkonsult\PhpJsCalendar;

use Exception;
use InvalidArgumentException;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\PhpJsCalendar\Dto\Event  as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Group  as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Task   as TaskDto;
use Kigkonsult\PhpJsCalendar\Json\Event as EventJsonFactory;
use Kigkonsult\PhpJsCalendar\Json\Group as GroupJsonFactory;
use Kigkonsult\PhpJsCalendar\Json\Task  as TaskJsonFactory;
use Kigkonsult\PhpJsCalendar\Ical\Event as EventIcalFactory;
use Kigkonsult\PhpJsCalendar\Ical\Group as GroupIcalFactory;
use Kigkonsult\PhpJsCalendar\Ical\Task  as TaskIcalFactory;
use RuntimeException;

class PhpJsCalendar implements BaseInterface
{
    /**
     * @var null|string
     */
    private ? string $jsonString = null;

    /**
     * @var null|GroupDto|EventDto|TaskDto
     */
    private null|GroupDto|EventDto|TaskDto $dto = null;

    /**
     * @var null|Vcalendar
     */
    private ? Vcalendar $vcalendar = null;

    /**
     * Class factory method
     *
     * @param null|string $jsonString
     * @param null|GroupDto|EventDto|TaskDto $dto
     * @param null|Vcalendar $vcalendar
     * @return static
     */
    public static function factory(
        ? string $jsonString = null,
        null|GroupDto|EventDto|TaskDto $dto = null,
        ? Vcalendar $vcalendar = null
    ) : static
    {
        $instance   = new self();
        if( null !== $jsonString ) {
            $instance->setJsonString( $jsonString );
        }
        if( null !== $dto ) {
            $instance->setDto( $dto );
        }
        if( null !== $vcalendar ) {
            $instance->setVcalendar( $vcalendar );
        }
        return $instance;
    }

    /**
     * Class factory method, parse jsonString into Dto, return static
     *
     * @param string $jsonString
     * @return static
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public static function factoryJsonParse( string $jsonString ) : static
    {
        return self::factory( $jsonString )->jsonParse();
    }

    /**
     * Class factory method, write Dto into jsonString, return static
     *
     * @param GroupDto|EventDto|TaskDto $dto
     * @param null|bool $prettyPrint  default false
     * @return static
     * @throws RuntimeException
     */
    public static function factoryJsonWrite(
        GroupDto|EventDto|TaskDto $dto,
        ? bool $prettyPrint = false
    ) : static
    {
        return self::factory()->jsonWrite( $dto, $prettyPrint );
    }

    /**
     * Parse jsonString into Dto
     *
     * @param null|string $jsonString
     * @param null|int $flags    default JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR
     * @return static
     * @throws Exception
     * @throws RuntimeException
     */
    public function jsonParse(
        ? string $jsonString = null,
        ? int $flags = JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR
    ) : static
    {
        static $ERR1  = 'No jsonString to parse';
        static $ERR2  = 'NO decode result array';
        static $ERR3  = 'NO top object @type set';
        static $ERR4  = 'Unknown @type ';
        if( null !== $jsonString ) {
            $this->setJsonString( $jsonString );
        }
        elseif( empty( $this->jsonString )) {
            throw new RuntimeException( $ERR1 );
        }
        $jsonArray = Json::jsonDecode( $this->jsonString, $flags );
        if( ! is_array( $jsonArray )) {
            throw new RuntimeException( $ERR2 );
        }
        $objectType = $jsonArray[self::OBJECTTYPE] ?? $ERR3;
        $this->dto = match( $objectType ) {
            self::GROUP => GroupJsonFactory::parse( $jsonArray ),
            self::EVENT => EventJsonFactory::parse( $jsonArray ),
            self::TASK  => TaskJsonFactory::parse( $jsonArray ),
            default     => throw new RuntimeException( $ERR4 . $objectType ),
        };
        return $this;
    }

    /**
     * Write Dto into jsonString
     *
     * @param null|GroupDto|EventDto|TaskDto $dto
     * @param null|bool $prettyPrint  default false
     * @param null|int $flags    default JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
     * @return static
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function jsonWrite(
        null|GroupDto|EventDto|TaskDto $dto = null,
        ? bool $prettyPrint = false,
        ? int $flags = JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    ) : static
    {
        static $ERR1 = 'No DTO exists for json write';
        static $ERR2 = 'unknown @type %s, expects %s';
        static $BS   = '\\';
        if( null !== $dto ) {
            $this->setDto( $dto );
        }
        elseif( empty( $this->dto )) {
            throw new RuntimeException( $ERR1 );
        }
        $objectType = $this->getDtoType();
        try {
            switch( $objectType ) {
                case self::GROUP :
                    $jsonArray = GroupJsonFactory::write( $this->dto );
                    break;
                case self::EVENT :
                    $jsonArray = EventJsonFactory::write( $this->dto );
                    break;
                case self::TASK :
                    $jsonArray = TaskJsonFactory::write( $this->dto );
                    break;
                default :
                    $expType = array_slice( explode( $BS, $this->dto::class ), -1, 1 );
                    throw new InvalidArgumentException( sprintf( $ERR2, $objectType, $expType ) );
            }
        }
        catch( Exception $e ) {
            throw new RuntimeException( $e->getMessage(), $e->getCode(), $e );
        }
        if( $prettyPrint ) {
            $flags |= JSON_PRETTY_PRINT;
        }
        $this->jsonString = Json::jsonEncode( $jsonArray, $flags );
        return $this;
    }

    /**
     * Transform Dto to Vcalendar
     *
     * @param null|GroupDto|EventDto|TaskDto $dto
     * @param mixed[] $config  Vcalendar config
     * @return PhpJsCalendar
     * @throws InvalidArgumentException
     * @throws RuntimeException|Exception
     */
    public function iCalWrite( null|GroupDto|EventDto|TaskDto $dto = null, array $config = [] ) : PhpJsCalendar
    {
        static $ERR1 = 'No DTO exists for convertion to Vcalendar';
        static $ERR2 = 'unknown @type %s, expects %s';
        static $BS   = '\\';
        if( null !== $dto ) {
            $this->setDto( $dto );
        }
        elseif( empty( $this->dto )) {
            throw new RuntimeException( $ERR1 );
        }
        $this->vcalendar = new Vcalendar( $config ?? [] );
        $objectType      = $this->getDtoType();
        switch( $objectType ) {
            case self::GROUP :
                try {
                    $vtimezones = GroupIcalFactory::processTo( $this->dto, $this->vcalendar );
                }
                catch( Exception $e ) {
                    throw new RuntimeException( $e->getMessage(), $e->getCode(), $e );
                }
                break;
            case self::EVENT :
                GroupIcalFactory::setDtoMethod2Ical( $this->dto, $this->vcalendar );
                try {
                    $vtimezones = EventIcalFactory::processTo( $this->dto, $this->vcalendar->newVevent());
                }
                catch( Exception $e ) {
                    throw new RuntimeException( $e->getMessage(), $e->getCode(), $e );
                }
                break;
            case self::TASK :
                GroupIcalFactory::setDtoMethod2Ical( $this->dto, $this->vcalendar );
                try {
                    $vtimezones = TaskIcalFactory::processTo( $this->dto, $this->vcalendar->newVtodo());
                }
                catch( Exception $e ) {
                    throw new RuntimeException( $e->getMessage(), $e->getCode(), $e );
                }
                break;
            default :
                $expType = array_slice( explode( $BS, $this->dto::class ), -1, 1 );
                throw new InvalidArgumentException( sprintf( $ERR2, $objectType, $expType ));
        }
        if( ! empty( $vtimezones )) {
            foreach( $vtimezones as $vtimezone ) {
                $this->vcalendar->setComponent( $vtimezone );
            }
            $this->vcalendar->sort();
        }
        return $this;
    }

    /**
     * Transform Vcalendar into Group/Event/Task
     *
     * Param forceGroup = true return Group
     * Single Vevent or Vtodo return Event/Task
     * Multi Vevent/Vtodo return Group
     *
     * @param null|Vcalendar $vcalendar
     * @param null|bool $forceGroup
     * @return static
     * @throws RuntimeException
     * @throws Exception
     */
    public function iCalParse( ? Vcalendar $vcalendar = null, ? bool $forceGroup = false ) : static
    {
        static $ERR1 = 'No Vcalendar exists for convertion';
        static $ERR2 = 'Empty Vcalendar, no Vevents/Vtodos';
        if( null !== $vcalendar ) {
            $this->setVcalendar( $vcalendar );
        }
        elseif( empty( $this->vcalendar )) {
            throw new RuntimeException( $ERR1 );
        }
        $vtimezones = $vevents = $vtodos = [];
        $this->vcalendar->resetCompCounter();
        while( false !== ( $component = ( $this->vcalendar->getComponent()))) {
            switch( true ) {
                case ( Vcalendar::VTIMEZONE === $component->getCompType()) :
                    if( false !== ( $timezoneId = $component->getTzid())) {
                        $vtimezones[$timezoneId] = $component;
                    }
                    else {
                        $vtimezones[] = $component; // ??
                    }
                    break;
                case ( Vcalendar::VEVENT === $component->getCompType()) :
                    $vevents[] = $component;
                    break;
                case ( Vcalendar::VTODO === $component->getCompType()) :
                    $vtodos[] = $component;
                    break;
            } // end witch
        } // end while
        switch( true ) {
            case ( empty( $vevents ) && empty( $vtodos )) :
                throw new RuntimeException( $ERR2 );
            case $forceGroup : // fall through
            case ( 1 < count( $vevents ) || ( 1 < count( $vtodos ))) : // fall through
            case ( 0 < count( $vevents ) && ( 0 < count( $vtodos ))) :
                $this->dto = GroupIcalFactory::processFrom( $this->vcalendar, $vtimezones );
                break;
            case ( 1 === count( $vevents )) :
                $this->dto = EventIcalFactory::processFrom( reset( $vevents ), $vtimezones );
                if( ! $this->dto->isMethodSet()) {
                    GroupIcalFactory::setIcalMethod2Dto( $this->vcalendar, $this->dto );
                }
                break;
            case ( 1 === count( $vtodos )) : // fall through
            default :
                $this->dto = TaskIcalFactory::processFrom( reset( $vtodos ), $vtimezones );
                if( ! $this->dto->isMethodSet()) {
                    GroupIcalFactory::setIcalMethod2Dto( $this->vcalendar, $this->dto );
                }
                break;
        } // end switch
        return $this;
    }

    /**
     * Getters and Setters
     */

    /**
     * @return null|string
     */
    public function getDtoType() : ? string
    {
        return $this->dto->getType();
    }

    /**
     * Return bool true if Dto is Event
     *
     * @return bool
     * @throws RuntimeException
     */
    public function isDtoEvent() : bool
    {
        return ( self::EVENT === $this->getDtoType());
    }

    /**
     * Return bool true if Dto is Group
     *
     * @return bool
     * @throws RuntimeException
     */
    public function isDtoGroup() : bool
    {
        return ( self::GROUP === $this->getDtoType());
    }

    /**
     * Return bool true if Dto is Task
     *
     * @return bool
     * @throws RuntimeException
     */
    public function isDtoTask() : bool
    {
        return ( self::TASK === $this->getDtoType());
    }

    /*
     * get/set-methods
     */

    /**
     * @return null|string
     */
    public function getJsonString() : ? string
    {
        return $this->jsonString;
    }

    /**
     * @param string $jsonString
     * @return PhpJsCalendar
     */
    public function setJsonString( string $jsonString ) : PhpJsCalendar
    {
        $this->jsonString = $jsonString;
        return $this;
    }

    /**
     * @return null|EventDto|GroupDto|TaskDto
     */
    public function getDto() : null | TaskDto | EventDto | GroupDto
    {
        return $this->dto;
    }

    /**
     * @param EventDto|GroupDto|TaskDto $dto
     * @return PhpJsCalendar
     */
    public function setDto( TaskDto | EventDto | GroupDto $dto ) : PhpJsCalendar
    {
        $this->dto = $dto;
        return $this;
    }

    /**
     * @return Vcalendar|null
     */
    public function getVcalendar() : ? Vcalendar
    {
        return $this->vcalendar;
    }

    /**
     * @param Vcalendar $vcalendar
     * @return PhpJsCalendar
     */
    public function setVcalendar( Vcalendar $vcalendar ) : PhpJsCalendar
    {
        $this->vcalendar = $vcalendar;
        return $this;
    }
}
