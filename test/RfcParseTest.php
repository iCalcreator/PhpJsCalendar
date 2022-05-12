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

use PHPUnit\Framework\TestCase;

class RfcParseTest extends TestCase
{
    /**
     * parseTest provider
     *
     * @return mixed[]
     */
    public function parseTestProvider() : array
    {
        $dataArr = [];

        // rfc 8984 6.1.  Simple Event
        $dataArr[] = [
            '6.1',
            '{
    "@type": "Event",
    "uid": "a8df6573-0474-496d-8496-033ad45d-6-1",
    "updated": "2020-01-02T18:23:04Z",
    "title": "Some event",
    "start": "2020-01-15T13:00:00",
    "timeZone": "America/New_York",
    "duration": "PT1H"
}'
            ];

        // rfc 8984 6.2.  Simple Task
        $dataArr[] = [
            '6.2',
            '{
    "@type": "Task",
    "uid": "2a358cee-6489-4f14-a57f-c104db4d-6-2",
    "updated": "2020-01-09T14:32:01Z",
    "title": "Do something"
}'
        ];

        // rfc 8984 6.3.  Simple Group
        // BUT... Group has 'title', not 'name'
        $dataArr[] = [
            '6.3',
            '{
    "@type": "Group",
    "uid": "bf0ac22b-4989-4caf-9ebd-54301b4e-6-3",
    "updated": "2020-01-15T18:00:00Z",
    "title": "A simple group",
    "entries": [
        {
            "@type": "Event",
            "uid": "a8df6573-0474-496d-8496-033ad45d7fea",
            "updated": "2020-01-02T18:23:04Z",
            "title": "Some event",
            "start": "2020-01-15T13:00:00",
            "timeZone": "America/New_York",
            "duration": "PT1H"
        },
        {
            "@type": "Task",
            "uid": "2a358cee-6489-4f14-a57f-c104db4dc2f2",
            "updated": "2020-01-09T14:32:01Z",
            "title": "Do something"
        }
    ]
}'
        ];

        // rfc 8984 6.4.  All-Day Event
        $dataArr[] = [
            '6.4',
            '{
    "@type": "Event",
    "uid": "2a358cee-6489-4f14-a57f-c104db4d-6-4",
    "title": "April Fool\'s Day",
    "showWithoutTime": true,
    "start": "1900-04-01T00:00:00",
    "duration": "P1D",
    "recurrenceRules": [
        {
            "@type": "RecurrenceRule",
            "frequency": "yearly"
        }
    ]
}'
        ];

        // rfc 8984 6.5.  Task with a Due Date
        $dataArr[] = [
            '6.5',
            '{
    "@type": "Task",
    "uid": "2a358cee-6489-4f14-a57f-c104db4d-6-5",
    "title": "Buy groceries",
    "due": "2020-01-19T18:00:00",
    "timeZone": "Europe/Vienna",
    "estimatedDuration": "PT1H"
}'
        ];

        // rfc 8984 6.6.  Event with End Time Zone
        // BUT... Lcation has 'relativeTo', NOT 'rel'
        $dataArr[] = [
            '6.6',
            '{
    "@type": "Event",
    "uid": "2a358cee-6489-4f14-a57f-c104db4d-6-6",
    "title": "Flight XY51 to Tokyo",
    "start": "2020-04-01T09:00:00",
    "timeZone": "Europe/Berlin",
    "duration": "PT10H30M",
    "locations": {
        "1": {
            "@type": "Location",
            "name": "Frankfurt Airport (FRA)",
            "relativeTo": "start"
        },
        "2": {
            "@type": "Location",
            "name": "Narita International Airport (NRT)",
            "relativeTo": "end",
            "timeZone": "Asia/Tokyo"
        }
    }
}'
        ];

        // rfc 8984 6.7.  Floating-Time Event (with Recurrence)
        $dataArr[] = [
            '6.7',
            '{
    "@type": "Event",
    "uid": "2a358cee-6489-4f14-a57f-c104db4d-6-7",
    "title": "Yoga",
    "start": "2020-01-01T07:00:00",
    "duration": "PT30M",
    "recurrenceRules": [
        {
            "@type": "RecurrenceRule",
            "frequency": "daily"
        }
    ]
}'
        ];

        // rfc 8984 6.8.  Event with Multiple Locations and Localization
        // todo fix json encode 'größte'
        $dataArr[] = [
            '6.8',
            '{
    "@type": "Event",
    "uid": "2a358cee-6489-4f14-a57f-c104db4d-6-8",
    "title": "Live from Music Bowl: The Band",
    "description": "Go see the biggest music event ever!",
    "locale": "en",
    "start": "2020-07-04T17:00:00",
    "timeZone": "America/New_York",
    "duration": "PT3H",
    "locations": {
        "c0503d30-8c50-4372-87b5-7657e8e0fedd": {
            "@type": "Location",
            "name": "The Music Bowl",
            "description": "Music Bowl, Central Park, New York",
            "coordinates": "geo:40.7829,-73.9654"
        }
    },
    "virtualLocations": {
        "vloc1": {
            "@type": "VirtualLocation",
            "name": "Free live Stream from Music Bowl",
            "uri": "https://stream.example.com/the_band_2020"
        }
    },
    "localizations": {
        "de": {
            "title": "Live von der Music Bowl: The Band!",
            "description": "Schau dir das größte Musikereignis an!",
            "virtualLocations/vloc1/name": "Gratis Live-Stream aus der Music Bowl"
        }
    }
}'
        ];
