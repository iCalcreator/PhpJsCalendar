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
namespace Kigkonsult\PhpJsCalendar\DtoLoad;

use Faker;
use Kigkonsult\PhpJsCalendar\Dto\RecurrenceRule as Dto;

class RecurrenceRule extends BaseDtoLad
{
    /**
     * Use faker to populate new RecurrenceRule
     *
     * Kigkonsult\Icalcreator\Util\RecurFactory2::assertRecur() rules are applied
     *
     * @return Dto
     */
    public static function load() : Dto
    {
        $faker = Faker\Factory::create();
        $dto   = new Dto();
        $freq  = $faker->randomElement( [ 'yearly', 'monthly', 'weekly', 'daily', 'hourly', 'minutely', 'secondly' ] );
        $dto->setFrequency( $freq );
        /*
         *   '#7 The BYWEEKNO rule part MUST NOT be used ' .
         *   'when the FREQ rule part is set to anything other than YEARLY.';
         */
        $weekNos = (( 1 === $faker->randomElement( range( 1, 2 ))) &&
            ( $freq === 'yearly' ))
            ? $faker->randomElements( range( '1', '52' ), 3 )
            : [];
        sort( $weekNos );

        $dto->setInterval( $faker->randomDigitNotNull());
        $dto->setRscale( $faker->randomElement( [ 'gregory', 'gregorian', 'other' ] ));
        $dto->setSkip( $faker->randomElement( [ 'omit', 'backward', 'forward' ] ));
        $dto->setFirstDayOfWeek( $faker->randomElement( [ 'su', 'mo' ] ));

        $withDayNo =
            /*
             *     '#3 The BYDAY rule part MUST NOT ' .
             *     'be specified with a numeric value ' .
             *     'when the FREQ rule part is not set to MONTHLY or YEARLY. ';
             */
            ( ! in_array( $freq, [ 'yearly', 'monthly' ] )) &&
            /*
             *     '#4 The BYDAY rule part MUST NOT ' .
             *     'be specified with a numeric value ' .
             *     'with the FREQ rule part set to YEARLY ' .
             *     'when the BYWEEKNO rule part is specified. ';
             */
            (( $freq === 'yearly' ) && ! empty( $weekNos ));
        $list = [];
        for( $x = 0; $x < 3; $x++ ) {
            $list[] = ( NDay::load( $withDayNo ));
        }
        foreach( NDay::sort( $list ) as $nDay ) {
            $dto->addByDay( $nDay );
        }

        $list = $faker->randomElements( range( '1', '12' ), 3 );
        sort( $list );
        foreach( $list as $month ) {
            $dto->addByMonth( $month );
        }

        if( $freq !== 'weekly' ) {
            /*
             *     '#5 The BYMONTHDAY rule part MUST NOT be specified ' .
             *     'when the FREQ rule part is set to WEEKLY. '
             */
            $list = $faker->randomElements( range( '1', '28' ), 3 );
            sort( $list );
            foreach( $list as $monthDay ) {
                $dto->addByMonthDay( $monthDay );
            }
        }
        if( ! in_array( $freq, [ 'daily', 'monthly', 'weekly' ] )) {
            /*
             *     '#6 The BYYEARDAY rule part MUST NOT be specified ' .
             *     'when the FREQ rule part is set to DAILY, WEEKLY, or MONTHLY. ';
             */
            $list = $faker->randomElements( range( '1', '365' ), 3 );
            sort( $list );
            foreach( $list as $yearDay ) {
                $dto->addByYearDay( $yearDay );
            }
        }

        foreach( $weekNos as $weekNo ) {
            $dto->addByWeekNo( $weekNo );
        }

        $list = $faker->randomElements( range( '1', '23' ), 3 );
        sort( $list );
        foreach( $list as $hour ) {
            $dto->addByHour( $hour );
        }

        $list = $faker->randomElements( range( '1', '59' ), 3 );
        sort( $list );
        foreach( $list as $minute ) {
            $dto->addByMinute( $minute );
        }

        $list = $faker->randomElements( range( '1', '59' ), 3 );
        sort( $list );
        foreach( $list as $second ) {
            $dto->addBySecond( $second );
        }

        $list = $faker->randomElements( [ -3, -2, -1, 1, 2, 3 ], 2 );
        sort( $list );
        foreach( $list as $setByPos ) {
            $dto->addBySetPosition( $setByPos );
        }

        $dto->setCount( $faker->NumberBetween( 10, 20 ));

        $dto->setUntil( $faker->dateTime( '+1 year', 'UTC' ));

        return $dto;
    }
}
