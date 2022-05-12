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
use Kigkonsult\FakerLocRelTypes\Provider\en_US\LocationTypes;
use Kigkonsult\PhpJsCalendar\Dto\Location as Dto;

class Location extends BaseDtoLad
{
    /**
     * Use faker to populate new Location
     *
     * @return Dto
     */
    public static function load() : Dto
    {
        $faker = Faker\Factory::create();
        $faker->addProvider( new LocationTypes( $faker ));

        $locationTypes = [];
        $max   = $faker->randomElement( [ 3, 4 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $locType = $faker->locationType();
            $locationTypes[$locType] = $locType;
        }

        $dto   = new Dto();

        $dto->setName( $faker->words( 3, true ));
        $dto->setDescription( $faker->words( 9, true ));

        foreach( $locationTypes as $locationType ) {
            $dto->addLocationType( $locationType );
        }

        $dto->setRelativeTo( $faker->randomElement( [ 'start', 'end' ] ));

        $dto->setTimeZone( $faker->timezone());

        if( 1 === $faker->randomElement( [ 1, 2 ] )) {
            $dto->setCoordinates(
                'geo:' .
                $faker->randomFloat( 6, -90, 90 ) .
                ',' .
                $faker->randomFloat( 6, -180, 180 )
            );
        }
        else {
            $dto->setLatLongCoordinates(
                $faker->randomFloat( 6, -90, 90 ),
                $faker->randomFloat( 6, -180, 180 )
            );
        }

        $max = $faker->randomElement( [ 2, 3 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $link = Link::load();
            $dto->addLink( $link->getCid(), $link );
        }
        return $dto;
    }
}
