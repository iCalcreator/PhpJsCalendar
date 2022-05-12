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
use Kigkonsult\PhpJsCalendar\Dto\NDay as Dto;

class NDay extends BaseDtoLad
{
    /**
     * @var array|string[]
     */
    private static array $weekDays =[ "mo", "tu", "we", "th", "fr", "sa", "su" ];

    /**
     * Use faker to populate new NDay
     *
     * @param null|bool $withdayNo
     * @return Dto
     */
    public static function load( ? bool $withdayNo = true ) : Dto
    {
        $faker = Faker\Factory::create();
        $dto   = new Dto();
        $dto->setDay( $faker->randomElement( self::$weekDays ));
        if( $withdayNo ) {
            $dto->setNthOfPeriod( self::getNumberNotZero() );
        }
        return $dto;
    }

    private static function getNumberNotZero() : int
    {
        $faker = Faker\Factory::create();
        while( true ) {
            $number = $faker->numberbetween( -365, 365 );
            if( 0 !== $number ) {
                return $number;
            }
        }
    }

    /**
     * Sort NDays om weekday and, opt, nthOfPeriod
     *
     * @param Dto[] $NDays
     */
    public static function sort( array $NDays ) : array
    {
        $weekdays = array_flip( self::$weekDays );
        $output = [];
        foreach( $NDays as $nDay ) {
            $key   = $weekdays[$nDay->getDay()];
            $value = $nDay->getNthOfPeriod();
            if( empty( $value )) {
                $key .= '000';
            }
            else {
                $key .= $value += 1000;
            }
            $output[$key] = $nDay;
        }
        ksort( $output );
        return array_values( $output );
    }

}
