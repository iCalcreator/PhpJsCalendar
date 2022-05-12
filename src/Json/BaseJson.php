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

abstract class BaseJson implements JsonInterface
{
    /**
     * Return bool true if objectArray has key '@type' and objectArray[@type] == objectType
     *
     * @param mixed[] $objectArray
     * @param string $objectType
     * @return bool
     */
    protected static function hasObjectType( array $objectArray, string $objectType ) : bool
    {
        return ( isset( $objectArray[self::OBJECTTYPE] ) &&
            ( $objectType === $objectArray[self::OBJECTTYPE] ));
    }

    /**
     * Transform Json bool to PHP bool
     *
     * @param mixed $value
     * @return bool
     */
    protected static function jsonBool2Php( mixed $value ) : bool
    {
        static $true  = 'true';
        static $false = 'false';
        return match ( true ) {
            is_bool( $value )  => $value,
            ( 0 === strcasecmp( $true, $value ))  => true,
            ( 0 === strcasecmp( $false, $value )) => false,
            default            => (bool) $value,
        };
    }

    /**
     * Sort array after master template
     *
     * @param string[] $master
     * @param mixed[]  $arrayToSort
     * @return mixed[]
     */
    protected static function orderElements( array $master, array $arrayToSort ) : array
    {
        $sortedArray = [];
        foreach( $master as $key ) {
            if( isset( $arrayToSort[$key] )) {
                $sortedArray[$key] = $arrayToSort[$key];
            }
        } // end foreach
        return $sortedArray;
    }
}
