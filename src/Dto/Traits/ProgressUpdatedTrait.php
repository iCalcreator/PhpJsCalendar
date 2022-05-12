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

use DateTime;
use DateTimeInterface;
use Exception;

trait ProgressUpdatedTrait
{
    /**
     * Specifies the date/time the "progress" of either the task overall or a specific participant was last updated
     *
     * Optional; only allowed for participants of a Task
     *
     * @var DateTimeInterface|null   UTCDateTime
     */
    private ? DateTimeInterface $progressUpdated = null;

    /**
     * @param null|bool $asString  default true
     * @return null|string|DateTimeInterface   DateTime in UTC, string with suffix 'Z'
     */
    public function getProgressUpdated( ? bool $asString = true ) : null |string | DateTimeInterface
    {
        return (( $this->progressUpdated instanceof DateTimeInterface ) && $asString )
            ? $this->progressUpdated->format( self::$UTCDateTimeFMT )
            : $this->progressUpdated;
    }

    /**
     * Return bool true if progressUpdated is not null
     *
     * @return bool
     */
    public function isProgressUpdatedSet() : bool
    {
        return ( null !== $this->progressUpdated );
    }

    /**
     * Set progressUpdated
     *
     * If empty, UTC date-time now
     * If DateTime, any timezone allowed, converted to UTC DateTime
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param null|string|DateTimeInterface $progressUpdated UTCDateTime
     * @return static
     * @throws Exception
     */
    public function setProgressUpdated( null | string | DateTimeInterface $progressUpdated = null ) : static
    {
        $this->progressUpdated = self::toUtcDateTime( $progressUpdated ?? new DateTime(), false );
        return $this;
    }
}
