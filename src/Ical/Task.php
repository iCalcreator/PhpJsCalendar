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
use Kigkonsult\Icalcreator\Vtimezone         as IcalVtimezone;
use Kigkonsult\Icalcreator\Vtodo             as IcalVtodo;
use Kigkonsult\PhpJsCalendar\Dto\Task        as TaskDto;

class Task extends BaseEventTask
{
    /**
     * Task properties to iCal Vtodo properties
     *
     * @param TaskDto $taskDto
     * @param IcalVtodo $iCalVtodo
     * @return array
     * @throws Exception
     */
    public static function processToIcal( TaskDto $taskDto, IcalVtodo $iCalVtodo  ) : array
    {
        parent::groupEventTaskProcessToIcal( $taskDto, $iCalVtodo );
        [ $iCalVtimezones, ] = parent::eventTaskProcessToIcal( $taskDto, $iCalVtodo );

        $isDurationSet = $taskDto->isEstimatedDurationSet();
        $isDueSet      = $taskDto->isDueSet();
        $dueParams  = [];
        switch( true ) {
            case ( ! $isDueSet && ! $isDurationSet ) :
                break;
            case ( ! $isDueSet && $isDurationSet ) :
                $iCalVtodo->setDuration( $taskDto->getEstimatedDuration());
                break;
            case $isDurationSet :
                // if due AND duration set, set duration as a due x-param
                $dueParams[self::setXPrefix( self::ESTIMATEDDURATION )] =
                    $taskDto->getEstimatedDuration();
                // fall through
            default : // only due set
                if( $taskDto->isShowWithoutTimeSet() && $taskDto->getShowWithoutTime()) {
                    $dueParams[IcalVtodo::VALUE] = IcalVtodo::DATE;
                }
                $tzid = $taskDto->getTimeZone();
                if( ! empty( $tzid )) {
                    $dueParams[IcalVtodo::VALUE] = IcalVtodo::DATE_TIME; // for clarity
                    $dueParams[IcalVtodo::TZID]  = $tzid;
                }
                $iCalVtodo->setDue( $taskDto->getDue(), $dueParams );
                break;
        } // end switch
        if( $taskDto->isPercentCompleteSet()) {
            $iCalVtodo->setPercentcomplete( $taskDto->getPercentComplete());
        }
        // link : iCal VTODO status allowed values
        static $vTodoStatusAllowed = [
            IcalVtodo::NEEDS_ACTION,
            IcalVtodo::COMPLETED,
            IcalVtodo::IN_PROCESS,
            IcalVtodo::CANCELLED
        ];
        if( $taskDto->isProgressSet()) {
            $status = strtoupper( $taskDto->getProgress());
            if( in_array( $status, $vTodoStatusAllowed, true )) {
                $iCalVtodo->setStatus( $taskDto->getProgress() );
            }
            else {
                $iCalVtodo->setXprop( self::setXPrefix( self::PROGRESS ), $status);
            }
            if( $taskDto->isProgressUpdatedSet()) {
                $iCalVtodo->setXprop(
                    self::setXPrefix( self::PROGRESSUPDATED ),
                    $taskDto->getProgressUpdated()
                );
            }
        } // end if
        return $iCalVtimezones;
    }

    /**
     * Ical iCal Vevent properties to Event properties
     *
     * @param IcalComponent|IcalVtodo $icalVtodo
     * @param IcalVtimezone[] $iCalVtimezones
     * @return TaskDto
     * @throws Exception
     */
    public static function processFromIcal(
        IcalComponent|IcalVtodo $icalVtodo,
        array $iCalVtimezones
    ) : TaskDto
    {
        $taskDto = new TaskDto();
        parent::groupEventTaskProcessFromIcal( $icalVtodo, $taskDto  );
        // $startDateTime =
        parent::eventTaskProcessFromIcal( $icalVtodo, $taskDto, $iCalVtimezones );
        if( $icalVtodo->isDueSet()) {
            $due = $icalVtodo->getDue( true );
            $taskDto->setDue( $due->getValue());
            if( $due->hasParamKey( IcalVtodo::TZID ) && empty( $taskDto->getTimeZone())) {
                $taskDto->setTimeZone( $due->getParams( IcalVtodo::TZID ));
            }
            $estDurKey = self::setXPrefix( self::ESTIMATEDDURATION );
            if( $due->hasParamKey( $estDurKey )) {
                // if due AND duration set, duration is set as a due x-param
                $taskDto->setEstimatedDuration( $due->getParams( $estDurKey ));
            }
        } // end if
        elseif( $icalVtodo->isDurationSet()) {
            $taskDto->setEstimatedDuration( $icalVtodo->getDuration());
        }
        if( $icalVtodo->isPercentCompleteSet()) {
            $taskDto->setPercentComplete( $icalVtodo->getPercentComplete());
        }
        $statusKey = self::setXPrefix( self::PROGRESS );
        $status = match( true ) {
            $icalVtodo->isStatusSet() => $icalVtodo->getStatus(),
            $icalVtodo->isXpropSet( $statusKey ) => $icalVtodo->getXprop( $statusKey )[1],
            default               => null,
        };
        if( null !== $status ) {
            $taskDto->setProgress( $status );
            $key = self::setXPrefix( self::PROGRESSUPDATED );
            if( $icalVtodo->isXpropSet( $key )) {
                $taskDto->setProgressUpdated( $icalVtodo->getXprop( $key )[1] );
            }
        }
        return $taskDto;
    }
}
