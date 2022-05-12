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

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Traits\PercentCompleteTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\ProgressTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\ProgressUpdatedTrait;

final class Task extends BaseEventTask
{
    /**
     * @var string  Media type
     */
    public static string $mediaType = 'application/jscalendar+json;type=task';

    /**
     * Task property order as in rfc8984
     *
     * @var string[]
     */
    public static array $ElementOrder = [
        self::OBJECTTYPE,
        self::UID,
        self::PRODID,
        self::CREATED,
        self::UPDATED,
        self::TITLE,
        self::DESCRIPTION,
        self::DESCRIPTIONCONTENTTYPE,
        self::LOCALE,
        self::SEQUENCE,
        self::METHOD,
        self::DUE,
        self::START,
        self::TIMEzONE,
        self::ESTIMATEDDURATION,
        self::PERCENTCOMPLETE,
        self::PROGRESS,
        self::PROGRESSUPDATED,
        self::SHOWWITHOUTTIME,
        self::LOCATIONS,
        self::VIRTUALLOCATIONS,
        self::LINKS,
        self::KEYWORDS,
        self::CATEGORIES,
        self::COLOR,
        self::RECURRENCEID,
        self::RECURRENCEIDTIMEZONE,
        self::RECURRENCERULES,
        self::EXCLUDEDRECURRENCERULES,
        self::RECURRENCEOVERRIDES,
        self::EXCLUDED,
        self::PRIORITY,
        self::FREEBUSYSTATUS,
        self::PRIVACY,
        self::REPLYTO,
        self::SENTBY,
        self::PARTICIPANTS,
        self::REQUESTSTATUS,
        self::USEDEFAULTALERTS,
        self::ALERTS,
        self::LOCALIZATIONS,
        self::TIMEZONES,
    ];

    /**
     * The date/time the task is due in the task's time zone.
     *
     * @var DateTimeInterface|null  LocalDateTime, saved in UTC
     */
    private ? DateTimeInterface $due = null;

    /**
     * Specifies the estimated positive duration of time the task takes to complete
     *
     * @var DateInterval|null
     */
    private ? DateInterval $estimatedDuration = null;

    /**
     * The progress of a task
     */
    use ProgressTrait;

    /**
     * Specifies the date-time the "progress" property was last set on a Task overall
     */
    use ProgressUpdatedTrait;

    /**
     * Represents the percent completion of the Task overall
     */
    use PercentCompleteTrait;

    /**
     * Class constructor
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TASK;
    }

    /**
     * Class factory method
     *
     * @param null|string|DateTimeInterface $start
     * @param null|string|DateTimeInterface $due
     * @param null|string|DateInterval $duration
     * @param null|string $title
     * @return static
     * @throws Exception
     */
    public static function factory(
        null|string|DateTimeInterface $start = null,
        null|string|DateTimeInterface $due = null,
        null|string|DateInterval $duration = null,
        null|string $title = null
    ) : static
    {
        $instance = new self();
        if( null!== $start ) {
            $instance->setStart( $start );
        }
        if( null !== $due ) {
            $instance->setDue( $due );
        }
        if( null !== $duration ) {
            $instance->setEstimatedDuration( $duration );
        }
        if( null !== $title ) {
            $instance->setTitle( $title );
        }
        return $instance;
    }

    /**
     * @param null|bool $asString  default true
     * @return null|string|DateTimeInterface   DateTime with UTC, string without timezone suffix
     */
    public function getDue( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        return (( $this->due instanceof DateTimeInterface ) && $asString )
            ? $this->due->format( self::$LocalDateTimeFMT )
            : $this->due;
    }

    /**
     * Return bool true if due is not null
     *
     * @return bool
     */
    public function isDueSet() : bool
    {
        return ( null !==  $this->due );
    }

    /**
     * Set due, DateTime or string (as defined in https://www.php.net/manual/en/datetime.formats.php)
     *
     * If DateTime, any timezone allowed, saved as DateTime with input:date[time] with UTC timezone
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param null|string|DateTimeInterface $due LocalDateTime, saved as DateTime with UTC (note, not 'in' UTC)
     * @return static
     * @throws Exception
     */
    public function setDue( null | string | DateTimeInterface $due ) : static
    {
        $this->due = self::toUtcDateTime( $due ?? new DateTime());
        return $this;
    }

    /**
     * @param null|bool $asString  default true
     * @return null | string | DateInterval
     */
    public function getEstimatedDuration( ? bool $asString = true ) : null|string|DateInterval
    {
        return (( $this->estimatedDuration instanceof DateInterval ) && $asString )
            ? self::dateInterval2String( $this->estimatedDuration, false )
            : $this->estimatedDuration;
    }

    /**
     * Return bool true if estimatedDuration is not null
     *
     * @return bool
     */
    public function isestimatedDurationSet() : bool
    {
        return ( null !==  $this->estimatedDuration );
    }

    /**
     * @param string|DateInterval $estimatedDuration
     * @return static
     * @throws Exception
     */
    public function setEstimatedDuration( string|DateInterval $estimatedDuration ) : static
    {
        $this->estimatedDuration = $estimatedDuration instanceof DateInterval
            ? $estimatedDuration
            : new DateInterval( $estimatedDuration );
        return $this;
    }

    /**
     * Return estimated start datetime as LocalDateTime, only if due and estimatedDuration is set, otherwise null
     *
     * @param bool|null $asString  default true
     * @return string|DateTimeInterface|null        DateTime with UTC, string without timezone suffix
     */
    public function getEstimatedStart( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        if( empty( $this->due ) || empty( $this->estimatedDuration )) {
            return null;
        }
        $estimatedDuration         = clone $this->estimatedDuration;
        $estimatedDuration->invert = 1; // invert duration, count backwards
        $dateTime = self::modifyDateTimeFromDateInterval( $this->due, $estimatedDuration );
        return ( $asString )
            ? $dateTime->format( self::$LocalDateTimeFMT )
            : $dateTime;
    }

    /**
     * Return estimated end datetime as LocalDateTime, only if start and estimatedDuration is set, otherwise null
     *
     * @param bool|null $asString  default true
     * @return string|DateTimeInterface|null        DateTime with UTC, string without timezone suffix
     */
    public function getEstimatedEnd( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        if( empty( $this->start ) || empty( $this->estimatedDuration )) {
            return null;
        }
        $dateTime = self::modifyDateTimeFromDateInterval( $this->start, $this->estimatedDuration );
        return ( $asString )
            ? $dateTime->format( self::$LocalDateTimeFMT )
            : $dateTime;
    }
}
