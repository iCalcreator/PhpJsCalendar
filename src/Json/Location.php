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

use Kigkonsult\PhpJsCalendar\Dto\Location as Dto;
use stdClass;

class Location extends BaseJson
{
    /**
     * Parse json array to populate new Location
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::NAME] ) ) {
            $dto->setName( $jsonArray[self::NAME] );
        }
        if( isset( $jsonArray[self::DESCRIPTION] ) ) {
            $dto->setDescription( $jsonArray[self::DESCRIPTION] );
        }
        if( isset( $jsonArray[self::LOCATIONTYPES] ) ) {
            foreach( $jsonArray[self::LOCATIONTYPES] as $locationType => $bool ) {
                $dto->addLocationType( $locationType, self::jsonBool2Php( $bool ));
            }
        }
        if( isset( $jsonArray[self::RELATIVETO] ) ) {
            $dto->setRelativeTo( $jsonArray[self::RELATIVETO] );
        }
        elseif( isset( $jsonArray[self::REL] ) ) {  // due to error? in rfc8984 6.6.  Event with End Time Zone ??
            $dto->setRelativeTo( $jsonArray[self::REL] );
        }
        if( isset( $jsonArray[self::TIMEzONE] )) {
            $dto->setTimeZone( $jsonArray[self::TIMEzONE] );
        }
        if( isset( $jsonArray[self::COORDINATES] )) {
            $dto->setCoordinates( $jsonArray[self::COORDINATES] );
        }
        if( isset( $jsonArray[self::LINKS] )) {
            foreach( $jsonArray[self::LINKS] as $lid => $link ) {
                $dto->addLink( $lid, Link::parse( $lid, $link ));
            }
        }
        return $dto;
    }

    /**
     * Write Location Dto properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return array
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];
        if( $dto->isNameSet()) {
            $jsonArray[self::NAME] = $dto->getName();
        }
        if( $dto->isDescriptionSet()) {
            $jsonArray[self::DESCRIPTION] = $dto->getDescription();
        }
        // array of "String[Boolean]"
        if( ! empty( $dto->getLocationTypesCount())) {
            foreach( $dto->getLocationTypes() as $locationType => $bool ) {
                $jsonArray[self::LOCATIONTYPES][$locationType] = $bool;
            }
        }
        if( $dto->isRelativeToSet()) {
            $jsonArray[self::RELATIVETO] = $dto->getRelativeTo();
        }
        if( $dto->isTimeZoneSet()) {
            $jsonArray[self::TIMEzONE] = $dto->getTimeZone();
        }
        if( $dto->isCoordinatesSet()) {
            $jsonArray[self::COORDINATES] = $dto->getCoordinates();
        }
        // array of "Id[Link]"
        if( ! empty( $dto->getLinksCount())) {
            $jsonArray[self::LINKS] = new stdClass();
            foreach( $dto->getLinks() as $lid => $link ) {
                $jsonArray[self::LINKS]->{$lid} = Link::write( $lid, $link );
            }
        }
        return $jsonArray;
    }
}
