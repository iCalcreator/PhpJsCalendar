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
use Kigkonsult\PhpJsCalendar\Dto\Group as Dto;

class Group extends BaseGroupEventTask
{
    /**
     * Parse json array to populate new Group
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        parent::groupEventTaskParse( $jsonArray, $dto );
        if( isset( $jsonArray[self::ENTRIES] )) {
            foreach( $jsonArray[self::ENTRIES] as $groupMember ) {
                if( self::hasObjectType( $groupMember, self::EVENT )) {
                    $dto->addEntry( Event::parse( $groupMember ));
                }
                elseif( self::hasObjectType( $groupMember, self::TASK )) {
                    $dto->addEntry( Task::parse( $groupMember ));
                }
            }
        }
        if( isset( $jsonArray[self::SOURCE] )) {
            $dto->setSource( $jsonArray[self::SOURCE] );
        }
        return $dto;
    }

    /**
     * Parse json array to populate new Group
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return array
     * @throws Exception
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];
        parent::groupEventTaskWrite( $dto, $jsonArray );
        // array of "(Task|Event)[]"
        if( ! empty( $dto->getEntriesCount())) {
            foreach( $dto->getEntries() as $entry ) {
                if( self::EVENT === $entry->getType()) {
                    $jsonArray[self::ENTRIES][] = (object)Event::write( $entry );
                }
                elseif( self::TASK === $entry->getType() ) {
                    $jsonArray[self::ENTRIES][] = (object)Task::write( $entry );
                }
            }
        }
        if( $dto->isSourceSet()) {
            $jsonArray[self::SOURCE] = $dto->getSource();
        }
        return self::orderElements( Dto::$ElementOrder, $jsonArray );
    }
}
