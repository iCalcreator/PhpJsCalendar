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

use Exception;
use Kigkonsult\PhpJsCalendar\Dto\TimeZoneRule as Dto;
use stdClass;

class TimeZoneRule extends BaseJson
{
    /**
     * Parse json array to populate new TimeZoneRule
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::START] )) {
            $dto->setStart( $jsonArray[self::START] );
        }
        if( isset( $jsonArray[self::OFFSETFROM] )) {
            $dto->setOffsetFrom( $jsonArray[self::OFFSETFROM] );
        }
        if( isset( $jsonArray[self::OFFSETTO] )) {
            $dto->setOffsetTo( $jsonArray[self::OFFSETTO] );
        }
        if( isset( $jsonArray[self::RECURRENCERULES] )) {
            foreach( $jsonArray[self::RECURRENCERULES] as $recurrenceRule ) {
                $dto->addRecurrenceRule( RecurrenceRule::parse( $recurrenceRule ));
            }
        }
        if( isset( $jsonArray[self::RECURRENCEOVERRIDES] )) {
            foreach( $jsonArray[self::RECURRENCEOVERRIDES] as $localDateTime => $patchObject) {
                $dto->addRecurrenceOverride( $localDateTime, PatchObject::parse( $patchObject ));
            }
        }
        if( isset( $jsonArray[self::NAMES] )) {
            foreach( $jsonArray[self::NAMES] as $name => $bool ) {
                $dto->addName( $name, self::jsonBool2Php( $bool ));
            }
        }
        if( isset( $jsonArray[self::COMMENTS] )) {
            foreach( $jsonArray[self::COMMENTS] as $comment ) {
                $dto->addComment( $comment );
            }
        }
        return $dto;
    }
    /**
     * Parse json array to populate new TimeZoneRule
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return mixed[]
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];

        if( $dto->isStartSet()) {
            $jsonArray[self::START] = $dto->getStart();
        }

        if( $dto->isOffsetFromSet()) {
            $jsonArray[self::OFFSETFROM] = $dto->getOffsetFrom();
        }
        
        if( $dto->isOffsetToSet()) {
            $jsonArray[self::OFFSETTO] = $dto->getOffsetTo();
        }
        // array of "RecurrenceRule[]"
        if( ! empty( $dto->getRecurrenceRulesCount())) {
            foreach( $dto->getRecurrenceRules() as $x => $recurrenceRule ) {
                $jsonArray[self::RECURRENCERULES][$x] = (object)RecurrenceRule::write( $recurrenceRule );
            }
        }
        // array of "LocalDateTime[PatchObject]"
        if( ! empty( $dto->getRecurrenceOverridesCount())) {
            $jsonSource[self::RECURRENCEOVERRIDES] = new stdClass();
            foreach( $dto->getRecurrenceOverrides() as $localDateTime => $patchObject ) {
                $jsonSource[self::RECURRENCEOVERRIDES]->{$localDateTime} = (object) PatchObject::write( $patchObject );
            }
        }
        // array of "String[Boolean]"
        if( ! empty( $dto->getNamesCount())) {
            foreach( $dto->getNames() as $name => $bool ) {
                $jsonSource[self::NAMES][$name] = $bool;
            }
        }
        // array of "String[]"
        if( ! empty( $dto->getCommentsCount())) {
            foreach( $dto->getComments() as $x => $comment ) {
                $jsonSource[self::COMMENTS][$x] = $comment;
            }
        }
        return $jsonArray;
    }
}
