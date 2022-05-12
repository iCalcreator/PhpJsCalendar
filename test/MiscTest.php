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
namespace Kigkonsult\PhpJsCalendar;

use DateTime;
use Faker;
use Kigkonsult\PhpJsCalendar\Dto\Alert;
use Kigkonsult\PhpJsCalendar\Dto\Event;
use Kigkonsult\PhpJsCalendar\Dto\Group;
use Kigkonsult\PhpJsCalendar\Dto\Link;
use Kigkonsult\PhpJsCalendar\Dto\Location;
use Kigkonsult\PhpJsCalendar\Dto\OffsetTrigger;
use Kigkonsult\PhpJsCalendar\Dto\Participant;
use Kigkonsult\PhpJsCalendar\Dto\PatchObject;
use Kigkonsult\PhpJsCalendar\Dto\RecurrenceRule;
use Kigkonsult\PhpJsCalendar\Dto\TimeZone;
use Kigkonsult\PhpJsCalendar\Dto\TimeZoneRule;
use Kigkonsult\PhpJsCalendar\Dto\VirtualLocation;
use Kigkonsult\PhpJsCalendar\DtoLoad\Event          as EventLoader;
use Kigkonsult\PhpJsCalendar\DtoLoad\Group          as GroupLoader;
use Kigkonsult\PhpJsCalendar\DtoLoad\Location       as LocationLoader;
use Kigkonsult\PhpJsCalendar\DtoLoad\OffsetTrigger  as OffsetTriggerLoader;
use Kigkonsult\PhpJsCalendar\DtoLoad\Participant    as ParticipantLoader;
use Kigkonsult\PhpJsCalendar\DtoLoad\RecurrenceRule as RecurrenceRuleLoader;
use PHPUnit\Framework\TestCase;

/**
 * Class MiscTest
 */
class MiscTest extends TestCase
{
    /**
     * Testing BaseEventTask set-methods
     *
     * @test
     */
    public function baseEventTaskTest() : void
    {
        $event1 = EventLoader::load();
        $event2 = new Event();

        $this->assertNull( $event2->getDescriptionContentType( true ));
        $event2->setDescription( 'description' );
        $this->assertSame( Event::$descriptionContentTypeDefault, $event2->getDescriptionContentType( true ));

        $this->assertSame( Event::$excludedDefault,         $event2->getExcluded( true ));
        $this->assertSame( Event::$freeBusyStatusDefault,   $event2->getFreeBusyStatus( true ));
        $this->assertSame( Event::$priorityDefault,         $event2->getPriority( true ));
        $this->assertSame( Event::$privacyDefault,          $event2->getPrivacy( true ));
        $this->assertSame( Event::$sequenceDefault,         $event2->getSequence( true ));
        $this->assertSame( Event::$showWithoutTimeDefault,  $event2->getShowWithoutTime( true ));
        $this->assertSame( Event::$useDefaultAlertsDefault, $event2->getUseDefaultAlerts( true ));

        $this->assertSame( Event::$durationDefault,         $event2->getDuration( true, true ));
        $this->assertSame( Event::$statusDefault,           $event2->getStatus( true ));

        $this->assertsame(
            'P1W',
            $event2->setDuration( 'P7D' )->getDuration()
        );

        if( ! empty( $event1->getRecurrenceRulesCount())) {
            $this->assertNotEmpty(
                $event2->setRecurrenceRules( $event1->getRecurrenceRules())->getRecurrenceRules()
            );
        }

        if( ! empty( $event1->getExcludedRecurrenceRulesCount())) {
            $this->assertNotEmpty(
                $event2->setExcludedRecurrenceRules( $event1->getExcludedRecurrenceRules())->getExcludedRecurrenceRules()
            );
        }

        if( ! empty( $event1->getLocalizationsCount())) {
            $this->assertNotEmpty(
                $event2->setLocalizations( $event1->getLocalizations())->getLocalizations()
            );
        }

        if( ! empty( $event1->getLocationsCount())) {
            $this->assertNotEmpty(
                $event2->setLocations( $event1->getLocations())->getLocations()
            );
        }

        if( ! empty( $event1->getParticipantsCount())) {
            $this->assertNotEmpty(
                $event2->setParticipants( $event1->getParticipants())->getParticipants()
            );
        }

        if( ! empty( $event1->getRecurrenceOverrides())) {
            $this->assertNotEmpty(
                $event2->setRecurrenceOverrides( $event1->getRecurrenceOverrides())->getRecurrenceOverrides()
            );
        }

        if( ! empty( $event1->getRelatedToCount())) {
            $this->assertNotEmpty(
                $event2->setRelatedTo( $event1->getRelatedTo())->addRelatedTo( 'uid', 'relationType' )->getRelatedTo()
            );
        }

        if( ! empty( $event1->getReplyToCount())) {
            $this->assertNotEmpty(
                $event2->setReplyTo( $event1->getReplyTo())->getReplyTo()
            );
        }

        if( ! empty( $event1->getTimeZonesCount())) {
            $this->assertNotEmpty(
                $event2->setTimeZones( $event1->getTimeZones())->getTimeZones()
            );
        }

        $event2->addVirtualLocation( new VirtualLocation());
        $this->assertNotEmpty(
            $event2->setVirtualLocations( $event1->getVirtualLocations())->getVirtualLocations()
        );
        $this->assertNotEmpty(
            $event2->setAlerts( $event1->getAlerts())->getAlerts()
        );

    }

