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

use Kigkonsult\Icalcreator\Vcalendar;
use PHPUnit\Framework\TestCase;

/**
 * class IcalTest,
 *
 * Testing from/to iCal data, same provider as Kigkonsult\Icalcreator\test\ParseTest.php
 */
class IcalTest extends TestCase
{
    /**
     * iCal2dto2iCalTest provider
     *
     * @return mixed[]
     */
    public function iCal2dto2iCalTestProvider() : array
    {
        $dataArr = [];

        $dataArr[] = [
            601,
            "BEGIN:VCALENDAR\r\n" .
            "VERSION:2.0\r\n" .
            "PRODID:-//ShopReply Inc//CalReply 1.0//EN\r\n" .
            "UID:601\r\n" .
            "METHOD:REFRESH\r\n" .
            "SOURCE;x-a=first;VALUE=uri:message://https://www.masked.de/account/subscripti\r\n" .
            " /delivery/8878/%3Fweek=2021-W03\r\n" .
            "X-WR-CALNAME:ESPN Daily Calendar\r\n" .
            "X-WR-RELCALID:657d63b8-df1d-e611-8b88-06bb54d48d13\r\n" .
            "X-PUBLISH-TTL:P1D\r\n" .
            "X-TEST:601\r\n" .
            "BEGIN:VTIMEZONE\r\n" .
            "TZID:America/New_York\r\n" .
            "TZURL;x-a=first;VALUE=uri:message//:https://www.masked.de/account/subscriptio\r\n" .
            " n/delivery/8878/%3Fweek=2021-W03\r\n" .
            "BEGIN:STANDARD\r\n" .
            "DTSTART:20070101T020000\r\n" .
            "RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU;\r\n" .
            "TZOFFSETFROM:-0400\r\n" .
            "TZOFFSETTO:-0500\r\n" .
            "END:STANDARD\r\n" .
            "BEGIN:DAYLIGHT\r\n" .
            "DTSTART:20070101T020000\r\n" .
            "RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU;\r\n" .
            "TZOFFSETFROM:-0500\r\n" .
            "TZOFFSETTO:-0400\r\n" .
            "END:DAYLIGHT\r\n" .
            "END:VTIMEZONE\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:e2317772-f3a2-42cf-a5ac-e639fb6b2af0\r\n" .
            "CLASS:PUBLIC\r\n" .
            "TRANSP:TRANSPARENT\r\n" .
//          "SUMMARY:⚽ English FA Cup on ESPN+\r\n" .
            "SUMMARY:This will result in test error on Vcalendar SOURCE and X-props," .
            " VTIMEZONE TZURL, " .
            " VEVENT DTSTAMP, LOCATION, TRANSP, URL and" .
            " VALARM DESCRIPTION" . "\r\n" .
            "DTSTART;TZID=\"America/New_York\":20190316T081500\r\n" .
            "DTEND;TZID=\"America/New_York\":20190316T091500\r\n" .
            'DESCRIPTION:Watch live: http://bit.ly/FACuponEPlus\n\nNot an ESPN+ subscrib' . "\r\n\t" .
            'er? Start your free trial here: http://bit.ly/ESPNPlusSignup\n\nShare - http:' . "\r\n\t" .
            '//calrep.ly/2pLaM0n\n\nYou may unsubscribe by following - https://espn.calrep' . "\r\n\t" .
            'lyapp.com/unsubscribe/9bba908612a34be1881bc5098e8adbda\n\nPowered by CalReply' . "\r\n\t" .
            " - http://calrep.ly/poweredby\r\n" .
            'LOCATION:England\'s biggest soccer competition continues.\n\n• Watford vs. C' . "\r\n\t" .
            'rystal Palace (8:15 a.m. ET)\n• Swansea City vs. Manchester City (1:20 p.m.)\\' . "\r\n\t" .
            'n• Wolverhampton vs. Manchester United (3:55 p.m.)\n\nWatch live: http://bit.' . "\r\n\t" .
            "ly/FACuponEPlus\r\n" .
            "DTSTAMP:20190315T211012Z\r\n" .
            "LAST-MODIFIED:20190315T211012Z\r\n" .
            "SEQUENCE:1\r\n" .
            "URL;x-a=first;VALUE=uri:message//:https://www.masked.de/account/subscription/\r\n" .
            " delivery/8878/%3Fweek=2021-W03\r\n" .
            "BEGIN:VALARM\r\n" .
            "ACTION:DISPLAY\r\n" .
            "DESCRIPTION:Reminder\r\n" .
            "TRIGGER:-PT15M\r\n" .
            "END:VALARM\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        // rfc 9073 8.1.  Example 1, extended with VLOCATION inside PARTICIPANT
        $dataArr[] = [
            611,
            "BEGIN:VCALENDAR\r\n" .
            "UID:611\r\n" .
            "BEGIN:VEVENT\r\n" .
            "CREATED:20200215T145739Z\r\n" .
            "DESCRIPTION: Piano Sonata No 3\r\n" .
            "DTSTAMP:20200215T145739Z\r\n" .
            "DTSTART;TZID=America/New_York:20200315T150000Z\r\n" .
            "DTEND;TZID=America/New_York:20200315T163000Z\r\n" .
            "LAST-MODIFIED:20200216T145739Z\r\n" .
//          "SUMMARY:Beethoven Piano Sonatas\r\n" .
            "SUMMARY:This will result in test error on " .
            " VEVENT DTSTAMP, IMAGE, X-prop and" .
            " PARTICIPANT LOCATION+VLOCATION" .
            " VEVENT VLOCATION" . "\r\n" .
            "UID:123456\r\n" .
            "IMAGE;VALUE=URI;DISPLAY=BADGE;FMTTYPE=image/png:h\r\n" .
            " ttp://example.com/images/concert.png\r\n" .
            "X-TEST:611\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "PARTICIPANT-TYPE:SPONSOR\r\n" .
            "UID:dG9tQGZvb2Jhci5xlLmNvbQ\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://example.com/sponsor.vcf\r\n" .
            "END:PARTICIPANT\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "PARTICIPANT-TYPE:PERFORMER:\r\n" .
            "UID:em9lQGZvb2GFtcGxlLmNvbQ\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://www.example.com/people/johndoe.vcf\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:123456-abcdef-123456780\r\n" .
            "NAME:The curators office\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/curator/office.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "END:PARTICIPANT\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:123456-abcdef-98765432\r\n" .
            "NAME:The venue\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/venues/big-hall.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:123456-abcdef-87654321\r\n" .
            "NAME:Parking for the venue\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/venues/parking.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        // as rfc 9073 8.1.  Example 1, extended with VLOCATION inside PARTICIPANT
        // BUT only STRUCTURED-DATA
        $dataArr[] = [
            612,
            "BEGIN:VCALENDAR\r\n" .
            "UID:612\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:123456\r\n" .
            "SUMMARY:This will result in test error on" .
            " VEVENT DTSTAMP, IMAGE, X-prop and" .
            " PARTICIPANT STRUCTURED-DATA and" .
            " VLOCATIONs STRUCTURED-DATA" . "\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/event.vcf\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://example.com/participant1.vcf\r\n" .
            "END:PARTICIPANT\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://www.example.com/participant2.vcf\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/participant2/vlocation1.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "END:PARTICIPANT\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/participant3/vlocation1.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/participant3/vlocation2.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        // rfc 9073 8.2.  Example 2
        $dataArr[] = [
            621,
            "BEGIN:VCALENDAR\r\n" .
            "UID:621\r\n" .
            "BEGIN:VEVENT\r\n" .
            "CREATED:20200215T145739Z\r\n" .
            "DTSTAMP:20200215T145739Z\r\n" .
            "DTSTART;TZID=America/New_York:20200315T150000Z\r\n" .
            "DTEND;TZID=America/New_York:20200315T163000Z\r\n" .
            "LAST-MODIFIED:20200216T145739Z\r\n" .
//          "SUMMARY:Conference planning\r\n" .
            "SUMMARY:This will result in test error on" .
            " VEVENT DTSTAMP, ATTENDEE, LOCATION and" .
            " PARTICIPANT and" .
            " VLOCATION" . "\r\n" .
            "UID:123456\r\n" .
            "ORGANIZER:mailto:a@example.com\r\n" .
            "ATTENDEE;PARTSTAT=ACCEPTED;CN=Aname:mailto:a@example.com\r\n" .
            "ATTENDEE;RSVP=TRUE;CN=Bname:mailto:b@example.com\r\n" .
            "X-TEST:621\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "PARTICIPANT-TYPE:ACTIVE\r\n" .
            "UID:v39lQGZvb2GFtcGxlLmNvbQ\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://www.example.com/people/b.vcf\r\n" .
            "LOCATION:At home\r\n" .
            "END:PARTICIPANT\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        // rfc 9073 6.6.  Structured-Data
        $dataArr[] = [
            631,
            "BEGIN:VCALENDAR\r\n" .
            "UID:631\r\n" .
            "X-TEST:631\r\n" .
            "BEGIN:VEVENT\r\n" .
            "CREATED:20200215T145739Z\r\n" .
            "DTSTAMP:20200215T145739Z\r\n" .
            "DTSTART;TZID=America/New_York:20200315T150000Z\r\n" .
            "DTEND;TZID=America/New_York:20200315T163000Z\r\n" .
            "LAST-MODIFIED:20200216T145739Z\r\n" .
//          "SUMMARY:Conference planning\r\n" .
            "SUMMARY:This will result in test error on" .
            " VEVENT DTSTAMP and" .
            " PARTICIPANT " . "\r\n" .
            "UID:123456\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "PARTICIPANT-TYPE:ACTIVE\r\n" .
            "UID:v39lQGZvb2GFtcGxlLmNvbQ\r\n" .

            "STRUCTURED-DATA;VALUE=URI;FMTTYPE=application/ld+json;SCHEMA=\"https://sch\r\n" .
            " ema.org/SportsEvent\";VALUE=TEXT:{\n                                                      \r\n" .
            " \"@context\": \"http://schema.org\"\\,\n                                          \r\n" .
            " \"@type\": \"SportsEvent\"\\,\n                                                   \r\n" .
            " \"homeTeam\": \"Pittsburgh Pirates\"\\,\n                                         \r\n" .
            " \"awayTeam\": \"San Francisco Giants\"\n                                         \r\n" .
            " }\n\r\n" .

            "END:PARTICIPANT\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        // rfc 9074 7.2.  Example   VALARM "snoozing", "re-snoozing", and dismissal of an alarm
        // all in one but VEVENTs/VALARMs with different UIDs
        $dataArr[] = [
            641,
            "BEGIN:VCALENDAR\r\n" .
            "UID:641\r\n" .
            "X-TEST:641\r\n" .

            "BEGIN:VEVENT\r\n" .
            "CREATED:20210302T151004Z\r\n" .
            "UID:AC67C078-CED3-4BF5-9726-832C3749F621\r\n" .
            "DTSTAMP:20210302T151516Z\r\n" .
            "DTSTART;TZID=America/New_York:20210302T103000\r\n" .
            "DTEND;TZID=America/New_York:20210302T113000\r\n" .
//          "SUMMARY:Meeting\r\n" .
            "SUMMARY:This will result in test error on" .
            " VEVENT DTSTAMP and" .
            " VALARM and its VLOCATION " . "\r\n" .
            "BEGIN:VALARM\r\n" .
            "UID:8297C37D-BA2D-4476-91AE-C1EAA364F8E1\r\n" .
            "TRIGGER:-PT15M\r\n" .
            "DESCRIPTION:Event reminder\r\n" .
            "ACTION:DISPLAY\r\n" .
            "ACKNOWLEDGED:20210302T151514Z\r\n" .
            "END:VALARM\r\n" .
            "BEGIN:VALARM\r\n" .
            "UID:DE7B5C34-83FF-47FE-BE9E-FF41AE6DD097\r\n" .
            "TRIGGER;VALUE=DATE-TIME:20210302T152000Z\r\n" .
            "RELATED-TO;RELTYPE=SNOOZE:8297C37D-BA2D-4476-91AE-C1EAA364F8E1\r\n" .
            "DESCRIPTION:Event reminder\r\n" .
            "ACTION:DISPLAY\r\n" .
            "END:VALARM\r\n" .
            "END:VEVENT\r\n" .

            "BEGIN:VEVENT\r\n" .
            "CREATED:20210302T151004Z\r\n" .
            "UID:AC67C078-CED3-4BF5-9726-832C3749F622\r\n" .
            "DTSTAMP:20210302T152026Z\r\n" .
            "DTSTART;TZID=America/New_York:20210302T103000\r\n" .
            "DTEND;TZID=America/New_York:20210302T113000\r\n" .
            "SUMMARY:Meeting\r\n" .
            "BEGIN:VALARM\r\n" .
            "UID:8297C37D-BA2D-4476-91AE-C1EAA364F8E2\r\n" .
            "TRIGGER:-PT15M\r\n" .
            "DESCRIPTION:Event reminder\r\n" .
            "ACTION:DISPLAY\r\n" .
            "ACKNOWLEDGED:20210302T152024Z\r\n" .
            "END:VALARM\r\n" .
            "BEGIN:VALARM\r\n" .
            "UID:87D690A7-B5E8-4EB4-8500-491F50AFE394\r\n" .
            "TRIGGER;VALUE=DATE-TIME:20210302T152500Z\r\n" .
            "RELATED-TO;RELTYPE=SNOOZE:8297C37D-BA2D-4476-91AE-C1EAA364F8E2\r\n" .
            "DESCRIPTION:Event reminder\r\n" .
            "ACTION:DISPLAY\r\n" .
            "END:VALARM\r\n" .
            "END:VEVENT\r\n" .

            "BEGIN:VEVENT\r\n" .
            "CREATED:20210302T151004Z\r\n" .
            "UID:AC67C078-CED3-4BF5-9726-832C3749F623\r\n" .
            "DTSTAMP:20210302T152508Z\r\n" .
            "DTSTART;TZID=America/New_York:20210302T103000\r\n" .
            "DTEND;TZID=America/New_York:20210302T113000\r\n" .
            "SUMMARY:Meeting\r\n" .
            "BEGIN:VALARM\r\n" .
            "UID:8297C37D-BA2D-4476-91AE-C1EAA364F8E3\r\n" .
            "TRIGGER:-PT15M\r\n" .
            "DESCRIPTION:Event reminder\r\n" .
            "ACTION:DISPLAY\r\n" .
            "ACKNOWLEDGED:20210302T152507Z\r\n" .
            "END:VALARM\r\n" .
            "BEGIN:VALARM\r\n" .
            "UID:87D690A7-B5E8-4EB4-8500-491F50AFE394\r\n" .
            "TRIGGER;VALUE=DATE-TIME:20210302T152500Z\r\n" .
            "RELATED-TO;RELTYPE=SNOOZE:8297C37D-BA2D-4476-91AE-C1EAA364F8E3\r\n" .
            "DESCRIPTION:Event reminder\r\n" .
            "ACTION:DISPLAY\r\n" .
            "ACKNOWLEDGED:20210302T152507Z\r\n" .
            "END:VALARM\r\n" .
            "END:VEVENT\r\n" .

            "END:VCALENDAR\r\n"
        ];

        // rfc 9074 8.2.  Example   VALARM with PROXIMITY and VLOCATION
        $dataArr[] = [
            641,
            "BEGIN:VCALENDAR\r\n" .
            "UID:641\r\n" .
            "X-TEST:641\r\n" .
            "BEGIN:VEVENT\r\n" .
            "CREATED:20200215T145739Z\r\n" .
            "DTSTAMP:20200215T145739Z\r\n" .
            "DTSTART;TZID=America/New_York:20200315T150000Z\r\n" .
            "DTEND;TZID=America/New_York:20200315T163000Z\r\n" .
            "LAST-MODIFIED:20200216T145739Z\r\n" .
            "SUMMARY:Conference planning\r\n" .
            "UID:123456\r\n" .
            "BEGIN:VALARM\r\n" .
            "UID:77D80D14-906B-4257-963F-85B1E734DBB6\r\n" .
            "ACTION:DISPLAY\r\n" .
            "TRIGGER;VALUE=DATE-TIME:19760401T005545Z\r\n" .
            "DESCRIPTION:Remember to buy milk\r\n" .
            "PROXIMITY:DEPART\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:123456-abcdef-98765432\r\n" .
            "NAME:Office1\r\n" .
            "URL:geo:40.443,-79.945;u=10\r\n" .
            "END:VLOCATION\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:987654-ghijkl-1234567890\r\n" .
            "NAME:Office2\r\n" .
            "URL:geo:40.443,-79.945;u=10\r\n" .
            "END:VLOCATION\r\n" .
            "END:VALARM\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        return $dataArr;
    }

    /**
     * Testing ical to dto to ical again, most (all) will result in error
     *
     * @test
     * @dataProvider iCal2dto2iCalTestProvider
     * @param int $case
     * @param string $iCalString
     */
    public function iCal2dto2iCalTest( int $case, string $iCalString ) : void
    {
        $vcalendar     = new Vcalendar();
        $vcalendar->parse( $iCalString );
        $iCalString1   = $vcalendar->createCalendar();

//      $this->assertSame( $iCalString, $iCalString1, __FUNCTION__ . ' error case ' . $case . '-1' );
//      error_log( __METHOD__ . ' case ' . $case . PHP_EOL . $iCalString ); // test ###
//      error_log( __METHOD__ . ' case ' . $case . PHP_EOL . $iCalString1 ); // test ###

        $phpJsCalendar = PhpJsCalendar::factory()->iCalParse( $vcalendar, true );

        $jsonString    = $phpJsCalendar->jsonWrite( null, true )
            ->getJsonString();
//      error_log( __METHOD__ . ' case ' . $case . PHP_EOL . $jsonString ); // test ###

        $iCalString2   = $phpJsCalendar->setJsonString( $jsonString )
            ->iCalWrite()
            ->getVcalendar()
            ->createCalendar();

        $this->assertSame( $iCalString1, $iCalString2, __FUNCTION__ . ' error case ' . $case . '-2' );

//      error_log( $c->createCalendar()); // test ###
    }

    /**
     * iCal2dto2iCalTest2 provider
     *
     * @return mixed[]
     */
    public function iCal2dto2iCalTest2Provider() : array
    {
        $dataArr = [];

        // DURATION test Vevent, exp ok
        $dataArr[] = [
            701,
            "BEGIN:VCALENDAR\r\n" .
            "UID:VCALENDAR-701\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:VEVENT-1\r\n" .
            "DURATION:PT1H\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];
        // DURATION test Vtodo, exp ok
        $dataArr[] = [
            702,
            "BEGIN:VCALENDAR\r\n" .
            "UID:VCALENDAR-702\r\n" .
            "BEGIN:VTODO\r\n" .
            "UID:VTODO-1\r\n" .
            "DURATION:PT1H\r\n" .
            "END:VTODO\r\n" .
            "END:VCALENDAR\r\n"
        ];
        // DTSTART+DURATION test Vevent, exp ( duration -> ) DTEND
        $dataArr[] = [
            706,
            "BEGIN:VCALENDAR\r\n" .
            "UID:VCALENDAR-706\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:VEVENT-1\r\n" .
            "DTSTART:20221111T11111\r\n" .
            "DURATION:PT1H\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];
        // DTSTART+DURATION test Vtodo, exp ok
        $dataArr[] = [
            707,
            "BEGIN:VCALENDAR\r\n" .
            "UID:VCALENDAR-707\r\n" .
            "BEGIN:VTODO\r\n" .
            "UID:VTODO-1\r\n" .
            "DTSTART:20221111T11111\r\n" .
            "DURATION:PT1H\r\n" .
            "END:VTODO\r\n" .
            "END:VCALENDAR\r\n"
        ];

        // LOCATION test Vevent, exp err, LOCATION -> VLOCATION
        $dataArr[] = [
            712,
            "BEGIN:VCALENDAR\r\n" .
            "UID:VCALENDAR-712\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:VEVENT-1\r\n" .
            "DURATION:PT1H\r\n" .
            "LOCATION:Location-1\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];
        // LOCATION test Participant, exp err, LOCATION -> VLOCATION
        $dataArr[] = [
            722,
            "BEGIN:VCALENDAR\r\n" .
            "UID:VCALENDAR-722\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:VEVENT-1\r\n" .
            "LOCATION:Location-1\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "UID:PARTICIPANT-1\r\n" .
            "LOCATION:Participant-Location-1\r\n" .
            "END:PARTICIPANT\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];
        // LOCATION test Vlocation, exp err, LOCATION
        $dataArr[] = [
            732,
            "BEGIN:VCALENDAR\r\n" .
            "UID:VCALENDAR-732\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:VEVENT-1\r\n" .
            "LOCATION:Vevent-1-Vlocation-1-Name\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "UID:PARTICIPANT-1\r\n" .
            "LOCATION:Participant-1-Vlocation-1-Name\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:PARTICIPANT-1-VLOCATION-1\r\n" .
            "NAME:Participant-1-Vlocation-1-Name\r\n" .
            "END:VLOCATION\r\n" .
            "END:PARTICIPANT\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:VEVENT-1-VLOCATION-1\r\n" .
            "NAME:Vevent-1-Vlocation-1-Name\r\n" .
            "END:VLOCATION\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        return $dataArr;
    }

    /**
     * Same as parseIcalTest BUT startdate/duration - LOCATION/NAME iCal comp property tests
     *
     * @test
     * @dataProvider iCal2dto2iCalTest2Provider
     * @param int $case
     * @param string $iCalString
     */
    public function iCal2dto2iCalTest2( int $case, string $iCalString ) : void
    {
        $this->iCal2dto2iCalTest( $case, $iCalString );
    }

    /**
     * iCal2dto2iCalTest3 provider
     *
     * @return mixed[]
     */
    public function iCal2dto2iCalTest3Provider() : array
    {
        $dataArr = [];

        // STRUCTURED-DATA test Vevent
        $dataArr[] = [
            812,
            "BEGIN:VCALENDAR\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:123456\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/eventStrDta1.vcf\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/eventStrDta2.vcf\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];
        // STRUCTURED-DATA test Participant
        $dataArr[] = [
            822,
            "BEGIN:VCALENDAR\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:123456\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "UID:123456\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://example.com/participant1.vcf\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://example.com/participant2.vcf\r\n" .
            "END:PARTICIPANT\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];
        // STRUCTURED-DATA test Vlocation
        $dataArr[] = [
            832,
            "BEGIN:VCALENDAR\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:VEVENT-1\r\n" .
            "BEGIN:PARTICIPANT\r\n" .
            "UID:PARTICIPANT1\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:VLOCATION-P\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/participant1/vlocation1.vcf\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/participant1/vlocation2.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "END:PARTICIPANT\r\n" .
            "BEGIN:VLOCATION\r\n" .
            "UID:VEVENT-1\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/vevent/vlocation1.vcf\r\n" .
            "STRUCTURED-DATA;VALUE=URI:http://dir.example.com/vevent/vlocation2.vcf\r\n" .
            "END:VLOCATION\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n"
        ];

        return $dataArr;
    }

    /**
     * Same as parseIcalTest BUT STRUCTURED-DATA iCal comp property tests
     *
     * @test
     * @dataProvider iCal2dto2iCalTest3Provider
     * @param int $case
     * @param string $iCalString
     */
    public function iCal2dto2iCalTest3( int $case, string $iCalString ) : void
    {
        $this->iCal2dto2iCalTest( $case, $iCalString );
    }
}
