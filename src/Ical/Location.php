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
use Kigkonsult\Icalcreator\Pc;
use Kigkonsult\Icalcreator\Util\StringFactory;
use Kigkonsult\Icalcreator\Vlocation;
use Kigkonsult\PhpJsCalendar\Dto\Location as LocationDto;

class Location extends BaseIcal
{
    /**
     * @var string
     */
    private static string $geoPrefix = 'geo:';

    /**
     * @var string
     */
    private static string $X_GEOURLKEY = 'X-GEOURL';

    /**
     * Location properties to iCal Vlocation
     *
     * @param int|string $id
     * @param LocationDto $locationDto
     * @param null|string $locale
     * @return Vlocation
     * @throws Exception
     */
    public static function processTo( int|string $id, LocationDto $locationDto, ? string $locale = null ) : Vlocation
    {
        $vLocation = new Vlocation();
        $vLocation->setUid( $id );
//      $vlocation->setXprop( Vlocation::X_VLOCATIONID, $id );

        $geoUrlParams   = empty( $locale ) ? [] : [ Vlocation::LANGUAGE => $locale ];

        if( $locationDto->isNameSet()) {
            $vLocation->setName( $locationDto->getName(), $geoUrlParams );
        }

        if( $locationDto->isDescriptionSet()) {
            $vLocation->setDescription( $locationDto->getDescription(), $geoUrlParams );
        }

        // array of "String[Boolean]"  ONLY one accepted BUT multiple comma separated..
        if( ! empty( $locationDto->getLocationTypesCount())) {
            $locationTypes = implode( self::$itemSeparator, array_keys( $locationDto->getLocationTypes()));
            $vLocation->setLocationtype( $locationTypes );
        } // end if

        if( $locationDto->isRelativeToSet()) {
            $vLocation->setXprop( self::setXPrefix( self::RELATIVETO ), $locationDto->getRelativeTo());
        }

        // Only coordinates prefixed by 'geo:' accepted
        // Vlocation has (opt?) GEO and, opt, URL (rfc9073/9074)
        if( $locationDto->isCoordinatesSet()) {
            static $urlSetMethod = 'setUrl';
            static $geoSetMethod = 'setGeo';
            $value  = $locationDto->getCoordinates();
            $urlSet = ! method_exists( $vLocation, $urlSetMethod ); // i.e. false if exists
            if( str_starts_with( $value, self::$geoPrefix )) {
                $geoSet = ! method_exists( $vLocation, $geoSetMethod ); // i.e. false if exists
                $geoUrlParams = [ self::$X_GEOURLKEY => $value ];
                if( ! $geoSet ) {
                    $value2 = substr( $value, 4 );
                    if( str_contains( $value2, self::$SQ ) ) {
                        $value2 = StringFactory::before( self::$SQ, $value2 );
                    }
                    [ $lat, $long ] = explode( self::$itemSeparator, $value2 );
                    $vLocation->setGeo( $lat, $long, $geoUrlParams );
                }
                elseif( ! $urlSet ) {
                    $vLocation->setUrl( $value, $geoUrlParams );
                }
            } // end if
        } // end if

        // array of "Id[Link]"   to iCal IMAGE/STRUCTURED_DATA
        if( ! empty( $locationDto->getLinksCount())) {
            Link::processLinksTo( $locationDto->getLinks(), $vLocation );
        }

        // timezone as xProp
        if( $locationDto->isTimeZoneSet()) {
            $vLocation->setXprop( self::setXPrefix( self::TIMEzONE ), $locationDto->getTimeZone());
        }

        return $vLocation;
    }

    /**
     * Location properties to iCal Location value/params
     *
     * @param int|string $id
     * @param LocationDto $locationDto
     * @param null|string $locale
     * @return mixed[]   [ locationValue, locationParams ]
     */
    public static function processToLocationArr( int|string $id, LocationDto $locationDto, ? string $locale = null ) : array
    {
        $locationValue  = null;
        $locationParams = [ Vlocation::X_VLOCATIONID => $id ];

        if( ! empty( $locale )) {
            $locationParams[Vlocation::LANGUAGE] = $locale;
        }
        if( $locationDto->isNameSet()) {
            $locationValue = $locationDto->getName();
        }

        // array of "String[Boolean]"  ONLY one accepted BUT multiple comma separated..
        if( ! empty( $locationDto->getLocationTypesCount())) {
            $locationParams[Vlocation::X_LOCATION_TYPE] =
                implode( self::$itemSeparator, array_keys( $locationDto->getLocationTypes()));
        } // end if

        // Only coordinates prefixed by 'geo:' accepted
        if( $locationDto->isCoordinatesSet()) {
            $value = $locationDto->getCoordinates();
            if( str_starts_with( $value, self::$geoPrefix ) ) {
                $locationParams[self::$X_GEOURLKEY] = $value;
            }
        }

        return [ $locationValue, $locationParams ];
    }

