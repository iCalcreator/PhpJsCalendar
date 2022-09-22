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

/**
An "UnknownTrigger" object is an object that contains an "@type"
property whose value is not recognized (i.e., not "OffsetTrigger"
or "AbsoluteTrigger") plus zero or more other properties.  This is
for compatibility with client extensions and future
specifications.  Implementations SHOULD NOT trigger for trigger
types they do not understand but MUST preserve them.
 */
final class UnknownTrigger extends BaseDto
{
    /**
     * Any UnknownTrigger properties, set and returned 'as is'
     *
     * @var array
     */
    private array $properties = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::UNKNOWNTRIGGER;
    }

    /**
     * Class factory method
     *
     * @param string $type
     * @return static
     */
    public static function factoryType( string $type ) : UnknownTrigger
    {
        return ( new self())->setType( $type );
    }

    /**
     * @return array
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @return int
     */
    public function getPropertiesCount() : int
    {
        return count( $this->properties );
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function addProperty( string $key, mixed $value ) : UnknownTrigger
    {
        $this->properties[$key] = $value;
        return $this;
    }

    /**
     * @param array $properties
     * @return UnknownTrigger
     */
    public function setProperties( array $properties ) : UnknownTrigger
    {
        foreach( $properties as $key => $value ) {
            $this->addProperty( $key, $value );
        }
        return $this;
    }
}
