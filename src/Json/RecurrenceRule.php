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
namespace Kigkonsult\PhpJsCalendar\Json;

use Exception;
use Kigkonsult\PhpJsCalendar\Dto\RecurrenceRule as Dto;

class RecurrenceRule extends BaseJson
{
    /**
     * Parse json array to populate new RecurrenceRule
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::FREQUENCY] )) {
            $dto->setFrequency( $jsonArray[self::FREQUENCY] );
        }
        if( isset( $jsonArray[self::INTERVAL] )) {
            $dto->setInterval((int) $jsonArray[self::INTERVAL] );
        }
        if( isset( $jsonArray[self::RSCALE] )) {
            $dto->setRscale( $jsonArray[self::RSCALE] );
        }
        if( isset( $jsonArray[self::SKIP] )) {
            $dto->setSkip( $jsonArray[self::SKIP] );
        }
        if( isset( $jsonArray[self::FIRSTDAYOFWEEK] )) {
            $dto->setFirstDayOfWeek( $jsonArray[self::FIRSTDAYOFWEEK] );
        }
        if( isset( $jsonArray[self::BYDAY] )) {
            foreach( $jsonArray[self::BYDAY] as $nDay ) {
                $dto->addByDay( NDay::parse( $nDay ));
            }
        }
        if( isset( $jsonArray[self::BYMONTH] )) {
            foreach( $jsonArray[self::BYMONTH] as $month ) {
                $dto->addByMonth( $month );
            }
        }
        if( isset( $jsonArray[self::BYMONTHDAY] )) {
            foreach( $jsonArray[self::BYMONTHDAY] as $monthDay ) {
                $dto->addByMonthDay((int) $monthDay );
            }
        }
        if( isset( $jsonArray[self::BYYEARDAY] )) {
            foreach( $jsonArray[self::BYYEARDAY] as $yearDay ) {
                $dto->addByYearDay((int) $yearDay );
            }
        }
        if( isset( $jsonArray[self::BYWEEKNO] )) {
            foreach( $jsonArray[self::BYWEEKNO] as $weekNo ) {
                $dto->addByWeekNo((int) $weekNo );
            }
        }
        if( isset( $jsonArray[self::BYHOUR] )) {
            foreach( $jsonArray[self::BYHOUR] as $hour ) {
                $dto->addByHour((int) $hour );
            }
        }
        if( isset( $jsonArray[self::BYMINUTE] )) {
            foreach( $jsonArray[self::BYMINUTE] as $minute ) {
                $dto->addByMinute((int) $minute );
            }
        }
        if( isset( $jsonArray[self::BYSECOND] )) {
            foreach( $jsonArray[self::BYSECOND] as $second ) {
                $dto->addBySecond((int) $second );
            }
        }
        if( isset( $jsonArray[self::BYSETPOSITION] )) {
            foreach( $jsonArray[self::BYSETPOSITION] as $setByPos ) {
                $dto->addBySetPosition((int) $setByPos );
            }
        }
        if( isset( $jsonArray[self::COUNT] )) {
            $dto->setCount((int) $jsonArray[self::COUNT] );
        }
        if( isset( $jsonArray[self::UNTIL] )) {
            $dto->setUntil( $jsonArray[self::UNTIL] );
        }
        return $dto;
    }

    /**
     * Write RecurrenceRule Dto properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return array
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];

        if( $dto->isFrequencySet()) {
            $jsonArray[self::FREQUENCY] = $dto->getFrequency();
        }

        if( $dto->isIntervalSet()) { // Note not empty
            $jsonArray[self::INTERVAL] = $dto->getInterval();
        }

        if( $dto->isRscaleSet()) {
            $jsonArray[self::RSCALE] = $dto->getRscale();
        }

        if( $dto->isSkipSet()) {
            $jsonArray[self::SKIP] = $dto->getSkip();
        }

        if( $dto->isFirstDayOfWeekSet()) {
            $jsonArray[self::FIRSTDAYOFWEEK] = $dto->getFirstDayOfWeek();
        }

        // array of "NDay[]"
        if( ! empty( $dto->getByDayCount())) {
            foreach( $dto->getByDay() as $x => $nday ) {
                $jsonArray[self::BYDAY][$x] = (object)NDay::write( $nday );
            }
        }
        if( ! empty( $dto->getByMonthDayCount())) {
            foreach( $dto->getByMonthDay() as $x => $value ) {
                $jsonArray[self::BYMONTHDAY][$x] = $value;
            }
        }
        if( ! empty( $dto->getByMonthCount())) {
            foreach( $dto->getByMonth() as $x => $value ) {
                $jsonArray[self::BYMONTH][$x] = $value;
            }
        }
        if( ! empty( $dto->getByYearDayCount())) {
            foreach( $dto->getByYearDay() as $x => $value ) {
                $jsonArray[self::BYYEARDAY][$x] = $value;
            }
        }
        if( ! empty( $dto->getByWeekNoCount())) {
            foreach( $dto->getByWeekNo() as $x => $value ) {
                $jsonArray[self::BYWEEKNO][$x] = $value;
            }
        }
        if( ! empty( $dto->getByHourCount())) {
            foreach( $dto->getByHour() as $x => $value ) {
                $jsonArray[self::BYHOUR][$x] = $value;
            }
        }
        if( ! empty( $dto->getByMinuteCount())) {
            foreach( $dto->getByMinute() as $x => $value ) {
                $jsonArray[self::BYMINUTE][$x] = $value;
            }
        }
        if( ! empty( $dto->getBySecondCount())) {
            foreach( $dto->getBySecond() as $x => $value ) {
                $jsonArray[self::BYSECOND][$x] = $value;
            }
        }
        if( ! empty( $dto->getBySetPositionCont())) {
            foreach( $dto->getBySetPosition() as $x => $value ) {
                $jsonArray[self::BYSETPOSITION][$x] = $value;
            }
        }
        if( $dto->isCountSet()) { // Note empty !!
            $jsonArray[self::COUNT] = $dto->getCount();
        }

        if( $dto->isUntilSet()) {
            $jsonArray[self::UNTIL] = $dto->getUntil();
        }

        return $jsonArray;
    }
}
