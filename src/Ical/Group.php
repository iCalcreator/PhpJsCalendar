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
namespace Kigkonsult\PhpJsCalendar\Ical;

use Exception;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vtimezone;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Group as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Task as TaskDto;

class Group extends BaseGroupEventTask
{

    /**
     * Ical Group Dto properties to iCal Vcalendar
     *
     * @param GroupDto $dto
     * @param Vcalendar $vcalendar
     * @return Vtimezone[]
     * @throws Exception
     */
    public static function processTo( GroupDto $dto, Vcalendar $vcalendar ) : array
    {
        parent::groupEventTaskProcessTo( $dto, $vcalendar );

        // array of "(Task|Event)[]"
        $vtimezones  = [];
        $isMethodSet = false;
        if( ! empty( $dto->getEntriesCount())) {
            foreach( $dto->getEntries() as $entry ) {
                if( ! $isMethodSet ) { // first found
                    self::setDtoMethod2Ical( $entry, $vcalendar );
                    $isMethodSet = true;
                }
                if( self::EVENT === $entry->getType()) {
                    foreach( Event::processTo( $entry, $vcalendar->newVevent()) as $timezoneId => $vtimezone ) {
                        $vtimezones[$timezoneId] = $vtimezone;
                    }
                }
                elseif( self::TASK === $entry->getType() ) {
                    foreach( Task::processTo( $entry, $vcalendar->newVtodo()) as $timezoneId => $vtimezone ) {
                        $vtimezones[$timezoneId] = $vtimezone;
                    }
                }
            }
        } // end if

        if( $dto->isSourceSet()) {
            $vcalendar->setSource( $dto->getSource());
        }

        return $vtimezones;
    }

    /**
     * Ical iCal Vcalendar to new Group
     *
     * @param Vcalendar $vcalendar
     * @param Vtimezone[] $vtimezones
     * @return GroupDto
     * @throws Exception
     */
    public static function processFrom( Vcalendar $vcalendar, array $vtimezones ) : GroupDto
    {
        $groupDto = new GroupDto();
        if( $vcalendar->isSourceSet()) {
            $groupDto->setSource( $vcalendar->getSource());
        }
        parent::groupEventTaskProcessFrom( $vcalendar, $groupDto );
        $vcalendar->resetCompCounter();
        while( false !== ( $component = ( $vcalendar->getComponent()))) {
            switch( true ) {
                case ( Vcalendar::VEVENT === $component->getCompType()) :
                    $entry = Event::processFrom( $component, $vtimezones );
                    if( ! $entry->isMethodSet()) {
                        self::setIcalMethod2Dto( $vcalendar, $entry );
                    }
                    $groupDto->addEntry( $entry );
                    break;
                case ( Vcalendar::VTODO === $component->getCompType()) :
                    $entry = Task::processFrom( $component, $vtimezones );
                    if( ! $entry->isMethodSet()) {
                        self::setIcalMethod2Dto( $vcalendar, $entry );
                    }
                    $groupDto->addEntry( $entry );
                    break;
                default:
                    break;
            } // end switch
        } // end while

        return $groupDto;
    }

    /**
     * @param Vcalendar $vcalendar
     * @param EventDto|TaskDto $dto
     */
    public static function setIcalMethod2Dto( Vcalendar $vcalendar, EventDto | TaskDto $dto ) : void
    {
        if( $vcalendar->IsMethodSet()) {
            $dto->setMethod( $vcalendar->getMethod());
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param Vcalendar $vcalendar
     */
    public static function setDtoMethod2Ical( EventDto | TaskDto $dto, Vcalendar $vcalendar ) : void
    {
        if( $dto->isMethodSet()) {
            $vcalendar->setMethod( strtoupper( $dto->getMethod()));
        }
    }
}
