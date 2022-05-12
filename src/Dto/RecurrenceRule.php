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

use DateTime;
use DateTimeInterface;
use Exception;

final class RecurrenceRule extends BaseDto
{
    /**
     * The FREQ part from iCalendar, converted to lowercase
     *
     * one of yearly/monthly/weekly/daily/hourly/minutely/secondly
     *
     * @var null|string
     */
    private ? string $frequency= null;

    /**
     * The INTERVAL part from iCalendar, optional, default 1
     *
     * @var int|null  UnsignedInt
     */
    private ? int $interval = null;

    /**
     * @var int
     */
    public static int $intervalDefault = 1;

    /**
     * The RSCALE part from iCalendar RSCALE [RFC7529], converted to lowercase, optional, default: "gregorian"
     * @var string|null
     */
    private ? string $rscale = null;

    /**
     * @var string
     */
    public static string $rscaleDefault = 'gregorian';

    /**
     * The SKIP part from iCalendar RSCALE [RFC7529], converted to lowercase, optional, default: "omit"
     *
     * one of omit/backward/forward
     *
     * @var string|null
     */
    private ? string $skip = null;

    /**
     * @var string
     */
    public static string $skipDefault = 'omit';

    /**
     * The WKST part from iCalendar, optional, default: "mo"
     *
     * one of mo/tu/we/th/fr/sa/su
     *
     * @var string|null
     */
    private ? string $firstDayOfWeek = null;

    /**
     * @var string
     */
    public static string $firstDayOfWeekDefault = 'mo';

    /**
     * days of the week on which to repeat, optional
     *
     * @var NDay[]
     */
    private array $byDay = [];

    /**
     * The BYMONTH part from iCalendar, optional
     *
     * with an optional "L" suffix (see [RFC7529]) for leap months (this MUST be uppercase, e.g., "3L")
     *
     * @var string[]
     */
    private array $byMonth = [];

    /**
     * The BYMONTHDAY part in iCalendar, optional
     *
     * @var int[]
     */
    private array $byMonthDay = [];

    /**
     * The BYYEARDAY part from iCalendar, optional
     *
     * @var int[]
     */
    private array $byYearDay = [];

    /**
     * The BYWEEKNO part from iCalendar, optional
     *
     * @var int[]
     */
    private array $byWeekNo = [];

    /**
     * The BYHOUR part from iCalendar, optional
     *
     * @var int[]   unsignedInt[]
     */
    private array $byHour = [];

    /**
     * The BYSECOND part from iCalendar, optional
     *
     * @var int[]   unsignedInt[]
     */
    private array $byMinute = [];

    /**
     * The BYMINUTE part from iCalendar, optional
     *
     * @var int[]   unsignedInt[]
     */
    private array $bySecond = [];

    /**
     * The BYSETPOS part from iCalendar, optional
     *
     * @var int[]
     */
    private array $bySetPosition = [];

    /**
     * The COUNT part from iCalendar, optional
     *
     * @var null|int  UnsignedInt
     */
    private ? int $count = null;

