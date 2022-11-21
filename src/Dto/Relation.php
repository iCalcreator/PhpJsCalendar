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

final class Relation extends BaseDto
{
    /**
     * Describes how the linked object is related to the linking object.
     *
     * The relation is defined as a set of relation types
     * "first":  The linked object is the first in a series the linking object is part of.
     * "next":   The linked object is next in a series the linking object is part of.
     * "child":  The linked object is a subpart of the linking object.
     * "parent": The linking object is a subpart of the linked object.
     * OR a value registered in the IANA "JSCalendar Enum Values" registry
     * OR a vendor-specific value
     *
     * @var array String[Boolean]
     */
    private array $relation = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::RELATION;
    }

    /**
     * Class factory factory method
     *
     * @param string $relation
     * @return static
     */
    public static function factoryRelation( string $relation ) : Relation
    {
        return ( new self())->addRelation( $relation );
    }

    /**
     * @return array
     */
    public function getRelation() : array
    {
        return $this->relation;
    }

    /**
     * @return int
     */
    public function getRelationCount() : int
    {
        return count( $this->relation );
    }

    /**
     * @param string $relation
     * @param null|bool $bool default true
     * @return Relation
     */
    public function addRelation( string $relation, ? bool $bool = true  ) : Relation
    {
        $this->relation[$relation] = $bool;
        return $this;
    }

    /**
     * @param array $relation
     * @return Relation
     */
    public function setRelation( array $relation ) : Relation
    {
        foreach( $relation as $key => $value ) {
            if( self::isStringKeyAndBoolValue( $key, $value )) {
                $this->addRelation( $key, $value );
            }
            else {
                $this->addRelation( $value );
            }
        }
        return $this;
    }

}
