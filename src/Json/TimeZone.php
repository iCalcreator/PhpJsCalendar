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
use Kigkonsult\PhpJsCalendar\Dto\TimeZone as Dto;

class TimeZone extends BaseJson
{
    /**
     * Parse json array to populate new TimeZone
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::TZID] )) {
            $dto->setTzId( $jsonArray[self::TZID] );
        }
        if( isset( $jsonArray[self::UPDATED] )) {
            $dto->setUpdated( $jsonArray[self::UPDATED] );
        }
        if( isset( $jsonArray[self::URL] )) {
            $dto->setUrl( $jsonArray[self::URL] );
        }
        if( isset( $jsonArray[self::VALIDUNTIL] )) {
            $dto->setValidUntil( $jsonArray[self::VALIDUNTIL] );
        }
        if( isset( $jsonArray[self::ALIASES] )) {
            foreach( $jsonArray[self::ALIASES] as $alias => $bool ) {
                $dto->addAlias( $alias, self::jsonBool2Php( $bool ));
            }
        }
        if( isset( $jsonArray[self::STANDARD] )) {
            foreach( $jsonArray[self::STANDARD] as $standard ) {
                $dto->addStandard( TimeZoneRule::parse( $standard ));
            }
        }
        if( isset( $jsonArray[self::DAYLIGHT] )) {
            foreach( $jsonArray[self::DAYLIGHT] as $daylght ) {
                $dto->addDaylight( TimeZoneRule::parse( $daylght ));
            }
        }
        return $dto;
    }

    /**
     * Write TimeZone Dto properties to json array
     *
     * @param Dto $dto
     * @return array
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];

        if( $dto->isTzIdSet()) {
            $jsonArray[self::TZID] = $dto->getTzId();
        }
        if( $dto->isUpdatedSet()) {
            $jsonArray[self::UPDATED] = $dto->getUpdated();
        }

        if( $dto->isUrlSet()) {
            $jsonArray[self::URL] = $dto->getUrl();
        }

        if( $dto->isValidUntilSet()) {
            $jsonArray[self::VALIDUNTIL] = $dto->getValidUntil();
        }

        // ara of "String[Boolean]"
        if( ! empty( $dto->getAliasesCount())) {
            foreach( $dto->getAliases() as $alias => $bool ) {
                $jsonArray[self::ALIASES][$alias] = $bool;
            }
        }
        // array of "TimeZoneRule[]"
        if( ! empty( $dto->getStandardCount())) {
            foreach( $dto->getStandard() as $x => $standard ) {
                $jsonArray[self::STANDARD][$x] = (object)TimeZoneRule::write( $standard );
            }
        }
        if( ! empty( $dto->getDaylightCount())) {
            foreach( $dto->getDaylight() as $x => $daylight ) {
                $jsonArray[self::DAYLIGHT][$x] = (object)TimeZoneRule::write( $daylight );
            }
        }

        return $jsonArray;
    }
}
