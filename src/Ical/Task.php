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
use Kigkonsult\Icalcreator\Vtimezone;
use Kigkonsult\Icalcreator\Vtodo;
use Kigkonsult\PhpJsCalendar\Dto\Task as TaskDto;

class Task extends BaseEventTask
{
    /**
     * Ical Task properties to iCal Vtodo properties
     *
     * @param TaskDto $taskDto
     * @param Vtodo $vtodo
     * @return array
     * @throws Exception
     */
    public static function processTo( TaskDto $taskDto, Vtodo $vtodo  ) : array
    {
        parent::groupEventTaskProcessTo( $taskDto, $vtodo );
        [ $vtimezones, $startDateTime ] = parent::eventTaskProcessTo( $taskDto, $vtodo );

        $isDurationSet = $taskDto->isEstimatedDurationSet();
        $isDueSet      = $taskDto->isDueSet();
        if( ! $isDurationSet && ! $isDueSet ) {
            return $vtimezones;
        }
        $dueParams  = [];
        switch( true ) {
            case ( ! $isDueSet && $isDurationSet ) :
                $vtodo->setDuration( $taskDto->getEstimatedDuration());
                break;
            case $isDurationSet :
                // if due AND duration set, set duration as a due x-param
                $dueParams[self::setXPrefix( self::ESTIMATEDDURATION )] = $taskDto->getEstimatedDuration();
                // fall through
            default :
                if( $taskDto->isShowWithoutTimeSet() && $taskDto->getShowWithoutTime()) {
                    $dueParams[Vtodo::VALUE] = Vtodo::DATE;
                }
                $tzid = $taskDto->getTimeZone();
                if( ! empty( $tzid )) {
                    $dueParams[Vtodo::VALUE] = Vtodo::DATE_TIME; // for clarity
                    $dueParams[Vtodo::TZID]  = $tzid;
                }
                $vtodo->setDue( $taskDto->getDue(), $dueParams );
                break;
        } // end switch

        if( $taskDto->isPercentCompleteSet()) {
            $vtodo->setPercentcomplete( $taskDto->getPercentComplete());
        }
        // link : iCal VTODO status allowed values
        static $vTodoStatusAllowed = [ 'NEEDS-ACTION', 'COMPLETED','IN-PROCESS','CANCELLED' ];
        if( $taskDto->isProgressSet()) {
            $status = strtoupper( $taskDto->getProgress());
            if( in_array( $status, $vTodoStatusAllowed, true )) {
                $vtodo->setStatus( $taskDto->getProgress() );
            }
            else {
                $vtodo->setXprop( self::setXPrefix( self::PROGRESS ), $status);
            }
            if( $taskDto->isProgressUpdatedSet()) {
                $vtodo->setXprop( self::setXPrefix( self::PROGRESSUPDATED ), $taskDto->getProgressUpdated() );
            }
        }

        return $vtimezones;
    }

    /**
     * Ical iCal Vevent properties to Event properties
     *
     * @param Vtodo $vtodo
     * @param Vtimezone[] $vtimezones
     * @return TaskDto
     * @throws Exception
     */
    public static function processFrom( Vtodo $vtodo, array $vtimezones ) : TaskDto
    {
        $taskDto = new TaskDto();
        parent::groupEventTaskProcessFrom( $vtodo, $taskDto  );
        // $startDateTime =
        parent::eventTaskProcessFrom( $vtodo, $taskDto, $vtimezones );

        if( $vtodo->isDueSet()) {
            $due = $vtodo->getDue( true );
            $taskDto->setDue( $due->value );
            if( $due->hasParamKey( Vtodo::TZID ) && empty( $taskDto->getTimeZone())) {
                $taskDto->setTimeZone( $due->getParams( Vtodo::TZID ));
            }
            $estDurKey = self::setXPrefix( self::ESTIMATEDDURATION );
            if( $due->hasParamKey( $estDurKey )) {
                // if due AND duration set, duration is set as a due x-param
                $taskDto->setEstimatedDuration( $due->getParams( $estDurKey ));
            }
        }
        elseif( $vtodo->isDurationSet()) {
            $taskDto->setEstimatedDuration( $vtodo->getDuration());
        }

        if( $vtodo->isPercentCompleteSet()) {
            $taskDto->setPercentComplete( $vtodo->getPercentComplete());
        }

        $statusKey = self::setXPrefix( self::PROGRESS );
        $status = match( true ) {
            $vtodo->isStatusSet() => $vtodo->getStatus(),
            $vtodo->isXpropSet( $statusKey ) => $vtodo->getXprop( $statusKey )[1],
            default               => null,
        };
        if( null !== $status ) {
            $taskDto->setProgress( $status );
            $key = self::setXPrefix( self::PROGRESSUPDATED );
            if( $vtodo->isXpropSet( $key )) {
                $taskDto->setProgressUpdated( $vtodo->getXprop( $key )[1] );
            }
        }
        return $taskDto;
    }
}
