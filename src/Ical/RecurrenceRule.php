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
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\PhpJsCalendar\Dto\RecurrenceRule as RecurrenceRuleDto;
use Kigkonsult\PhpJsCalendar\Dto\NDay           as NDayDto;

class RecurrenceRule extends BaseIcal
{
    /**
     * RecurrenceRule properties to iCal RECUR array
     *
     * @param RecurrenceRuleDto $recurrenceRuleDto
     * @param null|string $tzid
     * @return mixed[]
     * @throws Exception
     */
    public static function processTo( RecurrenceRuleDto $recurrenceRuleDto, ? string $tzid = null  ) : array
    {
        $recur = [];
        if( $recurrenceRuleDto->isFrequencySet()) {
            $recur[Vcalendar::FREQ] = $recurrenceRuleDto->getFrequency();
        }
        if( $recurrenceRuleDto->isIntervalSet()) {
            $recur[Vcalendar::INTERVAL] = $recurrenceRuleDto->getInterval( false );
        }
        if( $recurrenceRuleDto->isRscaleSet()) {
            $recur[Vcalendar::RSCALE] = $recurrenceRuleDto->getRscale( false );
        }
        if( $recurrenceRuleDto->isSkipSet()) {
            $recur[Vcalendar::SKIP] = $recurrenceRuleDto->getSkip( false );
        }
        if( $recurrenceRuleDto->isFirstDayOfWeekSet()) {
            $recur[Vcalendar::WKST] = $recurrenceRuleDto->getFirstDayOfWeek( false );
        }
        // array of "NDay[]"
        if( ! empty( $recurrenceRuleDto->getByDayCount())) {
            foreach( $recurrenceRuleDto->getByDay() as $x => $nday ) { // NO Ical\Nday
                $byDay = [];
                if( $nday->isNthOfPeriodSet()) { // not empty
                    $byDay[] = $nday->getNthOfPeriod();
                }
                if( $nday->isDaySet()) {
                    $byDay[Vcalendar::DAY] = $nday->getDay();
                }
                if( ! empty( $byDay )) {
                    $recur[Vcalendar::BYDAY][$x] = $byDay;
                }
            }
        }

        if( ! empty( $recurrenceRuleDto->getByMonthDayCount())) {
            foreach( $recurrenceRuleDto->getByMonthDay() as $x => $value ) {
                $recur[Vcalendar::BYMONTHDAY][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByMonthCount())) {
            foreach( $recurrenceRuleDto->getByMonth() as $x => $value ) {
                $recur[Vcalendar::BYMONTH][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByYearDayCount())) {
            foreach( $recurrenceRuleDto->getByYearDay() as $x => $value ) {
                $recur[Vcalendar::BYYEARDAY][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByWeekNoCount())) {
            foreach( $recurrenceRuleDto->getByWeekNo() as $x => $value ) {
                $recur[Vcalendar::BYWEEKNO][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByHourCount())) {
            foreach( $recurrenceRuleDto->getByHour() as $x => $value ) {
                $recur[Vcalendar::BYHOUR][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getByMinuteCount())) {
            foreach( $recurrenceRuleDto->getByMinute() as $x => $value ) {
                $recur[Vcalendar::BYMINUTE][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getBySecondCount())) {
            foreach( $recurrenceRuleDto->getBySecond() as $x => $value ) {
                $recur[Vcalendar::BYSECOND][$x] = $value;
            }
        }
        if( ! empty( $recurrenceRuleDto->getBySetPositionCont())) {
            foreach( $recurrenceRuleDto->getBySetPosition() as $x => $value ) {
                $recur[Vcalendar::BYSETPOS][$x] = $value;
            }
        }
        if( $recurrenceRuleDto->isCountSet()) { // Note empty ?!
            $recur[Vcalendar::COUNT] = $recurrenceRuleDto->getCount();
        }
        if( $recurrenceRuleDto->isUntilSet()) {
            $value     = $recurrenceRuleDto->getUntil();
            // is in localdate but iCal expects UTC (here DATE-TIME), if NO tzid, is in UTC?
            if( ! empty( $tzid )) {
                $value = new DateTime( $value, new DateTimeZone( $tzid ));
                $value->setTimezone( new DateTimeZone( Vcalendar::UTC ));
            }
            $recur[Vcalendar::UNTIL] = $value;
        }
        return $recur;
    }

    /**
     * Ical RECUR array to RecurrenceRule
     *
     * @param string[]|string[][] $recur
     * @param null|string $tzid
     * @return RecurrenceRuleDto
     * @throws Exception
     */
    public static function processFrom( array $recur, ? string $tzid = null ) : RecurrenceRuleDto
    {
        $dto = new RecurrenceRuleDto();
        if( isset( $recur[Vcalendar::FREQ] )) {
            $dto->setFrequency( $recur[Vcalendar::FREQ] );
        }
        if( isset( $recur[Vcalendar::INTERVAL] )) {
            $dto->setInterval((int) $recur[Vcalendar::INTERVAL] );
        }
        if( isset( $recur[Vcalendar::RSCALE] )) {
            $dto->setRscale( $recur[Vcalendar::RSCALE] );
        }
        if( isset( $recur[Vcalendar::SKIP] )) {
            $dto->setSkip( $recur[Vcalendar::SKIP] );
        }
        if( isset( $recur[Vcalendar::WKST] )) {
            $dto->setFirstDayOfWeek( $recur[Vcalendar::WKST] );
        }
        if( isset( $recur[Vcalendar::BYDAY] )) {
            foreach((array) $recur[Vcalendar::BYDAY] as $byDay ) { // NO Ical\Nday class
                $day = $nthOfPeriod = null;
                foreach( $byDay as $bydayKey => $bydayPart ) {
                    if( Vcalendar::DAY === $bydayKey ) {
                        $day = $bydayPart;
                    }
                    else {
                        $nthOfPeriod = $bydayPart;
                    }
                } // end foreach
                $dto->addByDay( NDayDto::factoryDay( $day, $nthOfPeriod ));
            } // end foreach
        } // end if byDay
        if( isset( $recur[Vcalendar::BYMONTH] )) {
            if( is_array( $recur[Vcalendar::BYMONTH] )) {
                foreach( $recur[Vcalendar::BYMONTH] as $month ) {
                    $dto->addByMonth( $month );
                }
            }
            else {
                $dto->addByMonth( $recur[Vcalendar::BYMONTH] );
            }
        }
        if( isset( $recur[Vcalendar::BYMONTHDAY] )) {
            foreach( $recur[Vcalendar::BYMONTHDAY] as $monthDay ) {
                $dto->addByMonthDay( $monthDay );
            }
        }
        if( isset( $recur[Vcalendar::BYYEARDAY] )) {
            foreach( $recur[Vcalendar::BYYEARDAY] as $yearDay ) {
                $dto->addByYearDay( $yearDay );
            }
        }
        if( isset( $recur[Vcalendar::BYWEEKNO] )) {
            foreach( $recur[Vcalendar::BYWEEKNO] as $weekNo ) {
                $dto->addByWeekNo( $weekNo );
            }
        }
        if( isset( $recur[Vcalendar::BYHOUR] )) {
            foreach( $recur[Vcalendar::BYHOUR] as $hour ) {
                $dto->addByHour( $hour );
            }
        }
        if( isset( $recur[Vcalendar::BYMINUTE] )) {
            foreach( $recur[Vcalendar::BYMINUTE] as $minute ) {
                $dto->addByMinute( $minute );
            }
        }
        if( isset( $recur[Vcalendar::BYSECOND] )) {
            foreach( $recur[Vcalendar::BYSECOND] as $second ) {
                $dto->addBySecond( $second );
            }
        }
        if( isset( $recur[Vcalendar::BYSETPOS] )) {
            foreach( $recur[Vcalendar::BYSETPOS] as $setByPos ) {
                $dto->addBySetPosition( $setByPos );
            }
        }
        if( isset( $recur[Vcalendar::COUNT] )) {
            $dto->setCount( $recur[Vcalendar::COUNT] );
        }
        if( isset( $recur[Vcalendar::UNTIL] )) {
            // is in UTC, localdate expected
            if( ! empty( $tzid )) {
                $recur[Vcalendar::UNTIL]
                    ->setTimezone( new DateTimeZone( $tzid ))
                    ->format( RecurrenceRuleDto::$LocalDateTimeFMT);
            }
            $dto->setUntil( $recur[Vcalendar::UNTIL] );
        }
        return $dto;
    }
}