    /**
     * Testing BaseGroupEventTask set-methods
     *
     * @test
     */
    public function baseGroupEventTaskTest() : void
    {
        $faker = Faker\Factory::create();

        $event1 = EventLoader::load();
        $event2 = new Event();

        $categories   = $event1->getCategories();
        $categories[] = $faker->word();
        $this->assertNotEmpty(
            $event2->setCategories( $categories )->getCategories()
        );

        $keywords   = $event1->getKeywords();
        $keywords[] = $faker->word();
        $this->assertNotEmpty(
            $event2->setKeywords( $keywords )->getKeywords()
        );

        $this->assertNotEmpty(
            $event2->setLinks( $event1->getLinks())->addLink( new Link())->getLinks()
        );
    }

    /**
     * Testing Group set-method
     *
     * @test
     */
    public function groupTest() : void
    {
        $group1 = GroupLoader::load();
        $group2 = new Group();

        $this->assertNotEmpty(
            $group2->setEntries( $group1->getEntries())->getEntries()
        );
    }

    /**
     * Testing Alert
     *
     * @test
     */
    public function alertTest() : void
    {
        $alert = new Alert();
        $this->assertSame( Alert::$actionDefault, $alert->getAction( true ));
    }

    /**
     * Testing Location set-method
     *
     * @test
     */
    public function locationTest() : void
    {
        $location1 = LocationLoader::load();
        $location2 = new Location();

        $this->assertSame( Location::$relativeToDefault, $location2->getRelativeTo( true ));

        $locationTypes   = $location1->getLocationTypes();
        $locationTypes[] = 'locationype';
        $this->assertNotEmpty(
            $location2->setLocationTypes( $locationTypes )->getLocationTypes()
        );
    }

    /**
     * Testing OffsetTrigger set-method etc
     *
     * @test
     */
    public function offsetTriggerTest() : void
    {
        $offsetTrigger1 = OffsetTriggerLoader::load();
        $offsetTrigger2 = new OffsetTrigger();

        $this->assertSame( OffsetTrigger::$relativeToDefault, $offsetTrigger2->getRelativeTo( true ));

        $this->assertTrue( $offsetTrigger1->isOffsetSet());
        $this->assertNotEmpty(
            $offsetTrigger2->setOffset( $offsetTrigger1->getOffset( true ))->getOffset()
        );
    }

