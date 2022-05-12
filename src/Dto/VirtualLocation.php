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

use Kigkonsult\PhpJsCalendar\Dto\Traits\DescriptionTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\NameTrait;

final class VirtualLocation extends BaseDto
{
    /**
     * The human-readable name of the virtual location
     *
     * Optional
     */
    use NameTrait;

    /**
     * Human-readable plain-text instructions for accessing this virtual location.  This may be a conference access code, etc.
     *
     * Optional
     */
    use DescriptionTrait;

    /**
     * A URI [RFC3986] that represents how to connect to this virtual location
     *
     * Mandatory
     *
     * @var null|string
     */
    private ? string $uri = null;

    /**
     * A set of features supported by this virtual location
     *
     * audio:     Audio conferencing
     * chat:      Chat or instant messaging
     * feed:      Blog or atom feed
     * moderator: Provides moderator-specific features
     * phone:     Phone conferencing
     * screen:    Screen sharing
     * video:     Video conferencing
     * OR another value registered in the IANA "JSCalendar Enum Values" registry
     * OR a vendor-specific value
     *
     * Optional
     *
     * @var mixed[]    String[Boolean]
     */
    private array $features = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::VIRTUALLOCATION;
    }

    /**
     * Class factory method
     *
     * @param string $uri
     * @param string $feature
     * @return static
     */
    public static function factoryUriFeature( string $uri, string $feature ) : static
    {
        return ( new self())->setUri( $uri )->addFeature( $feature );
    }

    /**
     * @return null|string
     */
    public function getUri() : ? string
    {
        return $this->uri;
    }

    /**
     * Return bool true if uri is not null
     *
     * @return bool
     */
    public function isUriSet() : bool
    {
        return ( null !== $this->uri );
    }

    /**
     * @param string $uri
     * @return static
     */
    public function setUri( string $uri ) : static
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getFeatures() : array
    {
        return $this->features;
    }

    /**
     * @return int
     */
    public function getFeaturesCount() : int
    {
        return count( $this->features );
    }

    /**
     * @param string $feature
     * @param null|bool $bool default true
     * @return static
     */
    public function addFeature( string $feature, ? bool $bool = true ) : static
    {
        $this->features[$feature] = $bool;
        return $this;
    }

    /**
     * @param array $features
     * @return static
     */
    public function setFeatures( array $features ) : static
    {
        $this->features = $features;
        foreach( $features as $key => $value ) {
            if( is_string( $key ) && ! is_numeric( $key ) && is_bool( $value )) {
                $this->addFeature( $key, $value );
            }
            else {
                $this->addFeature( $value );
            }
        }
        return $this;
    }
}