    /**
     * The UNTIL part from iCalendar, optional
     *
     * @var null|DateTimeInterface  LocalDateTime
     */
    private ? DateTimeInterface $until = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::RECURRENCERULE;
    }

    /**
     * @return null|string
     */
    public function getFrequency() : ? string
    {
        return $this->frequency;
    }

    /**
     * Return bool true if frequency is not null
     *
     * @return bool
     */
    public function isFrequencySet() : bool
    {
        return ( null!== $this->frequency );
    }

    /**
     * @param string $frequency
     * @return static
     */
    public function setFrequency( string $frequency ) : static
    {
        $this->frequency = strtolower( $frequency );
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return int|null
     */
    public function getInterval( ? bool $defaultIfNotSet = false ) : ? int
    {
        return ( ! $this->isIntervalSet() && $defaultIfNotSet )
            ? self::$intervalDefault
            : $this->interval;
    }

    /**
     * Return bool true if interval is not null
     *
     * @return bool
     */
    public function isIntervalSet() : bool
    {
        return ( null !== $this->interval );
    }

    /**
     * @param int $interval
     * @return static
     */
    public function setInterval( int $interval ) : static
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getRscale( ? bool $defaultIfNotSet = false ) : ? string
    {
        return ( ! $this->isRscaleSet() && $defaultIfNotSet )
            ? self::$rscaleDefault
            : $this->rscale;
    }

    /**
     * Return bool true if rscale is not null
     *
     * @return bool
     */
    public function isRscaleSet() : bool
    {
        return ( null !== $this->rscale );
    }

    /**
     * @param string $rscale
     * @return static
     */
    public function setRscale( string $rscale ) : static
    {
        $this->rscale = strtolower( $rscale );
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getSkip( ? bool $defaultIfNotSet = false ) : ?string
    {
        return ( ! $this->isSkipSet() && $defaultIfNotSet )
            ? self::$skipDefault
            : $this->skip;
    }

    /**
     * Return bool true if skip is not null
     *
     * @return bool
     */
    public function isSkipSet() : bool
    {
        return ( null !== $this->skip );
    }

    /**
     * one of omit/backward/forward, lowercase
     *
     * @param string $skip
     * @return static
     */
    public function setSkip( string $skip ) : static
    {
        $this->skip = strtolower( $skip );
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getFirstDayOfWeek( ? bool $defaultIfNotSet = false ) : ?string
    {
        return ( ! $this->isFirstDayOfWeekSet() && $defaultIfNotSet )
            ? self::$firstDayOfWeekDefault
            : $this->firstDayOfWeek;
    }

    /**
     * Return bool true if firstDayOfWeek is not null
     *
     * @return bool
     */
    public function isFirstDayOfWeekSet() : bool
    {
        return ( null !== $this->firstDayOfWeek );
    }

    /**
     * @param string $firstDayOfWeek
     * @return static
     */
    public function setFirstDayOfWeek( string $firstDayOfWeek ) : static
    {
        $this->firstDayOfWeek = strtolower( $firstDayOfWeek );
        return $this;
    }

    /**
     * @return NDay[]
     */
    public function getByDay() : array
    {
        return $this->byDay;
    }

    /**
     * @return int
     */
    public function getByDayCount() : int
    {
        return count( $this->byDay );
    }

    /**
     * @param NDay $byDay
     * @return static
     */
    public function addByDay( NDay $byDay ) : static
    {
        $this->byDay[] = $byDay;
        return $this;
    }

    /**
     * @param NDay[] $byDay
     * @return static
     */
    public function setByDay( array $byDay ) : static
    {
        foreach( $byDay as $theDay ) {
            $this->addByDay( $theDay );
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getByMonth() : array
    {
        return $this->byMonth;
    }

    /**
     * @return int
     */
    public function getByMonthCount() : int
    {
        return count( $this->byMonth );
    }

    /**
     * @param int|string $byMonth
     * @return static
     */
    public function addByMonth( int|string $byMonth ) : static
    {
        $this->byMonth[] = (string) $byMonth;
        return $this;
    }

    /**
     * @param array $byMonth
     * @return static
     */
    public function setByMonth( array $byMonth ) : static
    {
        foreach( $byMonth as $theMonth ) {
            $this->addByMonth( $theMonth );
        }
        return $this;
    }

    /**
     * @return int[]
     */
    public function getByMonthDay() : array
    {
        return $this->byMonthDay;
    }

    /**
     * @return int
     */
    public function getByMonthDayCount() : int
    {
        return count( $this->byMonthDay );
    }

    /**
     * @param int $byMonthDay
     * @return static
     */
    public function addByMonthDay( int $byMonthDay ) : static
    {
        $this->byMonthDay[] = $byMonthDay;
        return $this;
    }

    /**
     * @param int[] $byMonthDay
     * @return static
     */
    public function setByMonthDay( array $byMonthDay ) : static
    {
        foreach( $byMonthDay as $theMonthDay) {
            $this->addByMonthDay( $theMonthDay );
        }
        return $this;
    }

    /**
     * @return int[]
     */
    public function getByYearDay() : array
    {
        return $this->byYearDay;
    }

    /**
     * @return int
     */
    public function getByYearDayCount() : int
    {
        return count( $this->byYearDay );
    }

    /**
     * @param int $byYearDay
     * @return static
     */
    public function addByYearDay( int $byYearDay ) : static
    {
        $this->byYearDay[] = $byYearDay;
        return $this;
    }

    /**
     * @param int[] $byYearDay
     * @return static
     */
    public function setByYearDay( array $byYearDay ) : static
    {
        foreach( $byYearDay as $theYearDay ) {
            $this->addByYearDay( $theYearDay );
        }
        return $this;
    }

    /**
     * @return int[]
     */
    public function getByWeekNo() : array
    {
        return $this->byWeekNo;
    }

    /**
     * @return int
     */
    public function getByWeekNoCount() : int
    {
        return count( $this->byWeekNo );
    }

    /**
     * @param int $byWeekNo
     * @return static
     */
    public function addByWeekNo( int $byWeekNo ) : static
    {
        $this->byWeekNo[] = $byWeekNo;
        return $this;
    }

    /**
     * @param int[] $byWeekNo
     * @return static
     */
    public function setByWeekNo( array $byWeekNo ) : static
    {
        foreach( $byWeekNo as $theWeekNo ) {
            $this->addByWeekNo( $theWeekNo );
        }
        return $this;
    }

    /**
     * @return int[]
     */
    public function getByHour() : array
    {
        return $this->byHour;
    }

    /**
     * @return int
     */
    public function getByHourCount() : int
    {
        return count( $this->byHour );
    }

    /**
     * @param int $byHour
     * @return static
     */
    public function addByHour( int $byHour ) : static
    {
        $this->byHour[] = $byHour;
        return $this;
    }

    /**
     * @param int[] $byHour
     * @return static
     */
    public function setByHour( array $byHour ) : static
    {
        foreach( $byHour as $theHour ) {
            $this->addByHour( $theHour );
        }
        return $this;
    }

    /**
     * @return int[]
     */
    public function getByMinute() : array
    {
        return $this->byMinute;
    }

    /**
     * @return int
     */
    public function getByMinuteCount() : int
    {
        return count( $this->byMinute );
    }

    /**
     * @param int $byMinute
     * @return static
     */
    public function addByMinute( int $byMinute ) : static
    {
        $this->byMinute[] = $byMinute;
        return $this;
    }

    /**
     * @param int[] $byMinute
     * @return static
     */
    public function setByMinute( array $byMinute ) : static
    {
        foreach( $byMinute as $theMinute ) {
            $this->addByMinute( $theMinute );
        }
        return $this;
    }

    /**
     * @return int[]
     */
    public function getBySecond() : array
    {
        return $this->bySecond;
    }

    /**
     * @return int
     */
    public function getBySecondCount() : int
    {
        return count( $this->bySecond );
    }

    /**
     * @param int $bySecond
     * @return static
     */
    public function addBySecond( int $bySecond ) : static
    {
        $this->bySecond[] = $bySecond;
        return $this;
    }

    /**
     * @param int[] $bySecond
     * @return static
     */
    public function setBySecond( array $bySecond ) : static
    {
        foreach( $bySecond as $theSecond ) {
            $this->addBySecond( $theSecond );
        }
        return $this;
    }

    /**
     * @return int[]
     */
    public function getBySetPosition() : array
    {
        return $this->bySetPosition;
    }

    /**
     * @return int
     */
    public function getBySetPositionCont() : int
    {
        return count( $this->bySetPosition );
    }

    /**
     * @param int $bySetPosition
     * @return static
     */
    public function addBySetPosition( int $bySetPosition ) : static
    {
        $this->bySetPosition[] = $bySetPosition;
        return $this;
    }

    /**
     * @param int[] $bySetPosition
     * @return static
     */
    public function setBySetPosition( array $bySetPosition ) : static
    {
        foreach( $bySetPosition as $theSetPosition ) {
            $this->addBySetPosition( $theSetPosition );
        }
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCount() : ?int
    {
        return $this->count;
    }

    /**
     * Return bool true if count is not null
     *
     * @return bool
     */
    public function isCountSet() : bool
    {
        return ( null !== $this->count );
    }

    /**
     * @param int|null $count
     * @return static
     */
    public function setCount( ?int $count ) : static
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param null|bool $asString  default true
     * @return null|string|DateTimeInterface        DateTime with UTC, string without timezone suffix
     */
    public function getUntil( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        return (( $this->until instanceof DateTimeInterface ) && $asString )
            ? $this->until->format( self::$LocalDateTimeFMT )
            : $this->until;
    }

    /**
     * Return bool true if until is not null
     *
     * @return bool
     */
    public function isUntilSet() : bool
    {
        return ( null !== $this->until );
    }

    /**
     * @param null|string|DateTimeInterface $until
     * @return static
     * @throws Exception
     */
    public function setUntil( null|string|DateTimeInterface $until ) : static
    {
        $this->until = self::toUtcDateTime( $until ?? new DateTime());
        return $this;
    }
}
