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

use DateTimeInterface;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\BaseDto;

/**
 * Used by baseEventTask and TimeZoneRule
 */
trait StartTrait
{
    /**
     * The date/time the event/task starts in the objects time zone (as specified in the "timeZone" property), mandatory
     * For a TimeZoneRule the STANDARD/DAYLIGHT start datetime
     *
     * @var DateTimeInterface|null LocalDateTime  saved in UTC DateTime
     */
    protected ? DateTimeInterface $start = null;

    /**
     * @param null|bool $asString  default true
     * @return null|string|DateTimeInterface   DateTime with UTC, string without timezone suffix
     */
    public function getStart( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        return (( $this->start instanceof DateTimeInterface ) && $asString )
            ? $this->start->format( self::$LocalDateTimeFMT )
            : $this->start;
    }

    /**
     * Return bool true if start is not null
     *
     * @return bool
     */
    public function isStartSet() : bool
    {
        return ( null !== $this->start );
    }

    /**
     * Set start, DateTime or string (as defined in https://www.php.net/manual/en/datetime.formats.php)
     *
     * If DateTime, any timezone allowed, saved as DateTime with input:date[time] with UTC timezone
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param string|DateTimeInterface $start LocalDateTime, saved as DateTime with UTC (note, not 'in' UTC)
     * @return static
     * @throws Exception
     */
    public function setStart( string | DateTimeInterface $start ) : static
    {
        $this->start = BaseDto::toUtcDateTime( $start, true );
        return $this;
    }
}
