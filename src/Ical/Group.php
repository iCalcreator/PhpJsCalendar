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
use Kigkonsult\Icalcreator\Vcalendar   as IcalVcalendar;
use Kigkonsult\Icalcreator\Vtimezone   as IcalVtimezone;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Group as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Task  as TaskDto;

class Group extends BaseGroupEventTask
{

    /**
     * Group Dto properties to iCal Vcalendar
     *
     * @param GroupDto $dto
     * @param IcalVcalendar $iCalVcalendar
     * @return IcalVtimezone[]
     * @throws Exception
     */
    public static function processToIcal( GroupDto $dto, IcalVcalendar $iCalVcalendar ) : array
    {
        parent::groupEventTaskProcessToIcal( $dto, $iCalVcalendar );
        if( $dto->isSourceSet()) {
            $iCalVcalendar->setSource( $dto->getSource());
        }
        if( empty( $dto->getEntriesCount())) {
            return [];
        }
        // array of "(Task|Event)[]"
        $vtimezones  = [];
        $isMethodSet = false;
        foreach( $dto->getEntries() as $entry ) {
            if( ! $isMethodSet ) { // first found
                self::setDtoMethod2Ical( $entry, $iCalVcalendar );
                $isMethodSet = true;
            }
            if( self::EVENT === $entry->getType()) {
                foreach( Event::processToIcal( $entry, $iCalVcalendar->newVevent()) as $timezoneId => $vtimezone ) {
                    $vtimezones[$timezoneId] = $vtimezone;
                }
            }
            elseif( self::TASK === $entry->getType() ) {
                foreach( Task::processToIcal( $entry, $iCalVcalendar->newVtodo()) as $timezoneId => $vtimezone ) {
                    $vtimezones[$timezoneId] = $vtimezone;
                }
            }
        } // end foreach
        return $vtimezones;
    }

    /**
     * Ical iCal Vcalendar to new Group
     *
     * @param IcalVcalendar $iCalVcalendar
     * @param IcalVtimezone[] $iCalVtimezones
     * @return GroupDto
     * @throws Exception
     */
    public static function processFromIcal( IcalVcalendar $iCalVcalendar, array $iCalVtimezones ) : GroupDto
    {
        $groupDto = new GroupDto();
        if( $iCalVcalendar->isSourceSet()) {
            $groupDto->setSource( $iCalVcalendar->getSource());
        }
        parent::groupEventTaskProcessFromIcal( $iCalVcalendar, $groupDto );
        $iCalVcalendar->resetCompCounter();
        foreach( $iCalVcalendar->getComponents()as $component ) {
            switch( true ) {
                case ( IcalVcalendar::VEVENT === $component->getCompType()) :
                    $entry = Event::processFromIcal( $component, $iCalVtimezones );
                    if( ! $entry->isMethodSet()) {
                        self::setIcalMethod2Dto( $iCalVcalendar, $entry );
                    }
                    $groupDto->addEntry( $entry );
                    break;
                case ( IcalVcalendar::VTODO === $component->getCompType()) :
                    $entry = Task::processFromIcal( $component, $iCalVtimezones );
                    if( ! $entry->isMethodSet()) {
                        self::setIcalMethod2Dto( $iCalVcalendar, $entry );
                    }
                    $groupDto->addEntry( $entry );
                    break;
                default:
                    break;
            } // end switch
        } // end foreach
        return $groupDto;
    }

    /**
     * @param IcalVcalendar $iCalVcalendar
     * @param EventDto|TaskDto $dto
     */
    public static function setIcalMethod2Dto( IcalVcalendar $iCalVcalendar, EventDto | TaskDto $dto ) : void
    {
        if( $iCalVcalendar->IsMethodSet()) {
            $dto->setMethod( $iCalVcalendar->getMethod());
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVcalendar $iCalVcalendar
     */
    public static function setDtoMethod2Ical( EventDto | TaskDto $dto, IcalVcalendar $iCalVcalendar ) : void
    {
        if( $dto->isMethodSet()) {
            $iCalVcalendar->setMethod( strtoupper( $dto->getMethod()));
        }
    }
}
