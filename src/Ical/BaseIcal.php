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

abstract class BaseIcal implements IcalInterface
{
    /**
     * @var string
     */
    protected static string $xPrefix = 'X-';

    /**
     * @var string  item separator in iCal lists
     */
    protected static string $itemSeparator = ',';

    /**
     * @var string  another item separator
     */
    protected static string $SQ = ';';

    /**
     * @var string  x-param key separator
     */
    protected static string $D = '-';

    /**
     * Return bool true if value is X-Prefixed
     *
     * @param string $value
     * @return bool
     */
    public static function isXprefixed( string $value ) : bool
    {
        return str_starts_with( $value, self::$xPrefix );
    }

    /**
     * Prefix ical value parameters (key) as x-parameters
     *
     * @param string[] $params
     * @return string[]
     */
    public static function xPrefixKeys( array $params ) : array
    {
        $output = [];
        foreach( $params as $key => $value ) {
            $output[self::setXPrefix( $key )]= $value;
        }
        return $output;
    }

    /**
     * Return (iCal) X-prefixed string in upper case
     *
     * @param string $value
     * @return string
     */
    public static function setXPrefix( string $value ) : string
    {
        return self::$xPrefix . strtoupper( $value );
    }

    /**
     * Remove opt. leading x-prefix from ical x-parameter keys
     *
     * @param string[] $params
     * @return string[]
     */
    public static function unXPrefixKeys( array $params ) : array
    {
        $output = [];
        foreach( $params as $key => $value ) {
            $output[self::unsetXPrefix( $key )]= $value;
        }
        return $output;
    }

    /**
     * Remove leading x-prefix from (strtolower) value
     *
     * @param string $value
     * @return string
     */
    public static function unsetXPrefix( string $value ) : string
    {
        return strtolower( self::isXprefixed( $value ) ? substr( $value, 2 ) : $value );
    }
}
