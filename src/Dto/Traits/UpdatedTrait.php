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

use Exception;
use DateTime;
use DateTimeInterface;

trait UpdatedTrait
{
    /**
     * The date and time the data in this object was last modified
     *
     * @var null|DateTimeInterface   UTCDateTime
     */
    protected ? DateTimeInterface $updated = null;
    /**
     * Return updated
     *
     * @param null|bool $asString  default true
     * @return string|DateTimeInterface|null     DateTime in UTC, string with suffix 'Z'
     */
    public function getUpdated( ? bool $asString = true ) : null|string|DateTimeInterface
    {
        return ( ! empty( $this->updated ) && $asString )
            ? $this->updated->format( self::$UTCDateTimeFMT )
            : $this->updated;
    }

    /**
     * Return bool true if updated is not null
     *
     * @return bool
     */
    public function isUpdatedSet() : bool
    {
        return ( null !== $this->updated );
    }

    /**
     * Set updated
     *
     * If empty, UTC date-time now
     * If DateTime, any timezone allowed, converted to UTC DateTime
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param null|string|DateTimeInterface $updated any timeZone, saved as UTC
     * @return static
     * @throws Exception
     */
    public function setUpdated( null|string|DateTimeInterface $updated = null ) : static
    {
        $this->updated = self::toUtcDateTime( $updated ?? new DateTime(), false );
        return $this;
    }
}
