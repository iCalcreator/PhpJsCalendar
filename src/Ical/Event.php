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
use Kigkonsult\Icalcreator\Vevent;
use Kigkonsult\Icalcreator\Vtimezone;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;

class Event extends BaseEventTask
{
    /**
     * Ical Event properties to iCal Vevent
     *
     * @param EventDto $eventDto
     * @param Vevent $vevent
     * @return mixed[]  id[Vtimezone]
     * @throws Exception
     */
    public static function processTo( EventDto $eventDto, Vevent $vevent  ) : array
    {
        parent::groupEventTaskProcessTo( $eventDto, $vevent );
        [ $vtimezones, $startDateTime ] = parent::eventTaskProcessTo( $eventDto, $vevent );

        $duration = $eventDto->getDuration( false );
        switch( true) {
            case empty( $duration ) :
                break;
            case empty( $startDateTime ) :
                $vevent->setDuration( $duration );
                break;
            default :
                $vevent->setDtend( $startDateTime->add( $duration ));
                break;
        }

        if( $eventDto->isStatusSet()) {
            $vevent->setStatus( $eventDto->getStatus());
        }

        return $vtimezones;
    }

    /**
     * Ical iCal Vevent properties to Event properties
     *
     * @param Vevent $vevent
     * @param Vtimezone[] $vtimezones
     * @return EventDto
     * @throws Exception
     */
    public static function processFrom( Vevent $vevent, array $vtimezones ) : EventDto
    {
        $eventDto = new EventDto();
        parent::groupEventTaskProcessFrom( $vevent, $eventDto  );
        $startDateTime = parent::eventTaskProcessFrom( $vevent, $eventDto, $vtimezones );

        if( $vevent->isDurationSet()) {
            $eventDto->setDuration( $vevent->getDuration());
        }
        elseif(( null !== $startDateTime ) && $vevent->isDtendSet()) {
            $dtEnd = $vevent->getDtend();
            $eventDto->setDuration( $startDateTime->diff( $dtEnd->setTimezone( $startDateTime->getTimezone())));
        }

        if( $vevent->isStatusSet()) {
            $eventDto->setStatus( strtolower( $vevent->getStatus()));
        }

        return $eventDto;
    }
}
