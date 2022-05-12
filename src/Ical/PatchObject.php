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
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\PhpJsCalendar\Dto\PatchObject as PatchObjectDto;
use ReflectionClass;

class PatchObject extends BaseEventTask
{
    /**
     * @var string
     */
    private static string $keySeparator = '|';

    /**
     * @var string
     */
    private static string $SLASH = '/';

    /**
     * PatchObjectDto properties (all but arrays) to iCal x-parameters
     *
     * All keys will be in upper case
     *
     * @param PatchObjectDto $patchObjectDto
     * @return mixed[]
     */
    public static function processTo( PatchObjectDto $patchObjectDto  ) : array
    {
        $params = [];
        foreach( $patchObjectDto->getPatches() as $pKey => $pValue ) {
            if( str_contains( $pKey, self::$SLASH )) {
                $keyParts    = explode( self::$SLASH, $pKey );
                $keyParts[0] = self::setXPrefix( $keyParts[0] ); // strtoupper
                $pKey        = implode( self::$SLASH, $keyParts );
            }
            else {
                $pKey = self::setXPrefix( $pKey );
            }
            $params[$pKey] = $pValue;
        } // end foreach
        $output = [];
        self::patchArrayToParams( $params, $output );
        return $output;
    }

    /**
     * @param mixed[] $patchArray
     * @param string[] $params
     * @param null|string $prefixKey
     */
    private static function patchArrayToParams(
        array $patchArray,
        array & $params,
        ? string $prefixKey = null
    ) : void
    {
        foreach( $patchArray as $key => $value ) {
            if( ! empty( $prefixKey )) {
                $key = $prefixKey . self::$keySeparator . $key;
            }
            if( is_array( $value )) {
                self::patchArrayToParams( $value, $params, $key );
                continue;
            }
            if( is_bool( $value )) {
                $value = $value ? Vcalendar::TRUE : Vcalendar::FALSE;
            }
            $params[$key] = $value;
        }
    }


    /**
     * @var array
     */
    private static array $elementNames = [];

    /**
     * Class singleton method
     *
     * @return self
     */
    public static function singleton() : self
    {
        static $instance = null;
        if( null === $instance ) {
            $instance = new self();
            $reflect  = new ReflectionClass( get_class( $instance ));
            foreach( array_values( $reflect->getConstants()) as $const ) {
                self::$elementNames[strtolower( $const )] = $const;
            }
        }
        return $instance;
    }

    /**
     * Return PatchObject populated from iCal x-parameters
     *
     * @param string[] $params
     * @return PatchObjectDto
     * @throws Exception
     */
    public function processFrom( array $params ) : PatchObjectDto
    {
        $patches = [];
        foreach( $params as $key => $value ) {
            if( ! self::isXprefixed( $key )) {
                continue;
            }
            if( ! str_contains( $key, self::$keySeparator )) {
                $key           = self::rectifyKey( self::unsetXPrefix( $key ));
                $patches[$key] = self::getOptBoolValue( $value );
                continue;
            }
            $keys     = explode( self::$keySeparator, $key );
            $keys[0]  = self::unsetXPrefix( $keys[0] );
            $lastIx   = count( $keys );
            $currIx   = 0;
            $tmpPtchs = & $patches;
            foreach( $keys as $keyPart ) {
                ++$currIx;
                $keyPart = self::rectifyKey( $keyPart );
                if( ! isset( $tmpPtchs[$keyPart] )) {
                    $tmpPtchs[$keyPart] = [];
                }
                $tmpPtchs = & $tmpPtchs[$keyPart];
                if( $lastIx === $currIx ) {
                    $tmpPtchs = self::getOptBoolValue( $value );
                }
            } // end foreach
        } // end foreach
        return PatchObjectDto::factory( $patches );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private static function getOptBoolValue( mixed $value ) : mixed
    {
        return match ( true ) {
            ( ! is_string( $value )) => $value,
            ( 0 === strcasecmp( $value, Vcalendar::TRUE ))  => true,
            ( 0 === strcasecmp( $value, Vcalendar::FALSE )) => false,
            default => $value,
        };
    }

    /**
     * Return (PatchObject pointer) key with more correct key name parts OR in lowercase
     *
     * @param string $key
     * @return string
     */
    private static function rectifyKey( string $key ) : string
    {
        $key = strtolower( $key );
        if( str_contains( $key, self::$SLASH )) {
            $keyParts = explode( self::$SLASH, $key );
            foreach( $keyParts as $x => $keyPart ) {
                if( isset( self::$elementNames[$keyPart] ) ) {
                    $keyParts[$x] = self::$elementNames[$keyPart];
                }
            }
            $key = implode( self::$SLASH, $keyParts );
        }
        return $key;
    }
}
