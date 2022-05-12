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
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\PhpJsCalendar\Dto\Event;
use Kigkonsult\PhpJsCalendar\Dto\Group;
use Kigkonsult\PhpJsCalendar\Dto\Task;
use Kigkonsult\PhpJsCalendar\DtoLoad\Event as EventLoad;
use Kigkonsult\PhpJsCalendar\DtoLoad\Group as GroupLoad;
use Kigkonsult\PhpJsCalendar\DtoLoad\Task  as TaskLoad;
use Kigkonsult\PhpJsCalendar\Json\Event    as EventJson;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class FactoryTest
 */
class FactoryTest extends TestCase
{
    /**
     * Testing Group factory method
     *
     * @test
     */
    public function groupTest() : void
    {
        $testTitle = 'testTitle';
        $group     = Group::factory( $testTitle );

        $this->assertSame(
            $testTitle,
            $group->getTitle(),
            'Error case #13 exp: ' .  $testTitle . ' curr: ' . $group->getTitle()
        );

    }

    /**
     * Testing Event factory and date methods
     *
     * @test
     */
    public function eventTest() : void
    {
        $start     = (new DateTime())->format( Event::$LocalDateTimeFMT );
        $duration  = 'PT1H';
        $testTitle = 'testTitle';
        $event     = Event::factory( $start, $duration, $testTitle );

        $this->assertSame(
            $start,
            $event->getStart(),
            'Error case #21, exp: ' . $start . ' curr: ' . $event->getStart()
        );

        $this->assertSame(
            $duration,
            $event->getDuration(),
            'Error case #22, exp' . $duration . ' curr: ' . $event->getDuration()
        );

        $this->assertSame(
            $testTitle,
            $event->getTitle(),
            'Error case #23 exp: ' .  $testTitle . ' curr: ' . $event->getTitle()
        );

        $estimate = $event->getEstimatedEnd();
        $this->assertGreaterThan(
            $event->getStart(),
            $estimate,
            'Error case #24 end: ' . $estimate . ' exp > ' . $event->getStart()
        );

        $event     = Event::factory( $start );
        $estimate = $event->getEstimatedEnd();
        $this->assertNull(
            $estimate,
            'Error case #25 Event::getEstimatedEnd returns NOT null but : ' . var_export( $estimate, true )
        );
    }

    /**
     * Testing Task factory and date methods
     *
     * @test
     */
    public function taskTest() : void
    {
        $start     = (new DateTime())->format( Event::$LocalDateTimeFMT );
        $due       = (new DateTime())->modify( '+1day' )->format( Event::$LocalDateTimeFMT );
        $duration  = 'P1D';
        $testTitle = 'testTitle';
        $task      = Task::factory( $start, $due, $duration, $testTitle );

        $this->assertSame(
            $start,
            $task->getStart(),
            'Error case #31 (start), exp: ' . $start . ' curr: ' . $task->getStart()
        );

        $this->assertSame(
            $due,
            $task->getDue(),
            'Error case #32 (due), exp: ' . $due . ' curr: ' . $task->getDue()
        );

        $estimate = $task->getEstimatedDuration();
        $this->assertSame(
            $duration,
            $estimate,
            'Error case #33 (estimatedDuration), exp: ' . $duration . ' curr: ' . $estimate
        );

        $this->assertSame(
            $testTitle,
            $task->getTitle(),
            'Error case #34 (title) exp: ' .  $testTitle . ' curr: ' . $task->getTitle()
        );

        $estimate = $task->getEstimatedStart();
        $this->assertSame(
            $start,
            $estimate,
            'Error case #35 exp: ' .  $start . ' curr: ' . $estimate
        );

        $estimate = $task->getEstimatedEnd();
        $this->assertSame(
            $due,
            $estimate,
            'Error case #36 exp: ' .  $due . ' curr: ' . $estimate
        );

        $task      = Task::factory();
        $estimate = $task->getEstimatedStart();
        $this->assertNull(
            $estimate,
            'Error case #37 Task::getEstimatedStart returns NOT null but : ' . var_export( $estimate, true )
        );

        $estimate = $task->getEstimatedEnd();
        $this->assertNull(
            $estimate,
            'Error case #38 Task::getEstimatedEnd returns NOT null but : ' . var_export( $estimate, true )
        );
    }

    /**
     * Testing PhpJsCalendar factory method arguments
     *
     * @test
     */
    public function phpJsCalendarTest1() : void
    {
        $eventLoad = EventLoad::load();

        $phpJsCalendar = PhpJsCalendar::factory( Json::jsonEncode( EventJson::write( $eventLoad )));
        $this->assertIsString(
            $phpJsCalendar->getJsonString(),
            'Error case #41'
        );

        $phpJsCalendar = PhpJsCalendar::factory( null, $eventLoad );
        $this->assertTrue(
            $phpJsCalendar->isDtoEvent(),
            'Error case #421'
        );
        $this->assertInstanceOf(
            Event::class,
            $phpJsCalendar->getDto(),
            'Error case #422'
        );

        $phpJsCalendar = PhpJsCalendar::factory( null, TaskLoad::load() );
        $this->assertTrue(
            $phpJsCalendar->isDtoTask(),
            'Error case #431'
        );
        $this->assertInstanceOf(
            Task::class,
            $phpJsCalendar->getDto(),
            'Error case #432'
        );

        $phpJsCalendar = PhpJsCalendar::factory( null, GroupLoad::load() );
        $this->assertTrue(
            $phpJsCalendar->isDtoGroup(),
            'Error case #441'
        );
        $this->assertInstanceOf(
            Group::class,
            $phpJsCalendar->getDto(),
            'Error case #442'
        );

        $phpJsCalendar = PhpJsCalendar::factory( null, null, new Vcalendar());
        $this->assertInstanceOf(
            Vcalendar::class,
            $phpJsCalendar->getVcalendar(),
            'Error case #45'
        );
    }

    /**
     * Testing PhpJsCalendar factory method and empty input exceptions
     *
     * @test
     */
    public function phpJsCalendarTest2() : void
    {
        $ok = false;
        try {
            PhpJsCalendar::factory()->jsonParse();
        }
        catch( RuntimeException $re ) {
            $ok = true;
        }
        $this->assertTrue(
            $ok,
            'Error case #51'
        );

        $ok = false;
        try {
            PhpJsCalendar::factory()->jsonWrite();
        }
        catch( RuntimeException $re ) {
            $ok = true;
        }
        $this->assertTrue(
            $ok,
            'Error case #52'
        );

        $ok = false;
        try {
            PhpJsCalendar::factory()->iCalParse();
        }
        catch( RuntimeException $re ) {
            $ok = true;
        }
        $this->assertTrue(
            $ok,
            'Error case #53'
        );

        $ok = false;
        try {
            PhpJsCalendar::factory()->iCalWrite();
        }
        catch( RuntimeException $re ) {
            $ok = true;
        }
        $this->assertTrue(
            $ok,
            'Error case #54'
        );
    }
}
