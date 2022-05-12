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
namespace Kigkonsult\PhpJsCalendar\Dto\Traits;

use DateInterval;

trait DateInterval2StringTrait
{
    /**
     * Return DateInterval as string
     *
     * @param DateInterval $dateInterval
     * @param bool         $showOptSign
     * @return string
     */
    public static function dateInterval2String( DateInterval $dateInterval, ? bool $showOptSign = false ) : string
    {
        static $Y        = 'Y';
        static $T        = 'T';
        static $W        = 'W';
        static $D        = 'D';
        static $H        = 'H';
        static $M        = 'M';
        static $S        = 'S';
        static $PT0H0M0S = 'PT0H0M0S';
        static $s        = 's';
        static $i        = 'i';
        static $h        = 'h';
        static $d        = 'd';
        static $m        = 'm';
        static $y        = 'y';
        static $invert   = 'invert';
        static $P        = 'P';
        static $MINUS    = '-';
        $dateIntervalArr = (array) $dateInterval;
        $result          = $P;
        if( empty( $dateIntervalArr[$y] ) &&
            empty( $dateIntervalArr[$m] ) &&
            empty( $dateIntervalArr[$h] ) &&
            empty( $dateIntervalArr[$i] ) &&
            empty( $dateIntervalArr[$s] ) &&
            ! empty( $dateIntervalArr[$d] ) &&
            ( 0 === ( $dateIntervalArr[$d] % 7 ))) {
            $result .= (int) floor( $dateIntervalArr[$d] / 7 ) . $W;
            return ( $showOptSign && ( 0 < $dateIntervalArr[$invert] )) ? $MINUS . $result : $result;
        }
        if( 0 < $dateIntervalArr[$y] ) {
            $result .= $dateIntervalArr[$y] . $Y;
        }
        if( 0 < $dateIntervalArr[$m] ) {
            $result .= $dateIntervalArr[$m] . $M;
        }
        if( 0 < $dateIntervalArr[$d] ) {
            $result .= $dateIntervalArr[$d] . $D;
        }
        $hourIsSet = ! empty( $dateIntervalArr[$h] );
        $minIsSet  = ! empty( $dateIntervalArr[$i] );
        $secIsSet  = ! empty( $dateIntervalArr[$s] );
        if( ! $hourIsSet && ! $minIsSet && ! $secIsSet ) {
            if( $P === $result ) {
                $result = $PT0H0M0S;
            }
            return ( $showOptSign && ( 0 < $dateIntervalArr[$invert] )) ? $MINUS . $result : $result;
        }
        $result .= $T;
        if( $hourIsSet ) {
            $result .= $dateIntervalArr[$h] . $H;
        }
        if( $minIsSet ) {
            $result .= $dateIntervalArr[$i] . $M;
        }
        if( $secIsSet ) {
            $result .= $dateIntervalArr[$s] . $S;
        }
        return ( $showOptSign && ( 0 < $dateIntervalArr[$invert] ))
            ? $MINUS . $result : $result;
    }
}
