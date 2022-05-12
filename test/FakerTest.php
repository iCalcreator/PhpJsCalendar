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

use Exception;
use Kigkonsult\PhpJsCalendar\DtoLoad\Event as EventLoader;
use Kigkonsult\PhpJsCalendar\DtoLoad\Group as GroupLoader;
use Kigkonsult\PhpJsCalendar\DtoLoad\Task  as TaskLoader;
use PHPUnit\Framework\TestCase;

class FakerTest extends TestCase
{
    /**
     * parseTest provider
     *
     * @return mixed[]
     */
    public function dtoTypeProvider() : array
    {
        $dataArr = [];

        $dataArr[] = [
            1,
            PhpJsCalendar::EVENT,
        ];

        $dataArr[] = [
            2,
            PhpJsCalendar::TASK,
        ];

        $dataArr[] = [
            3,
            PhpJsCalendar::GROUP,
        ];

        return $dataArr;
    }

    /**
     * @test
     * @dataProvider dtoTypeProvider
     *
     * @param int $case
     * @param string $dtoType
     * @return void
     * @throws Exception
     */
    public function fakerFull( int $case, string $dtoType ) : void
    {
        $case += 10;

//      error_log( __METHOD__ . ' start case: ' . $case ); // test ###

        switch( $dtoType ) {
            case PhpJsCalendar::EVENT :
                $dto1 = EventLoader::load();
                break;
            case PhpJsCalendar::TASK :
                $dto1 = TaskLoader::load();
                break;
            case PhpJsCalendar::GROUP :
                $dto1 = GroupLoader::load();
                break;
        } // end switch
        $phpJsCalendar1 = PhpJsCalendar::factoryJsonWrite( $dto1, true );
        $this->assertSame(
            $dtoType,
            $phpJsCalendar1->getDtoType(),
            'error case ' . $case . '-1'
        );
        switch( $dtoType ) {
            case PhpJsCalendar::EVENT :
                $this->assertTrue( $phpJsCalendar1->isDtoEvent() );
                break;
            case PhpJsCalendar::TASK :
                $this->assertTrue( $phpJsCalendar1->isDtoTask() );
                break;
            case PhpJsCalendar::GROUP :
                $this->assertTrue( $phpJsCalendar1->isDtoGroup() );
                break;
        } // end switch

        $jsonString1 = $phpJsCalendar1->getJsonString();

//         echo $jsonString1 . PHP_EOL; // test ###

        $phpJsCalendar2 = PhpJsCalendar::factoryJsonParse( $jsonString1 );
        $this->assertSame(
            $dtoType,
            $phpJsCalendar2->getDtoType(),
            'error case ' . $case . '-2'
        );
        $dto2 = $phpJsCalendar2->getDto();

        $phpJsCalendar2 = PhpJsCalendar::factoryJsonWrite( $dto2, true );
        $this->assertSame(
            $dtoType,
            $phpJsCalendar2->getDtoType(),
            'error case ' . $case . '-3'
        );

        $jsonString2 = $phpJsCalendar2->getJsonString();
        $this->assertSame(
            $jsonString1,
            $jsonString2,
            'error case ' . $case . '-4'
        );
    }

    /**
     * @test
     * @dataProvider dtoTypeProvider
     *
     * @param int $case
     * @param string $dtoType
     * @return void
     * @throws Exception
     */
    public function fakerIcalFull( int $case, string $dtoType ) : void
    {
        $case += 20;

//      error_log( __METHOD__ . ' start case: ' . $case ); // test ###

        switch( $dtoType ) {
            case PhpJsCalendar::EVENT :
                $dto1 = EventLoader::load();
                break;
            case PhpJsCalendar::TASK :
                $dto1 = TaskLoader::load();
                break;
            case PhpJsCalendar::GROUP :
                $dto1 = GroupLoader::load();
                break;
        } // end switch

        $phpJsCalendar = PhpJsCalendar::factory( null, $dto1 );
        $jsonString1 = $phpJsCalendar->jsonWrite( null, true )->getJsonString();

        $phpJsCalendar->iCalWrite();

//    echo $phpJsCalendar->getVcalendar()->createCalendar() . PHP_EOL; // test ###

        $jsonString2 = $phpJsCalendar
            ->iCalParse()
            ->jsonWrite( null, true )
            ->getJsonString();

//      error_log( 'jsonString1 : ' . PHP_EOL . $jsonString1 ); // test ###
//      error_log( 'jsonString2 : ' . PHP_EOL . $jsonString2 ); // test ###

        $this->assertSame(
            $jsonString1,
            $jsonString2,
            'error case ' . $case . '-1'
        );
    }
}
