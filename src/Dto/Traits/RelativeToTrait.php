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

trait RelativeToTrait
{
    /**
     * Specifies the time property that the this (class intrance) is relative to
     *
     * Note, constants START and END
     *
     * @var null|string
     */
    private ? string $relativeTo = null;

    /**
     * @param null|bool $defaultIfNotSet
     * @return null|string
     */
    public function getRelativeTo( ? bool $defaultIfNotSet = false ) : ? string
    {
        return ( ! $this->isRelativeToSet() && $defaultIfNotSet )
            ? self::$relativeToDefault
            : $this->relativeTo;
    }

    /**
     * Return bool true if relativeTo is not null
     *
     * @return bool
     */
    public function isRelativeToSet() : bool
    {
        return ( null !== $this->relativeTo );
    }

    /**
     * @param string $relativeTo
     * @return static
     */
    public function setRelativeTo( string $relativeTo ) : static
    {
        $this->relativeTo = $relativeTo;
        return $this;
    }
}
