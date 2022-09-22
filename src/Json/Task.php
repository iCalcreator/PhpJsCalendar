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
namespace Kigkonsult\PhpJsCalendar\Json;

use Kigkonsult\PhpJsCalendar\Dto\Task as Dto;
use Exception;

class Task extends BaseEventTask
{
    /**
     * Parse json array to populate new Event
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();

        parent::groupEventTaskParse( $jsonArray, $dto );
        parent::eventTaskParse( $jsonArray, $dto );

        if( isset( $jsonArray[self::DUE] )) {
            $dto->setDue( $jsonArray[self::DUE] );
        }
        if( isset( $jsonArray[self::ESTIMATEDDURATION] )) {
            $dto->setEstimatedDuration( $jsonArray[self::ESTIMATEDDURATION] );
        }
        if( isset( $jsonArray[self::PROGRESS] )) {
            $dto->setProgress( $jsonArray[self::PROGRESS] );
            if( isset( $jsonArray[self::PROGRESSUPDATED] )) {
                $dto->setProgressUpdated( $jsonArray[self::PROGRESSUPDATED] );
            }
        }
        if( isset( $jsonArray[self::PERCENTCOMPLETE] )) {
            $dto->setPercentComplete((int) $jsonArray[self::PERCENTCOMPLETE] );
        }
        return $dto;
    }

    /**
     * Write Task Dto properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return array
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];

        parent::groupEventTaskWrite( $dto, $jsonArray );
        parent::eventTaskWrite( $dto, $jsonArray );

        if( $dto->isDueSet()) {
            $jsonArray[self::DUE] = $dto->getDue();
        }

        if( $dto->isEstimatedDurationSet()) {
            $jsonArray[self::ESTIMATEDDURATION] = $dto->getEstimatedDuration();
        }

        if( $dto->isProgressSet()) {
            $jsonArray[self::PROGRESS] = $dto->getProgress();
            if( $dto->isProgressUpdatedSet()) {
                $jsonArray[self::PROGRESSUPDATED] = $dto->getProgressUpdated();
            }
        }
        if( $dto->isPercentCompleteSet()) {
            $jsonArray[self::PERCENTCOMPLETE] = $dto->getPercentComplete();
        }

        return self::orderElements( Dto::$ElementOrder, $jsonArray );
    }
}
