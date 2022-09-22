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
use Kigkonsult\FakerLocRelTypes\Provider\en_US\Rfc8288RelationTypes;
use Kigkonsult\PhpJsCalendar\Dto\Link as Dto;

class Link extends BaseDtoLad
{
    /**
     * Use faker to populate new Link (cid auto set to uuid)
     *
     * @return Dto
     */
    public static function load() : Dto
    {
        $faker = Faker\Factory::create();
        $faker->addProvider( new Rfc8288RelationTypes( $faker ));

        $linkRelations = [];
        $max   = $faker->randomElement( [ 3, 4] );
        for( $x = 0; $x < $max; $x++ ) {
            $relType = $faker->rfc8288RelationType();
            $linkRelations[$relType] = $relType;
        }

        $dto   = Dto::factoryHref( $faker->url());
        $dto->setContentType( $faker->mimeType());
        $dto->setSize( $faker->randomNumber( 5, true ));
        $dto->setRel( $faker->randomElement( array_values( $linkRelations )));
        $dto->setDisplay( $faker->randomElement( [ 'badge', 'graphic', 'fullsize',  'thumbnail' ] ));
        $dto->setTitle( $faker->words( 3, true ));

        return $dto;
    }
}