    /**
     * Ical iCal Vlocation to Location
     *
     * @param Vlocation $vlocation has NO X-prop self::VIRTUALLOCATION (i.e. is Location)
     * @return mixed[]     [ id, Dto ]
     * @throws Exception
     */
    public static function processFrom( Vlocation $vlocation ) : array
    {
        $id = ( false !== ( $value = $vlocation->getXprop( Vlocation::X_VLOCATIONID )))
            ? $value[1]
            : $vlocation->getUid();

        $locationDto = new LocationDto();

        if( $vlocation->isNameSet()) {
            $locationDto->setName( $vlocation->getName());
        }

        if( $vlocation->isDescriptionSet()) {
            $locationDto->setDescription( $vlocation->getDescription());
        }

        if( $vlocation->IsLocationtypeSet()) {
            foreach( explode( self::$itemSeparator, $vlocation->getLocationtype()) as $locationType ) {
                $locationDto->addLocationType( $locationType );
            }
        }

        $relativeToKey = self::setXPrefix( self::RELATIVETO );
        if( $vlocation->isXpropSet( $relativeToKey )) {
            $locationDto->setRelativeTo( $vlocation->getXprop( $relativeToKey )[1] );
        }

        // GEO
        static $geoGetMethod = 'getGeo';
        static $geoIsMethod  = 'isGeoSet';
        $urlGeoValues   = [];
        if( method_exists( $vlocation, $geoGetMethod ) && $vlocation->{$geoIsMethod}()) {
            $value = $vlocation->{$geoGetMethod}( true );
            if( $value->hasParamKey( self::$X_GEOURLKEY )) {
                $urlValue       = $value->getParams( self::$X_GEOURLKEY );
                $locationDto->setCoordinates( $urlValue );
                $urlGeoValues[] = $urlValue;
            }
            else {
                $locationDto->setLatLongCoordinates(
                    $value->value[Vlocation::LATITUDE],
                    $value->value[Vlocation::LATITUDE]
                );
                $urlGeoValues[] = self::$geoPrefix .
                    $value->value[Vlocation::LATITUDE] .
                    self::$itemSeparator .
                    $value->value[Vlocation::LATITUDE];
            }
        } // end if
        // opt URL (may also contain GEO, also, opt, as xProp below)
        static $urlGetMethod = 'getUrl';
        static $urlIsMethod  = 'isUrlSet';
        if( method_exists( $vlocation, $urlGetMethod ) && $vlocation->{$urlIsMethod}()) {
            $value        = $vlocation->{$urlGetMethod}( true );
            $hasGeoPrefix = ( 0 === stripos( $value->value, self::$geoPrefix ));
            $hasParamsKey = isset( $value->params[self::$X_GEOURLKEY] );
            $urlValue     = $hasGeoPrefix
                ? self::$geoPrefix . substr( $value->value, 4 )
                : $value->value; // NO geo:Url
            switch( true ) {
                case ( ! $locationDto->isCoordinatesSet() && $hasParamsKey ) : // param before value
                    $urlValue = $value->params[self::$X_GEOURLKEY];
                    if( ! in_array( $urlValue, $urlGeoValues, true )) {
                        $locationDto->setCoordinates( $urlValue );
                        $urlGeoValues[] = $urlValue;
                    }
                    break;
                case in_array( $urlValue, $urlGeoValues, true ) :
                    break;
                case ( ! $locationDto->isCoordinatesSet() && ! $hasParamsKey ) :
                    if( $hasGeoPrefix ) {
                        $locationDto->setCoordinates( $urlValue );
                        $urlGeoValues[]  = $urlValue;
                        break;
                    }
                    // fall through
                default :
                    [ $lid, $link ] = Link::processFrom( $value );
                    $locationDto->addLink( $lid, $link );
                    $urlGeoValues[] = $urlValue;
                    break;
            } // end switch
        } // end if

        // array of "Id[Link]"   from iCal IMAGE/STRUCTURED_DATA
        Link::processLinksFrom( $vlocation, $locationDto );

        $timezoneKey = self::setXPrefix( self::TIMEzONE );
        if( $vlocation->isXpropSet(( $timezoneKey ))) {
            $locationDto->setTimeZone( $vlocation->getXprop( $timezoneKey )[1]);
        }

        return [ $id, $locationDto ];
    }

    /**
     * Ical property content (value and params) to Location
     *
     * @param Pc $content
     * @return mixed[]     [ id, Dto ]
     * @throws Exception
     */
    public static function fromIcaLocation( Pc $content ) : array
    {
        $id = $content->hasParamKey( Vlocation::X_VLOCATIONID )
            ? $content->getParams( Vlocation::X_VLOCATIONID )
            : LocationDto::getNewUid();
        $locationDto = new LocationDto();

        $locationDto->setName( $content->value );

        if( $content->hasParamKey( Vlocation::X_LOCATION_TYPE )) {
            foreach(
                explode( self::$itemSeparator, $content->getParams( Vlocation::X_LOCATION_TYPE ))
                as $locationType ) {
                $locationDto->addLocationType( $locationType );
            }
        } // end if

        $key    = self::setXPrefix( self::COORDINATES );
        if( $content->hasParamKey( $key )) {
            $locationDto->setCoordinates( $content->getParams( $key ));
        }

        return [ $id, $locationDto ];
    }
}
