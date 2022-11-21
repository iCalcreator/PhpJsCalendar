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
use Kigkonsult\Icalcreator\CalendarComponent as IcalComponent;
use Kigkonsult\Icalcreator\Vevent            as IcalVevent;
use Kigkonsult\Icalcreator\Vtimezone         as IcalVtimezone;
use Kigkonsult\PhpJsCalendar\Dto\Event       as EventDto;

class Event extends BaseEventTask
{
    /**
     * Event properties to iCal Vevent
     *
     * @param EventDto $eventDto
     * @param IcalVevent $iCalVevent
     * @return array  id[IcalVtimezone]
     * @throws Exception
     */
    public static function processToIcal( EventDto $eventDto, IcalVevent $iCalVevent  ) : array
    {
        parent::groupEventTaskProcessToIcal( $eventDto, $iCalVevent );
        [ $iCalVtimezones, $startDateTime ] = parent::eventTaskProcessToIcal( $eventDto, $iCalVevent );
        $duration = $eventDto->getDuration( false );
        switch( true) {
            case empty( $duration ) :
                break;
            case empty( $startDateTime ) :
                $iCalVevent->setDuration( $duration );
                break;
            default :
                $iCalVevent->setDtend(
                    $startDateTime->add( $duration ),
                    (( $eventDto->isShowWithoutTimeSet() && $eventDto->getShowWithoutTime())
                        ? [ IcalVevent::VALUE => IcalVevent::DATE ]
                        : []
                    )
                );
                break;
        } // end switch
        if( $eventDto->isStatusSet()) {
            $iCalVevent->setStatus( $eventDto->getStatus());
        }
        return $iCalVtimezones;
    }

    /**
     * Ical iCal Vevent properties to Event properties
     *
     * @param IcalComponent|IcalVevent $iCalVevent
     * @param IcalVtimezone[] $iCalVtimezones
     * @return EventDto
     * @throws Exception
     */
    public static function processFromIcal(
        IcalComponent|IcalVevent $iCalVevent,
        array $iCalVtimezones
    ) : EventDto
    {
        $eventDto = new EventDto();
        parent::groupEventTaskProcessFromIcal( $iCalVevent, $eventDto  );
        $startDateTime = parent::eventTaskProcessFromIcal( $iCalVevent, $eventDto, $iCalVtimezones );
        if( $iCalVevent->isDurationSet()) {
            $eventDto->setDuration( $iCalVevent->getDuration());
        }
        elseif(( null !== $startDateTime ) && $iCalVevent->isDtendSet()) {
            $dtEnd = $iCalVevent->getDtend();
            $eventDto->setDuration( $startDateTime->diff( $dtEnd->setTimezone( $startDateTime->getTimezone())));
        }
        if( $iCalVevent->isStatusSet()) {
            $eventDto->setStatus( strtolower( $iCalVevent->getStatus()));
        }
        return $eventDto;
    }
}
