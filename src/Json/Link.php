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

use Kigkonsult\PhpJsCalendar\Dto\Link as Dto;

class Link extends BaseJson
{
    /**
     * Parse json array to populate new Link
     *
     * @param string $lid
     * @param string[]|string[][] $jsonArray
     * @return Dto
     */
    public static function parse( string $lid, array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::HREF] ) ) {
            $dto->setHref( $jsonArray[self::HREF] );
        }
        $dto->setCid( $jsonArray[self::CID] ?? $lid );
        if( isset( $jsonArray[self::CONTENTTYPE] ) ) {
            $dto->setContentType( $jsonArray[self::CONTENTTYPE] );
        }
        if( isset( $jsonArray[self::SIZE] ) ) {
            $dto->setSize((int) $jsonArray[self::SIZE] );
        }
        if( isset( $jsonArray[self::REL] ) ) {
            $dto->setRel( $jsonArray[self::REL] );
        }
        if( isset( $jsonArray[self::DISPLAY] ) ) {
            $dto->setDisplay( $jsonArray[self::DISPLAY] );
        }
        if( isset( $jsonArray[self::TITLE] ) ) {
            $dto->setTitle( $jsonArray[self::TITLE] );
        }
        return $dto;
    }

    /**
     * Write Link Dto properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param string $lid
     * @param Dto $dto
     * @return array
     */
    public static function write( string $lid, Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];
        if( $dto->isHrefSet()) {
            $jsonArray[self::HREF] = $dto->getHref();
        }
        $cid     = $dto->getCid();
        if( $cid !== $lid ) {
            $cid = $lid;
        }
        $jsonArray[self::CID] = $cid;
        if( $dto->isContentTypeSet()) {
            $jsonArray[self::CONTENTTYPE] = $dto->getContentType();
        }
        if( $dto->isSizeSet()) {
            $jsonArray[self::SIZE] = $dto->getSize();
        }
        if( $dto->isRelSet()) {
            $jsonArray[self::REL] = $dto->getRel();
        }
        if( $dto->isDisplaySet()) {
            $jsonArray[self::DISPLAY] = $dto->getDisplay();
        }
        if( $dto->isTitleSet()) {
            $jsonArray[self::TITLE] = $dto->getTitle();
        }
        return $jsonArray;
    }
}
