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
use Kigkonsult\Icalcreator\Vlocation;
use Kigkonsult\PhpJsCalendar\Dto\VirtualLocation as VirtualLocationDto;

class VirtualLocation extends BaseIcal
{
    /**
     * VirtualLocation properties to Vlocation
     *
     * @param int|string $id
     * @param VirtualLocationDto $virtualLocationDto
     * @return Vlocation
     * @throws Exception
     */
    public static function processTo( int|string $id, VirtualLocationDto $virtualLocationDto  ) : Vlocation
    {
        $vlocation = new Vlocation();
        if( $id != (int) $id ) {  // note !=
            $vlocation->setUid( $id );
        }
        else {
            $vlocation->setXprop( self::setXPrefix( self::UID ), $id );
        }
        // mark as virtualLocation !!
        $vlocation->setXprop( self::setXPrefix( self::VIRTUALLOCATION ), 1 );

        if( $virtualLocationDto->isNameSet()) {
            $vlocation->setName( $virtualLocationDto->getName());
        }

        if( $virtualLocationDto->isDescriptionSet()) {
            $vlocation->setDescription( $virtualLocationDto->getDescription());
        }

        if( $virtualLocationDto->isUriSet()) {
            $vlocation->setUrl( $virtualLocationDto->getUri());
        }

        // array of "String[Boolean]"
        if( ! empty( $virtualLocationDto->getFeaturesCount())) {
            foreach( array_keys( $virtualLocationDto->getFeatures()) as $feature ) {
                $vlocation->setXprop( self::setXPrefix( $feature ), $feature );
            }
        }
        return $vlocation;
    }

    /**
     * Ical Vlocation properties to VirtualLocation
     *
     * @param Vlocation $vlocation has X-prop self::VIRTUALLOCATION
     * @return mixed[]   [ id, VirtualLocation ]
     * @throws Exception
     */
    public static function processFrom( Vlocation $vlocation ) : array
    {
        $id = ( false !== ( $value = $vlocation->getXprop( self::setXPrefix( self::UID ))))
            ? $value[1]
            : $vlocation->getUid();

        $virtualLocationDto = new VirtualLocationDto();
        if( $vlocation->isNameSet()) {
            $virtualLocationDto->setName( $vlocation->getName());
        }

        if( $vlocation->isDescriptionSet()) {
            $virtualLocationDto->setDescription( $vlocation->getDescription());
        }

        if( $vlocation->isUrlSet()) {
            $virtualLocationDto->setUri( $vlocation->getUrl());
        }

        while( false !== ( $value = $vlocation->getXprop())) {
            if( 0 === strcasecmp( self::unsetXPrefix( $value[0] ), $value[1] )) {
                $virtualLocationDto->addFeature( $value[1] );
            }
        } // end while

        return [ $id, $virtualLocationDto ];
    }
}