    /**
     * Testing Participant set-methods
     *
     * @test
     */
    public function participantTest() : void
    {
        $faker = Faker\Factory::create();

        $participant1 = ParticipantLoader::load(
            [ Participant::getNewUid(), Participant::getNewUid() ],
            Participant::getNewUid()
        );
        $participant2 = new Participant();

        $this->assertSame( Participant::$participationStatusDefault, $participant2->getParticipationStatus( true ));
        $this->assertSame( Participant::$expectReplyDefault, $participant2->getExpectReply( true ));
        $this->assertSame( Participant::$scheduleAgentDefault, $participant2->getScheduleAgent( true ));
        $this->assertSame( Participant::$scheduleForceSendDefault, $participant2->getScheduleForceSend( true ));
        $this->assertSame( Participant::$scheduleSequenceDefault, $participant2->getScheduleSequence( true ));

        $this->assertNotEmpty( $participant1->getSendToMethods() );

        if( ! empty( $participant1->getSendToCount())) {
            $this->assertNotEmpty(
                $participant2->setSendTo( $participant1->getSendTo())->getSendTo()
            );
        }

        if( ! empty( $participant1->getRolesCount())) {
            $this->assertNotEmpty(
                $participant2->setRoles( $participant1->getRoles())->getRoles()
            );
        }

        if( ! empty( $participant1->getScheduleStatusCount())) {
            $this->assertNotEmpty(
                $participant2->setScheduleStatus( $participant1->getScheduleStatus())->getScheduleStatus()
            );
        }

        $delegatedTo   = $participant1->getDelegatedTo();
        $delegatedTo[] = $faker->email();
        $this->assertNotEmpty(
            $participant2->setDelegatedTo( $delegatedTo )->getDelegatedTo()
        );

        $delegatedFrom   = $participant1->getDelegatedFrom();
        $delegatedFrom[] = $faker->email();
        $this->assertNotEmpty(
            $participant2->setDelegatedFrom( $delegatedFrom )->getDelegatedFrom()
        );

        $memberOf   = $participant1->getMemberOf();
        $memberOf[] = $faker->email();
            $this->assertNotEmpty(
            $participant2->setMemberOf( $memberOf )->getMemberOf()
        );
    }

    /**
     * Testing PatchObject pointer (offset) methods
     *
     * @test
     */
    public function patchObjectTest() : void
    {
        $pointer1 = 'pointer1';
        $pointer2 = 'pointer2';

        $patchObject = PatchObject::factory( [ $pointer1 => 1, $pointer2 => 2 ] );

        $this->assertTrue( $patchObject->isPointerSet( $pointer1 ));
        $this->assertSame( 1, $patchObject->getPointerValue( $pointer1 ));

        $patchObject->removePointer( $pointer1 );
        $this->assertFalse( $patchObject->isPointerSet( $pointer1 ));
    }

    /**
     * Testing RecurrenceRule set-methods
     *
     * @test
     */
    public function recurrenceRuleTest() : void
    {
        $recurrenceRule1 = RecurrenceRuleLoader::load();
        $recurrenceRule2 = new RecurrenceRule();

        $this->assertSame( RecurrenceRule::$intervalDefault, $recurrenceRule2->getInterval( true ));
        $this->assertSame( RecurrenceRule::$rscaleDefault, $recurrenceRule2->getRscale( true ));
        $this->assertSame( RecurrenceRule::$skipDefault, $recurrenceRule2->getSkip( true ));
        $this->assertSame( RecurrenceRule::$firstDayOfWeekDefault, $recurrenceRule2->getFirstDayOfWeek( true ));

        if( ! empty( $recurrenceRule1->getByDayCount())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setByDay( $recurrenceRule1->getByDay())->getByDay()
            );
        }

