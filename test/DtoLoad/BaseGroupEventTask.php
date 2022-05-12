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

use Exception;
use Faker;
use Kigkonsult\PhpJsCalendar\Dto\Group as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Task  as TaskDto;

abstract class BaseGroupEventTask extends BaseDtoLad
{
    /**
     * Use faker to populate Group|Event|Task
     *
     * @param GroupDto|EventDto|TaskDto $dto
     * @throws Exception
     */
    protected static function groupEventTaskLoad( GroupDto|EventDto|TaskDto $dto ) : void
    {
        $faker = Faker\Factory::create();
        $dto->setUid( $faker->uuid());

        $dateTime = $faker->dateTime( '-1 month');
        $dto->setCreated( $dateTime );
        $dto->setUpdated( clone( $dateTime )->modify( '+1 week'));

        $dto->setTitle( $faker->words( 5, true ));
        $max = $faker->randomDigitNotNull();
        for( $x = 0; $x < $max; $x++ ) {
            $dto->addCategory( $faker->word());
        }
        $dto->setColor( $faker->safeHexColor());
        $max = $faker->randomElement( [ 2, 3 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $link = Link::load();
            $dto->addLink( $link->getCid(), $link );
        }
        $max = $faker->randomDigitNotNull();
        for( $x = 0; $x < $max; $x++ ) {
            $dto->addKeyword( $faker->word());
        }
        $dto->setLocale( $faker->locale());
    }
}
