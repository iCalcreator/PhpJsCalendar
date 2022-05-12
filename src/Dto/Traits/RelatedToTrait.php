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
namespace Kigkonsult\PhpJsCalendar\Dto\Traits;

use Kigkonsult\PhpJsCalendar\Dto\Relation;

trait RelatedToTrait
{
    /**
     * A map of the UIDs of the related events/tasks to information about the relation, optional
     * Or relates this alert to other alerts in the same JSCalendar object
     *
     * @var mixed[] String[Relation]
     */
    protected array $relatedTo = [];

    /**
     * @return mixed[]
     */
    public function getRelatedTo() : array
    {
        return $this->relatedTo;
    }

    /**
     * @return int
     */
    public function getRelatedToCount() : int
    {
        return count( $this->relatedTo );
    }

    /**
     * @param string $uid
     * @param string|Relation $relatedTo   if string, relation type
     * @return static
     */
    public function addRelatedTo( string $uid, string | Relation $relatedTo ) : static
    {
        $this->relatedTo[$uid] = ( $relatedTo instanceof Relation )
            ? $relatedTo
            : Relation::factoryRelation( $relatedTo );
        return $this;
    }

    /**
     * @param array $relatedTo  String[Relation]
     * @return static
     */
    public function setRelatedTo( array $relatedTo ) : static
    {
        foreach( $relatedTo as $uid => $relation ) {
            $this->addRelatedTo( $uid,  $relation );
        }
        return $this;
    }
}
