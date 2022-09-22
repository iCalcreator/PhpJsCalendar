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

use DateTime;
use Exception;
use Faker;
use Kigkonsult\PhpJsCalendar\Dto\Event       as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Participant as ParticipantDto;
use Kigkonsult\PhpJsCalendar\Dto\Task        as TaskDto;

abstract class BaseEventTask extends BaseGroupEventTask
{
    /**
     * Use faker to populate Event|Task
     *
     * @param EventDto|TaskDto $dto
     * @return DateTime  dtstart
     * @throws Exception
     */
    protected static function eventTaskLoad( EventDto|TaskDto $dto ) : DateTime
    {
        $faker = Faker\Factory::create();

        $dto->setDescription( $faker->sentence( 6, true ));
        $dto->setDescriptionContentType( $faker->mimeType());
        $dto->setExcluded(( 1 !== $faker->numberBetween( 1,3 )));

        $dto->addExcludedRecurrenceRule( RecurrenceRule::load());

        $dto->setFreeBusyStatus( $faker->randomElement( [ "free", "busy" ] ));

        $locationIds = [];
        $max = $faker->randomElement( [ 1, 2 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $locationIds[] = $faker->uuid;
        }
        foreach( $locationIds as $locationId ) {
            $dto->addLocation(
                $locationId,
                Location::load()
            );
        }

        $max = $faker->randomDigitNotNull();
        for( $x = 0; $x < $max; $x++ ) {
            $dto->addLocalization(
                $faker->languageCode(),
                PatchObject::load()
            );
        }

        $dto->setMethod( $faker->randomElement(
            [
                'PUBLISH',
                'REQUEST',
                'REPLY',
                'ADD',
                'CANCEL',
                'REFRESH',
            ]
        ));

        $participantIds = [];
        $max = $faker->randomElement( [ 4, 5 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $participantIds[$x] = $dto::getNewUid();
        }
        for( $x = 0; $x < 2; ++$x ) { // full load of Participant
            $dto->addParticipant(
                $participantIds[$x],
                Participant::load(
                    array_diff( $participantIds, [ $participantIds[$x] ] ),
                    $faker->randomElement( $locationIds )
                )
            );
        }
        for( $x = 2; $x < $max; ++$x ) { // base load of Participant
            $dto->addParticipant(
                $participantIds[$x],
                ParticipantDto::factory( $faker->email(), $faker->name() )
            );
        }

        $dto->setPriority( $faker->numberBetween( 0, 9 ));
        $dto->setPrivacy( $faker->randomElement( [ "public", "private", "secret" ] ));
        $dto->setRecurrenceId( $faker->dateTime());
        $dto->setRecurrenceIdTimeZone( $faker->timezone );

        $dto->addRecurrenceRule( RecurrenceRule::load());

        $max = $faker->randomElement( [ 1, 2 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $dto->addRecurrenceOverride(
                $faker->dateTime()->format( 'Y-m-d\TH:i:s' ),
                PatchObject::load()
            );
        }

        $max = $faker->randomElement( [ 1, 2 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $dto->addRelatedTo(
                $faker->uuid(),
                Relation::load()
            );
        }

        $max = $faker->randomElement( [ 1, 2 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $method = $faker->randomElement( [ "imip", "web", "other" ] );
            switch( $method ) {
                case 'imip' :
                    $dto->addReplyTo( $method, $faker->email() );
                    break;
                case 'web' :// fall through
                default :
                    $dto->addReplyTo( $method, $faker->url() );
                    break;
            }
        }

        $dto->setRequestStatus(
            number_format( $faker->randomFloat( 1, 1, 4.9 ), 2 ) . ';' .
            $faker->sentence( 3, true ) . ';' .
            $faker->sentence( 6, true )
        );
        $dto->setSentBy( $faker->email());
        $dto->setSequence(  $faker->randomNumber());

        $showWithoutTime = ( 1 === $faker->numberBetween( 0, 1 ));
        $dto->setShowWithoutTime( $showWithoutTime );
        $startDateTime   = $faker->dateTimeBetween( '+2 month', '+6 month' );
        $dto->setStart( $startDateTime );

        $timezonNames = $dto->getLocationsTimezones();
        if( ! $showWithoutTime ) {
            $timezonName = $faker->timezone();
            $dto->setTimeZone( $timezonName );
            if( ! in_array( $timezonName, $timezonNames, true )) {
                $timezonNames[] = $timezonName;
            }
        } // endif
        foreach( $timezonNames as $timezonName ) {
            $dto->addTimeZone( $timezonName, TimeZone::load( $timezonName ));
        }

        $virtualLocationIds = [];
        $max = $faker->randomElement( [ 2, 3 ] );
        for( $x = 0; $x < $max; $x++ ) {
            $virtualLocationIds[] = $faker->uuid();
        }
        foreach( $virtualLocationIds as $virtualLocationId ) {
            $dto->addVirtualLocation(
                $virtualLocationId,
                VirtualLocation::load()
            );
        }

        $dto->setUseDefaultAlerts(( 1 === $faker->numberBetween( 0, 1 )));
        $max = $faker->numberBetween( 3, 5 );
        for( $x = 0; $x < $max; $x++ ) {
            $dto->addAlert(
                $faker->uuid(),
                Alert::load((clone $startDateTime )->modify( ( 1 - $x ) . ' hours' ))
            );
        }

        return clone $startDateTime;
    }
}
