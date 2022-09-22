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
use Kigkonsult\Icalcreator\CalendarComponent as IcalComponent;
use Kigkonsult\Icalcreator\Pc;
use Kigkonsult\Icalcreator\Vlocation         as IcalVlocation;
use Kigkonsult\PhpJsCalendar\Dto\Location    as LocationDto;

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
     * @return IcalVlocation
     * @throws Exception
     */
    public static function processToIcal(
        int|string $id,
        LocationDto $locationDto,
        ? string $locale = null
    ) : IcalVlocation
    {
        $vLocation = new IcalVlocation();
        $vLocation->setUid( $id );
//      $vlocation->setXprop( Vlocation::X_VLOCATIONID, $id );

        $geoUrlParams   = empty( $locale ) ? [] : [ IcalVlocation::LANGUAGE => $locale ];

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
            $value      = $locationDto->getCoordinates();
            $hasPropUrl = method_exists( $vLocation, $urlSetMethod );
            if( str_starts_with( $value, self::$geoPrefix )) {
                $hasPropGeo   = method_exists( $vLocation, $geoSetMethod );
                $geoUrlParams = [ self::$X_GEOURLKEY => $value ];
                if( $hasPropGeo ) {
                    $value2 = substr( $value, 4 );
                    if( str_contains( $value2, self::$SQ ) ) {
                        $value2 = substr( $value2, 0, strpos( $value2, self::$SQ ));
                    }
                    [ $lat, $long ] = explode( self::$itemSeparator, $value2 );
                    $vLocation->setGeo( $lat, $long, $geoUrlParams );
                }
                elseif( $hasPropUrl ) {
                    $vLocation->setUrl( $value, $geoUrlParams );
                }
            } // end if
        } // end if

        // array of "Id[Link]"   to iCal IMAGE/STRUCTURED_DATA
        if( ! empty( $locationDto->getLinksCount())) {
            Link::processLinksToIcal( $locationDto->getLinks(), $vLocation );
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
     * @return array   [ locationValue, locationParams ]
     */
    public static function processToIcalLocationArr(
        int|string $id,
        LocationDto $locationDto,
        ? string $locale = null
    ) : array
    {
        $locationValue  = null;
        $locationParams = [ IcalVlocation::X_VLOCATIONID => $id ];

        if( ! empty( $locale )) {
            $locationParams[IcalVlocation::LANGUAGE] = $locale;
        }
        if( $locationDto->isNameSet()) {
            $locationValue = $locationDto->getName();
        }

        // array of "String[Boolean]"  ONLY one accepted BUT multiple comma separated..
        if( ! empty( $locationDto->getLocationTypesCount())) {
            $locationParams[IcalVlocation::X_LOCATION_TYPE] =
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
     * @param IcalComponent|IcalVlocation $vlocation has NO X-prop self::VIRTUALLOCATION (i.e. is Location)
     * @return array     [ id, Dto ]
     * @throws Exception
     */
    public static function processFromIcal( IcalComponent|IcalVlocation $vlocation ) : array
    {
        $id = ( false !== ( $value = $vlocation->getXprop( IcalVlocation::X_VLOCATIONID )))
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

        // opt GEO
        static $isGeoSet     = 'isGeoSet';
        static $geoGetMethod = 'getGeo';
        $urlGeoValues   = [];
        if( self::existsAndIsset( $vlocation, $isGeoSet )) {
            $geoCnt = $vlocation->{$geoGetMethod}( true );
            if( $geoCnt->hasParamKey( self::$X_GEOURLKEY )) {
                $urlValue       = $geoCnt->getParams( self::$X_GEOURLKEY );
                $locationDto->setCoordinates( $urlValue );
                $urlGeoValues[] = $urlValue;
            }
            else {
                $latLongs = $geoCnt->getValue();
                $locationDto->setLatLongCoordinates(
                    $latLongs[IcalVlocation::LATITUDE],
                    $latLongs[IcalVlocation::LATITUDE]
                );
                $urlGeoValues[] = self::$geoPrefix .
                    $latLongs[IcalVlocation::LATITUDE] .
                    self::$itemSeparator .
                    $latLongs[IcalVlocation::LATITUDE];
            }
        } // end if
        // opt URL (may also contain GEO, also, opt, as xProp below)
        static $isUrlSet     = 'isUrlSet';
        static $urlGetMethod = 'getUrl';
        if( self::existsAndIsset( $vlocation, $isUrlSet )) {
            $value        = $vlocation->{$urlGetMethod}( true );
            $hasGeoPrefix = ( 0 === stripos( $value->getValue(), self::$geoPrefix ));
            $hasParamsKey = $value->hasParamKey( self::$X_GEOURLKEY );
            $urlValue     = $hasGeoPrefix
                ? self::$geoPrefix . substr( $value->getValue(), 4 )
                : $value->getValue(); // NO geo:Url
            switch( true ) {
                case ( ! $locationDto->isCoordinatesSet() && $hasParamsKey ) : // param before value
                    $urlValue = $value->getParams( self::$X_GEOURLKEY );
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
        Link::processLinksFromIcal( $vlocation, $locationDto );

        $timezoneKey = self::setXPrefix( self::TIMEzONE );
        if( $vlocation->isXpropSet(( $timezoneKey ))) {
            $locationDto->setTimeZone( $vlocation->getXprop( $timezoneKey )[1]);
        }

        return [ $id, $locationDto ];
    }

    /**
     * Ical property content (value and params) to Location
     *
     * @param Pc $content Ical property content
     * @return array     [ id, Dto ]
     * @throws Exception
     */
    public static function fromIcalLocation( Pc $content ) : array
    {
        $id = $content->hasParamKey( IcalVlocation::X_VLOCATIONID )
            ? $content->getParams( IcalVlocation::X_VLOCATIONID )
            : LocationDto::getNewUid();
        $locationDto = new LocationDto();

        $locationDto->setName( $content->getValue());

        if( $content->hasParamKey( IcalVlocation::X_LOCATION_TYPE )) {
            foreach(
                explode( self::$itemSeparator, $content->getParams( IcalVlocation::X_LOCATION_TYPE ))
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
