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
use DateTimeInterface;
use Exception;

final class Event extends BaseEventTask
{
    /**
     * @var string  Media type
     */
    public static string $mediaType = 'application/jscalendar+json;type=event';

    /**
     * Event property order as in rfc8984
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
        self::SHOWWITHOUTTIME,
        self::START,
        self::TIMEzONE,
        self::DURATION,
        self::STATUS,
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
     * The zero or positive duration of the event in the event's start time zone, optional, default: "PT0S"
     *
     * The end time of an event can be found by adding the duration to the event's start time.
     *
     * An Event MAY involve start and end locations that are in different
     * time zones (e.g., a transcontinental flight).  This can be expressed
     * using the "relativeTo" and "timeZone" properties of the Event's Location objects
     *
     * @var DateInterval|null
     */
    private ? DateInterval $duration = null;

    /**
     * @var string
     */
    public static string $durationDefault = 'PT0S';

    /**
     * The scheduling status of an Event, optional, default: "confirmed"
     *
     * "confirmed":  indicates the event is definitely happening
     * "cancelled":  indicates the event has been cancelled
     * "tentative":  indicates the event may happen
     * or a vendor-specific value
     *
     * @var string|null
     */
    private ? string $status = null;

    /**
     * @var string
     */
    public static string $statusDefault = 'confirmed';

    /**
     * Class constructor
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::EVENT;
    }

    /**
     * Class factory method
     *
     * @param null|string|DateTimeInterface $start
     * @param null|string|DateInterval $duration
     * @param string|null $title
     * @return static
     * @throws Exception
     */
    public static function factory(
        null|string|DateTimeInterface $start = null,
        null|string|DateInterval $duration = null,
        null|string $title = null
    ) : Event
    {
        $instance = new self();
        if( null!== $start ) {
            $instance->setStart( $start );
        }
        if( null !== $duration ) {
            $instance->setDuration( $duration );
        }
        if( null !== $title ) {
            $instance->setTitle( $title );
        }
        return $instance;
    }

    /**
     * @param null|bool $asString default true
     * @param null|bool $defaultIfNotSet
     * @return null | string | DateInterval
     * @return DateInterval|null
     * @throws Exception
     */
    public function getDuration( ? bool $asString = true, ? bool $defaultIfNotSet = false ) : null|string|DateInterval
    {
        $isEmpty = ! $this->isDurationSet();
        return match( true ) {
            ( ! $defaultIfNotSet && $isEmpty )
                => null,
            $isEmpty // && default
                => $asString ? self::$durationDefault : new DateInterval( self::$durationDefault ),
            (( $this->duration instanceof DateInterval ) && $asString )
                => self::dateInterval2String( $this->duration, false ),
            default
                => $this->duration
        };
    }

    /**
     * Return bool true if duration is not null
     *
     * @return bool
     */
    public function isDurationSet() : bool
    {
        return ( null !== $this->duration );
    }

    /**
     * @param string|DateInterval $duration
     * @return static
     * @throws Exception
     */
    public function setDuration( string | DateInterval $duration ) : Event
    {
        $this->duration = $duration instanceof DateInterval
            ? $duration
            : new DateInterval( $duration );
        return $this;
    }

    /**
     * Return end datetime as LocalDateTime, only if start and duration is set, otherwise null
     *
     * @param bool|null $asString  default true
     * @return string|DateTimeInterface|null         DateTime with UTC, string without timezone suffix
     */
    public function getEstimatedEnd( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        if( empty( $this->start ) || empty( $this->duration )) {
            return null;
        }
        $dateTime = self::modifyDateTimeFromDateInterval( $this->start, $this->duration );
        return ( $asString )
            ? $dateTime->format( self::$LocalDateTimeFMT )
            : $dateTime;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getStatus( ? bool $defaultIfNotSet = false ) : ?string
    {
        return ( ! $this->isStatusSet() && $defaultIfNotSet )
            ? self::$statusDefault
            : $this->status;
    }

    /**
     * Return bool true if status is not null
     *
     * @return bool
     */
    public function isStatusSet() : bool
    {
        return ( null !== $this->status );
    }

    /**
     * @param string $status
     * @return static
     */
    public function setStatus( string $status ) : Event
    {
        $this->status = $status;
        return $this;
    }
}
