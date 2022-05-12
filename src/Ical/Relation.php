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

use Kigkonsult\Icalcreator\Pc;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\PhpJsCalendar\Dto\Relation as RelationDto;

class Relation extends BaseIcal
{
    /**
     * Ical Relation property to iCal Relatedto (X-)params, return array
     *
     * @param RelationDto $relationDto
     * @return mixed[]   iCal Relatedto params, first one Vcalendar::RELTYPE, the rest x-type => type
     */
    public static function processTo( RelationDto $relationDto  ) : array
    {
        $params = [];
        // array of String[Boolean]
        if( ! empty( $relationDto->getRelationCount())) {
            foreach( array_keys( $relationDto->getRelation()) as $rix => $relation ) {
                if( ! isset( $params[Vcalendar::RELTYPE] )) {
                    $key = Vcalendar::RELTYPE;
                }
                else {
                    $key = self::setXPrefix( $relation ) . $rix;
                }
                $params[$key] = $relation;
            }
        } // end if
        return $params;
    }

    /**
     * Ical Relatedto property to Relation
     *
     * @param Pc $relatedto
     * @return mixed[]   [ id, Relation ]
     */
    public static function processFrom( Pc $relatedto ) : array
    {
        $id          = $relatedto->value;
        $relationDto = new RelationDto();
        $relTypeKey  = strtolower( Vcalendar::RELTYPE );
        foreach( self::unXPrefixKeys( $relatedto->params ) as $pKey => $pValue ) {
            if( $relTypeKey === $pKey ) {
                $relationDto->addRelation( $pValue );
                continue;
            }
            if( 0 === strcasecmp( self::remNumSuffix( $pKey ), $pValue )) {
                $relationDto->addRelation( $pValue );
            }
        } // end foreach
        return [ $id, $relationDto ];
    }

    /**
     * Removes trailing numeric chars from string
     *
     * @param string $key
     * @return string
     */
    private static function remNumSuffix( string $key ) : string
    {
        while( is_numeric( substr( $key, -1 ))) {
            $key = substr( $key, 0, -1 );
        }
        return $key;
    }
}
