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

use Kigkonsult\PhpJsCalendar\Dto\Link;

trait LinksTrait
{
    /**
     * A map of link ids to Link objects, representing external resources associated with the object, optional
     *
     * Id type : 1-255 chars, "A-Za-z0-9", "-", "_" only
     *
     * @var mixed[]  Id[Link]
     */
    protected array $links = [];

    /**
     * @return mixed[]
     */
    public function getLinks() : array
    {
        return $this->links;
    }

    /**
     * @return int
     */
    public function getLinksCount() : int
    {
        return count( $this->links );
    }

    /**
     * @param string|Link $id
     * @param null|Link $link
     * @return static
     */
    public function addLink( string|Link $id, ? Link $link = null ) : static
    {
        if( $id instanceof Link ) {
            $link = $id;
            $id   = $link->getCid(); // auto set as uuid
        }
        $this->links[$id] = $link;
        ksort( $this->links );
        return $this;
    }

    /**
     * @param array $links
     * @return static
     */
    public function setLinks( array $links ) : static
    {
        foreach( $links as $id => $link ) {
            $this->addLink( $id, $link );
        }
        return $this;
    }
}
