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
use Kigkonsult\PhpJsCalendar\Dto\Participant as Dto;

class Participant extends BaseDtoLad
{
    /**
     * Use faker to populate new Participant
     *
     * @param string[] $otherPuids  participant id's
     * @param string $randomLocationId
     * @return Dto
     * @throws Exception
     */
    public static function load( array $otherPuids, string $randomLocationId ) : Dto
    {
        $faker = Faker\Factory::create();
        $dto   = new Dto();
        $dto->setName( $faker->name());
        $dto->setEmail( $faker->unique()->email());
        $dto->setDescription( $faker->words(6, true ));

        $dto->addSendTo('imip', $faker->unique()->email());
        $dto->addSendTo('other', $faker->url());

        $dto->setKind( $faker->randomElement( [ 'individual', 'group', 'location', 'resource', ] ));
        foreach( $faker->randomElements(
            [ 'owner', 'attendee', 'optional', 'informational', 'chair', 'contact' ],
            2
        ) as $role ) {
             $dto->addRole( $role );
        }
        $dto->setLocationId( $randomLocationId );
        $dto->setLanguage( $faker->languageCode());
        $dto->setParticipationStatus(
            $faker->randomElement( [ 'needs-action', 'accepted', 'declined', 'resourceresource', 'delegated' ] )
        );
        $dto->setParticipationComment( $faker->words( 6, true ));
        $dto->setExpectReply( $faker->boolean());
        $dto->setScheduleAgent( $faker->randomElement( [ 'server', 'client', 'none', ] ));
        $dto->setScheduleForceSend( $faker->boolean());
        $dto->setScheduleSequence( $faker->randomDigitNot( 0 ));
        for( $x = 0; $x < 2; $x++ ) {
             $dto->addScheduleStatus(
                 number_format( $faker->randomFloat( 2, 1, 4.9 ), 2 ) .
                 ';' .
                 $faker->words( 5, true )
             );
        }
        $dto->setScheduleUpdated( $faker->dateTime( 'now', 'UTC' ));
        $dto->setSentBy( $faker->email());

        $dto->setInvitedBy( $faker->randomElement( $otherPuids ));
        for( $x = 0; $x < 2; $x++ ) {
            $dto->addDelegatedTo( $faker->randomElement( $otherPuids ));
        }
        for( $x = 0; $x < 2; $x++ ) {
            $dto->addDelegatedFrom( $faker->randomElement( $otherPuids ));
        }
        for( $x = 0; $x < 2; $x++ ) {
            $dto->addMemberOf( $faker->randomElement( $otherPuids ));
        }

        $max = $faker->randomElement( [ 2, 3 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $link = Link::load();
            $dto->addLink( $link->getCid(), $link );
        }

        $dto->setProgress( $faker->randomElement( [ 'completed', 'failed', 'in-process', 'needs-action' ] ));
        $dto->setProgressUpdated( $faker->dateTime( 'now', 'UTC' ));
        $dto->setPercentComplete( $faker->numberBetween( 0, 100 ));
        return $dto;
    }
}