        if( ! empty( $recurrenceRule1->getByMonthCount())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setByMonth( $recurrenceRule1->getByMonth())->getByMonth()
            );
        }

        if( ! empty( $recurrenceRule1->getByWeekNoCount())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setByWeekNo( $recurrenceRule1->getByWeekNo())->getByWeekNo()
            );
        }

        if( ! empty( $recurrenceRule1->getByMonthDayCount())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setByMonthDay( $recurrenceRule1->getByMonthDay())->getByMonthDay()
            );
        }

        if( ! empty( $recurrenceRule1->getByYearDay())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setByYearDay( $recurrenceRule1->getByYearDay())->getByYearDay()
            );
        }

        if( ! empty( $recurrenceRule1->getByHourCount())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setByHour( $recurrenceRule1->getByHour())->getByHour()
            );
        }

        if( ! empty( $recurrenceRule1->getByMinuteCount())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setByMinute( $recurrenceRule1->getByMinute())->getByMinute()
            );
        }

        if( ! empty( $recurrenceRule1->getBySecondCount())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setBySecond( $recurrenceRule1->getBySecond())->getBySecond()
            );
        }

        if( ! empty( $recurrenceRule1->getBySetPositionCont())) {
            $this->assertNotEmpty(
                $recurrenceRule2->setBySetPosition( $recurrenceRule1->getBySetPosition())->getBySetPosition()
            );
        }
    }

    /**
     * Testing TimeZone setAliases, setStandard and setDaylight and TimeZoneRule setNames/setComments methods
     *
     * @test
     *
     */
    public function timeZoneTest() : void
    {
        $timeZone = new TimeZone();
        $alias1   = 'alias1';
        $alias2   = 'alias2';
        $timeZone->setAliases( [ $alias1, $alias2 => true ] );
        $aliases  = $timeZone->getAliases();
        $this->assertArrayHasKey( $alias1, $aliases );
        $this->assertArrayHasKey( $alias2, $aliases );

        $timeZoneRule1 = (new TimeZoneRule())->setStart( new DateTime());
        $timeZoneRule2 = (new TimeZoneRule())->setStart( (new DateTime())->modify( '+6 month' ));

        $timeZone->setStandard( [ $timeZoneRule2, $timeZoneRule1 ] );
        $timeZoneRules = $timeZone->getStandard();
        $this->assertSame( $timeZoneRule1, $timeZoneRules[0] );
        $this->assertSame( $timeZoneRule2, $timeZoneRules[1] );

        $timeZone->setDaylight( [ $timeZoneRule2, $timeZoneRule1 ] );
        $timeZoneRules = $timeZone->getDaylight();
        $this->assertSame( $timeZoneRule1, $timeZoneRules[0] );
        $this->assertSame( $timeZoneRule2, $timeZoneRules[1] );

        $timeZoneRule1->setNames( [ $alias1, $alias2 => true ] );
        $names = $timeZoneRule1->getNames();
        $this->assertArrayHasKey( $alias1, $names );
        $this->assertArrayHasKey( $alias2, $names );

        $timeZoneRule1->setComments( [ $alias1, $alias2 ] );
        $comments = $timeZoneRule1->getComments();
        $this->assertContains( $alias1, $comments );
        $this->assertContains( $alias2, $comments );
    }

    /**
     * Testing VirtualLocation factory and setFeatures methods
     *
     * @test
     */
    public function virtualLocationTest() : void
    {
        $url1     = 'https:\\host1@domian.com';
        $feature1 = 'audio';
        $feature2 = 'chat';

        $virtualLocation = VirtualLocation::factoryUriFeature( $url1, $feature1 );
        $this->assertSame( $url1, $virtualLocation->getUri());
        $this->assertArrayHasKey( $feature1, $virtualLocation->getFeatures());

        $virtualLocation = new VirtualLocation();
        $virtualLocation->setFeatures( [ $feature1, $feature2 => true ] );
        $features        = $virtualLocation->getFeatures();
        $this->assertArrayHasKey( $feature1, $features );
        $this->assertArrayHasKey( $feature2, $features );
    }
}
