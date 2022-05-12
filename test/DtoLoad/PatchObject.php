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
use Kigkonsult\PhpJsCalendar\Dto\PatchObject as Dto;

class PatchObject
{
    /**
     * Use faker to populate new PatchObject
     *
     * @return Dto
     */
    public static function load() : Dto
    {
        $faker = Faker\Factory::create();
        $dto   = new Dto();
        $dto->append( Dto::NAME, $faker->word );
        $dto->append( Dto::DESCRIPTION, $faker->words( 6, true ));
/*
        $dto->append( // ??
            Dto::LOCATIONS,
            [
                $faker->password,
                Location::load()
            ]
        );
*/
        /*
         "title": "Calculus I Exam",
         "start": "2020-06-25T10:00:00",
         "duration": "PT2H",
         "excluded": true
         "locations": {
           "auditorium": {
             "@type": "Location",
             "title": "Big Auditorium",
             "description": "Big Auditorium, Other Road"
           }

     "recurrenceOverrides": {
       "2020-01-07T14:00:00": {
         "title": "Introduction to Calculus I (optional)"
       },
       "2020-04-01T09:00:00": {
         "excluded": true
       },
       "2020-06-25T09:00:00": {
         "title": "Calculus I Exam",
         "start": "2020-06-25T10:00:00",
         "duration": "PT2H",
         "locations": {
           "auditorium": {
             "@type": "Location",
             "title": "Big Auditorium",
             "description": "Big Auditorium, Other Road"
           }
         }
       }
     }

     "localizations": {
       "de": {
         "title": "Live von der Music Bowl: The Band!",
         "description": "Schau dir das größte Musikereignis an!",
         "virtualLocations/vloc1/name":
           "Gratis Live-Stream aus der Music Bowl"
       }
     }

         */

        return $dto;
    }
}
