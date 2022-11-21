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
namespace Kigkonsult\PhpJsCalendar\Ical;

use DateTime;
use DateTimeZone;
use Exception;
use Kigkonsult\Icalcreator\Vcalendar            as IcalVcalendar;
use Kigkonsult\PhpJsCalendar\Dto\NDay           as NDayDto;
use Kigkonsult\PhpJsCalendar\Dto\RecurrenceRule as RecurrenceRuleDto;

class RecurrenceRule extends BaseIcal
{
    /**
     * RecurrenceRule properties to iCal RECUR array
     *
     * @param RecurrenceRuleDto $recurrenceRuleDto
     * @param null|string $tzid
     * @return array
     * @throws Exception
     */
    public static function processToIcalRecur(
        RecurrenceRuleDto $recurrenceRuleDto,
        ? string $tzid = null
    ) : array
    {
        $recur = [];
        if( $recurrenceRuleDto->isFrequencySet()) {
            $recur[IcalVcalendar::FREQ] = $recurrenceRuleDto->getFrequency();
        }
        if( $recurrenceRuleDto->isIntervalSet()) {
            $recur[IcalVcalendar::INTERVAL] = $recurrenceRuleDto->getInterval( false );
        }
        if( $recurrenceRuleDto->isRscaleSet()) {
            $recur[IcalVcalendar::RSCALE] = $recurrenceRuleDto->getRscale( false );
        }
        if( $recurrenceRuleDto->isSkipSet()) {
            $recur[IcalVcalendar::SKIP] = $recurrenceRuleDto->getSkip( false );
        }
        if( $recurrenceRuleDto->isFirstDayOfWeekSet()) {
            $recur[IcalVcalendar::WKST] = $recurrenceRuleDto->getFirstDayOfWeek( false );
        }
        if( ! empty( $recurrenceRuleDto->getByDayCount())) {
            self::extractNdayDtos( $recurrenceRuleDto->getByDay(),$recur );
        }
        if( ! empty( $recurrenceRuleDto->getByMonthDayCount())) {
            foreach( $recurrenceRuleDto->getByMonthDay() as $x => $value ) {
                $recur[IcalVcalendar::BYMONTHDAY][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByMonthCount())) {
            foreach( $recurrenceRuleDto->getByMonth() as $x => $value ) {
                $recur[IcalVcalendar::BYMONTH][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByYearDayCount())) {
            foreach( $recurrenceRuleDto->getByYearDay() as $x => $value ) {
                $recur[IcalVcalendar::BYYEARDAY][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByWeekNoCount())) {
            foreach( $recurrenceRuleDto->getByWeekNo() as $x => $value ) {
                $recur[IcalVcalendar::BYWEEKNO][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByHourCount())) {
            foreach( $recurrenceRuleDto->getByHour() as $x => $value ) {
                $recur[IcalVcalendar::BYHOUR][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByMinuteCount())) {
            foreach( $recurrenceRuleDto->getByMinute() as $x => $value ) {
                $recur[IcalVcalendar::BYMINUTE][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getBySecondCount())) {
            foreach( $recurrenceRuleDto->getBySecond() as $x => $value ) {
                $recur[IcalVcalendar::BYSECOND][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getBySetPositionCont())) {
            foreach( $recurrenceRuleDto->getBySetPosition() as $x => $value ) {
                $recur[IcalVcalendar::BYSETPOS][$x] = $value;
            }
        }
        if( $recurrenceRuleDto->isCountSet()) { // Note empty ?!
            $recur[IcalVcalendar::COUNT] = $recurrenceRuleDto->getCount();
        }
        if( $recurrenceRuleDto->isUntilSet()) {
            self::extractUntil( $recurrenceRuleDto->getUntil(), $tzid, $recur );
        } // end if
        return $recur;
    }

    /**
     * @param NDayDto[] $nDays
     * @param array $recur
     */
    private static function extractNdayDtos(
        array $nDays,
        array & $recur
    ) : void
    {
        foreach( $nDays as $x => $nDay ) {
            $byDay = [];
            if( $nDay->isNthOfPeriodSet()) { // not empty
                $byDay[] = $nDay->getNthOfPeriod();
            }
            if( $nDay->isDaySet()) {
                $byDay[IcalVcalendar::DAY] = $nDay->getDay();
            }
            if( ! empty( $byDay )) {
                $recur[IcalVcalendar::BYDAY][$x] = $byDay;
            }
        } // end foreach
    }

    /**
     * Extract recur until
     *
     * Is in localdate but iCal expects UTC (as DATE-TIME), if NO tzid, is in UTC?
     *
     * @param string $until
     * @param string|null $tzid
     * @param array $recur
     * @throws Exception
     */
    private static function extractUntil( string $until, ? string $tzid, array & $recur ) : void
    {
        if( empty( $tzid )) {
            $recur[IcalVcalendar::UNTIL] = $until;
            return;
        }
        $value = new DateTime( $until, new DateTimeZone( $tzid ));
        $value->setTimezone( new DateTimeZone( IcalVcalendar::UTC ));
        $recur[IcalVcalendar::UNTIL] = $value;
    }

    /**
     * Ical RECUR array to RecurrenceRule
     *
     * @param string[]|string[][] $recur
     * @param null|string $tzid
     * @return RecurrenceRuleDto
     * @throws Exception
     */
    public static function processFromIcalRecur( array $recur, ? string $tzid = null ) : RecurrenceRuleDto
    {
        $dto = new RecurrenceRuleDto();
        if( isset( $recur[IcalVcalendar::FREQ] )) {
            $dto->setFrequency( $recur[IcalVcalendar::FREQ] );
        }
        if( isset( $recur[IcalVcalendar::INTERVAL] )) {
            $dto->setInterval((int) $recur[IcalVcalendar::INTERVAL] );
        }
        if( isset( $recur[IcalVcalendar::RSCALE] )) {
            $dto->setRscale( $recur[IcalVcalendar::RSCALE] );
        }
        if( isset( $recur[IcalVcalendar::SKIP] )) {
            $dto->setSkip( $recur[IcalVcalendar::SKIP] );
        }
        if( isset( $recur[IcalVcalendar::WKST] )) {
            $dto->setFirstDayOfWeek( $recur[IcalVcalendar::WKST] );
        }
        if( isset( $recur[IcalVcalendar::BYDAY] )) {
            self::extractIcalBydays( $recur[IcalVcalendar::BYDAY], $dto );
        }
        if( isset( $recur[IcalVcalendar::BYMONTH] )) {
            self::extractIcalBymonths( $recur[IcalVcalendar::BYMONTH], $dto );
        }
        if( isset( $recur[IcalVcalendar::BYMONTHDAY] )) {
            foreach( $recur[IcalVcalendar::BYMONTHDAY] as $monthDay ) {
                $dto->addByMonthDay((int) $monthDay );
            }
        }
        if( isset( $recur[IcalVcalendar::BYYEARDAY] )) {
            foreach( $recur[IcalVcalendar::BYYEARDAY] as $yearDay ) {
                $dto->addByYearDay((int) $yearDay );
            }
        }
        if( isset( $recur[IcalVcalendar::BYWEEKNO] )) {
            foreach( $recur[IcalVcalendar::BYWEEKNO] as $weekNo ) {
                $dto->addByWeekNo((int) $weekNo );
            }
        }
        if( isset( $recur[IcalVcalendar::BYHOUR] )) {
            foreach( $recur[IcalVcalendar::BYHOUR] as $hour ) {
                $dto->addByHour((int) $hour );
            }
        }
        if( isset( $recur[IcalVcalendar::BYMINUTE] )) {
            foreach( $recur[IcalVcalendar::BYMINUTE] as $minute ) {
                $dto->addByMinute((int) $minute );
            }
        }
        if( isset( $recur[IcalVcalendar::BYSECOND] )) {
            foreach( $recur[IcalVcalendar::BYSECOND] as $second ) {
                $dto->addBySecond((int) $second );
            }
        }
        if( isset( $recur[IcalVcalendar::BYSETPOS] )) {
            foreach( $recur[IcalVcalendar::BYSETPOS] as $setByPos ) {
                $dto->addBySetPosition((int) $setByPos );
            }
        }
        if( isset( $recur[IcalVcalendar::COUNT] )) {
            $dto->setCount((int) $recur[IcalVcalendar::COUNT] );
        }
        if( isset( $recur[IcalVcalendar::UNTIL] )) {
            if( ! empty( $tzid )) {// is in UTC, localdate expected
                $recur[IcalVcalendar::UNTIL]
                    ->setTimezone( new DateTimeZone( $tzid ))
                    ->format( RecurrenceRuleDto::$LocalDateTimeFMT);
            }
            $dto->setUntil( $recur[IcalVcalendar::UNTIL] );
        }
        return $dto;
    }

    /**
     * @param int|string|array $byDays
     * @param RecurrenceRuleDto $dto
     */
    private static function extractIcalBydays( int|string|array $byDays, RecurrenceRuleDto $dto ) : void
    {
        foreach((array) $byDays as $byDay ) { // NO Ical\Nday class
            $day = $nthOfPeriod = null;
            foreach( $byDay as $bydayKey => $bydayPart ) {
                if( IcalVcalendar::DAY === $bydayKey ) {
                    $day = $bydayPart;
                }
                else {
                    $nthOfPeriod = $bydayPart;
                }
            } // end foreach
            $dto->addByDay( NDayDto::factoryDay( $day, $nthOfPeriod ));
        } // end foreach
    }

    /**
     * @param int|string|array $byMonths
     * @param RecurrenceRuleDto $dto
     */
    private static function extractIcalBymonths( int|string|array $byMonths, RecurrenceRuleDto $dto ) : void
    {
        if( is_array( $byMonths )) {
            foreach( $byMonths as $month ) {
                $dto->addByMonth( $month );
            }
        }
        else {
            $dto->addByMonth( $byMonths );
        }
    }
}
