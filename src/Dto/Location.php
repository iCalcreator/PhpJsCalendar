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
namespace Kigkonsult\PhpJsCalendar\Dto;

use Kigkonsult\PhpJsCalendar\Dto\Traits\DescriptionTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\LinksTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\NameTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\RelativeToTrait;

final class Location extends BaseDto
{
    use NameTrait;

    use DescriptionTrait;

    /**
     * A set of one or more location types that describe this location, optional
     *
     * All types MUST be from the "Location Types Registry" [LOCATIONTYPES], as defined in [RFC4589].
     * The set is represented as a map, with the keys being the location types.
     * The value for each key in the map MUST be true.
     *
     * @var mixed[]  String[Boolean]
     */
    private array $locationTypes = [];

    /**
     * Specifies the relation between this location and the time of the JSCalendar object
     *
     * This is primarily to allow events representing travel to specify the location of departure (at the
     *  start of the event) and location of arrival (at the end); this is particularly important if these
     * locations are in different time zones, as a client may wish to highlight this information for the user.
     *
     * "start":  The event/task described by this JSCalendar object occurs at this location at the time the event/task starts.
     * "end":    The event/task described by this JSCalendar object occurs at this location at the time the event/task ends.
     * OR another value registered in the IANA "JSCalendar Enum Values" registry
     * OR a vendor-specific value
     *
     * Optional, NO default (i.e. null)
     */
    use RelativeToTrait;

    /**
     * @var null|string
     */
    public static null|string $relativeToDefault = null;


    /**
     * A "geo:" URI [RFC5870] for the location, optional
     *
     * @var string|null
     */
    private ? string $coordinates = null;

    /**
     * A time zone for this location, optional
     *
     * @var string|null TimeZoneId
     */
    private ? string $timeZone = null;

    use LinksTrait;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::LOCATION;
    }

    /**
     * @return mixed[]
     */
    public function getLocationTypes() : array
    {
        return $this->locationTypes;
    }

    /**
     * @return int
     */
    public function getLocationTypesCount() : int
    {
        return count( $this->locationTypes );
    }

    /**
     * @param string $locationType
     * @param null|bool $bool default true
     * @return static
     */
    public function addLocationType( string $locationType, ? bool $bool = true ) : static
    {
        $this->locationTypes[$locationType] = $bool;
        return $this;
    }

    /**
     * @param array $locationTypes  String[Boolean] or String[]
     * @return static
     */
    public function setLocationTypes( array $locationTypes ) : static
    {
        foreach( $locationTypes as $key => $value ) {
            if( is_string( $key ) && ! is_numeric( $key ) && is_bool( $value )) {
                $this->addLocationType( $key, $value );
            }
            else {
                $this->addLocationType( $value );
            }
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoordinates() : ?string
    {
        return $this->coordinates;
    }

    /**
     * Return bool true if coordinates is not null
     *
     * @return bool
     */
    public function isCoordinatesSet() : bool
    {
        return ( null !== $this->coordinates );
    }

    /**
     * @param null|string $coordinates
     * @return static
     */
    public function setCoordinates( ? string $coordinates ) : static
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @return $this
     */
    public function setLatLongCoordinates( float $latitude, float $longitude ) : static
    {
        static $GEO        = 'geo:';
        static $geoLatFmt  = '%09.6f';
        static $geoLongFmt = ',%8.6f';
        $this->coordinates = $GEO .
            self::geo2str2( $latitude, $geoLatFmt ) .
            self::geo2str2( $longitude, $geoLongFmt );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimeZone() : ?string
    {
        return $this->timeZone;
    }

    /**
     * Return bool true if timeZone is not null
     *
     * @return bool
     */
    public function isTimeZoneSet() : bool
    {
        return ( null !== $this->timeZone );
    }

    /**
     * @param null|string $timeZone
     * @return static
     */
    public function setTimeZone( ? string $timeZone ) : static
    {
        $this->timeZone = $timeZone;
        return $this;
    }

    /** class static methods */

    /**
     * Return formatted latitude/longitude
     *
     * @param float  $ll
     * @param string $format
     * @return string
     */
    private static function geo2str2( float $ll, string $format ) : string
    {
        static $PLUS  = '+';
        static $MINUS = '-';
        static $SP0   = '';
        static $ZERO  = '0';
        static $DOT   = '.';
        $sign = match ( true ) {
            ( 0.0 < $ll ) => $PLUS,
            ( 0.0 > $ll ) => $MINUS,
            default       => $SP0,
        };
        return rtrim( rtrim( $sign . sprintf( $format, abs( $ll )), $ZERO ), $DOT );
    }
}
