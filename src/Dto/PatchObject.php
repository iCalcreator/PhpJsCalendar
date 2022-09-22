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

use ArrayAccess;

/**
 * The only class without external type, INTERNALLY it is 'PatchObject'
 */
final class PatchObject extends BaseDto implements ArrayAccess
{
    /**
     * @var array
     */
    private array $container = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::PATCHOBJECT;
    }

    /**
     * Class factory method
     *
     * @param array $patches
     * @return static
     */
    public static function factory( array $patches ) : PatchObject
    {
        return ( new self())->setPatches( $patches );
    }

    /**
     * Add pointer/value pair, overwrite if pointer exist
     *
     * @param string $pointer
     * @param mixed $value
     * @return $this
     */
    public function append( string $pointer, mixed $value ) : PatchObject
    {
        $this->offsetSet( $pointer,$value );
        return $this;
    }

    /**
     * Return array container
     *
     * @return array
     */
    public function getPatches() : array
    {
        return $this->container;
    }

    /**
     * Return array container
     *
     * @return int
     */
    public function getPatchesCount() : int
    {
        return count( $this->container );
    }

    /**
     * Return pointer value, null if pointer NOT exists
     *
     * @param string $pointer
     * @return mixed
     */
    public function getPointerValue( string $pointer) : mixed
    {
        return $this->offsetget( $pointer );
    }

    /**
     * Return bool true if pointer is set, i.e if offset exists
     *
     * @param string $pointer
     * @return bool
     */
    public function isPointerSet( string $pointer) : bool
    {
        return $this->offsetExists( $pointer );
    }

    /**
     * Remove pointer value
     *
     * @param string $pointer
     * @return static
     */
    public function removePointer( string $pointer) : PatchObject
    {
        $this->offsetUnset( $pointer );
        return $this;
    }

    /**
     * Set patch pointer/value pars
     *
     * @param array $patches
     * @return static
     */
    public function setPatches( array $patches ) : PatchObject
    {
        foreach( $patches as $pointer => $value ) {
            $this->append( $pointer, $value );
        }
        return $this;
    }

    /**
     * Implemented interface methods
     */
    public function offsetSet( mixed $offset, mixed $value ) : void
    {
        $this->container[$offset] = $value;
    }

    public function offsetExists( $offset ) : bool
    {
        return isset( $this->container[$offset] );
    }

    public function offsetUnset( $offset ) : void
    {
        unset( $this->container[$offset] );
    }

    public function offsetGet( $offset ) : mixed
    {
        return $this->container[$offset] ?? null;
    }
}
