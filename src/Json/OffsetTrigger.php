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

use Exception;
use Kigkonsult\PhpJsCalendar\Dto\OffsetTrigger as Dto;

class OffsetTrigger extends BaseJson
{
    /**
     * Parse json array to populate new NDay
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::OFFSET] )) {
            $dto->setOffset( $jsonArray[self::OFFSET] );
        }
        if( isset( $jsonArray[self::RELATIVETO] )) {
            $dto->setRelativeTo( $jsonArray[self::RELATIVETO] );
        }
        return $dto;
    }

    /**
     * Write OffsetTrigger Dto properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return array
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];

        if( $dto->isOffsetSet()) {
            $jsonArray[self::OFFSET] = $dto->getOffset();
        }
        if( $dto->isRelativeToSet()) {
            $jsonArray[self::RELATIVETO] = $dto->getRelativeTo();
        }

        return $jsonArray;
    }
}