//            "description": "Schau dir das gr' . mb_chr( 0x00f6 ) . mb_chr( 0x00df ) . 'te Musikereignis an!",


        // rfc 8984 6.9.  Recurring Event with Overrides
        $dataArr[] = [
            '6.9',
            '{
    "@type": "Event",
    "uid": "2a358cee-6489-4f14-a57f-c104db4d-6-9",
    "title": "Calculus I",
    "start": "2020-01-08T09:00:00",
    "timeZone": "Europe/London",
    "duration": "PT1H30M",
    "locations": {
        "mlab": {
            "@type": "Location",
            "name": "Math lab room 1",
            "description": "Math Lab I, Department of Mathematics"
        }
    },
    "recurrenceRules": [
        {
            "@type": "RecurrenceRule",
            "frequency": "weekly",
            "until": "2020-06-24T09:00:00"
        }
    ],
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
}'
        ];

        // rfc 8984 6.10.  Recurring Event with Participants
        $dataArr[] = [
            '6.10',
            '{
    "@type": "Event",
    "uid": "2a358cee-6489-4f14-a57f-c104db4-6-10",
    "title": "FooBar team meeting",
    "start": "2020-01-08T09:00:00",
    "timeZone": "Africa/Johannesburg",
    "duration": "PT1H",
    "virtualLocations": {
        "0": {
            "@type": "VirtualLocation",
            "name": "ChatMe meeting room",
            "uri": "https://chatme.example.com?id=1234567&pw=a8a24627b63d"
        }
    },
    "recurrenceRules": [
        {
            "@type": "RecurrenceRule",
            "frequency": "weekly"
        }
    ],
    "recurrenceOverrides": {
        "2020-03-04T09:00:00": {
            "participants/dG9tQGZvb2Jhci5xlLmNvbQ/participationStatus": "declined"
        }
    },
    "replyTo": {
        "imip": "mailto:f245f875-7f63-4a5e-a2c8@schedule.example.com"
    },
    "participants": {
        "dG9tQGZvb2Jhci5xlLmNvbQ": {
            "@type": "Participant",
            "name": "Tom Tool",
            "email": "tom@foobar.example.com",
            "sendTo": {
                "imip": "mailto:tom@calendar.example.com"
            },
            "roles": {
                "attendee": true
            },
            "participationStatus": "accepted"
        },
        "em9lQGZvb2GFtcGxlLmNvbQ": {
            "@type": "Participant",
            "name": "Zoe Zelda",
            "email": "zoe@foobar.example.com",
            "sendTo": {
                "imip": "mailto:zoe@calendar.example.com"
            },
            "roles": {
                "owner": true,
                "attendee": true,
                "chair": true
            },
            "participationStatus": "accepted"
        }
    }
}'
        ];

        return $dataArr;
    }

    /**
     * Testing json string parse + json string write
     *
     * @test
     * @dataProvider parseTestProvider
     * @param string $case
     * @param string $jsonString
     */
    public function parseTest( string $case, string $jsonString ) : void
    {

//      error_log( __FUNCTION__ . ' case : ' . $case ); // test ###

        $phpJsCalendar = PhpJsCalendar::factory( $jsonString );

        $dto           = $phpJsCalendar->jsonParse()->getDto();

//      echo 'Dto : ' . var_export( $dto, true ) . PHP_EOL; // test ###

        $jsonString2   = $phpJsCalendar->jsonWrite( $dto, true )->getJsonString();

        $jsonString3 = str_replace(
            [
                '            "prodId": "Kigkonsult.se PhpJsCalendar 0.9",' . PHP_EOL,
                '    "prodId": "Kigkonsult.se PhpJsCalendar 0.9",' . PHP_EOL,
            ],
            '',
            $jsonString2
        );

        $this->assertSame(
            $jsonString,
            $jsonString3,
            'diff error in #1-' . $case . '-1'
        );

//      echo 'case ' . $case . PHP_EOL . $jsonString3; // test ###
    }


    /**
     * Same as above BUT json string parse, convert to iCal, convert from ical, json string write
     *
     * @test
     * @dataProvider parseTestProvider
     * @param string $case
     * @param string $jsonString
     */
    public function parseIcalTest( string $case, string $jsonString ) : void
    {
//      error_log( __FUNCTION__ . ' case : ' . $case ); // test ###

        $phpJsCalendar = PhpJsCalendar::factory( $jsonString );

        $dto           = $phpJsCalendar->jsonParse()->getDto();

//      echo 'Dto : ' . var_export( $dto, true ) . PHP_EOL; // test ###

        $vcalendar     = $phpJsCalendar->iCalWrite()->getVcalendar();

//      echo 'case: ' . $case . PHP_EOL . $vcalendar->createCalendar() . PHP_EOL; // test ###

        $dto2          = $phpJsCalendar->iCalParse()->getDto();

        $jsonString2   = $phpJsCalendar->jsonWrite( null, true )->getJsonString();

        $jsonString3 = str_replace(
            [
                '            "prodId": "Kigkonsult.se PhpJsCalendar 0.9",' . PHP_EOL,
                '    "prodId": "Kigkonsult.se PhpJsCalendar 0.9",' . PHP_EOL,
            ],
            '',
            $jsonString2
        );

//      echo PHP_EOL . $vcalendar->createCalendar() . PHP_EOL; // test ###

        $this->assertSame(
            $jsonString,
            $jsonString3,
            'diff error in #2-' . $case . '-1'
        );
//      echo $jsonString3 . PHP_EOL; // test ###
    }
}
