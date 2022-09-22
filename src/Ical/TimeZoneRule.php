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
namespace Kigkonsult\PhpJsCalendar\Ical;

use Exception;
use Kigkonsult\Icalcreator\CalendarComponent  as IcalComponent;
use Kigkonsult\Icalcreator\Daylight           as IcalDaylight;
use Kigkonsult\Icalcreator\Standard           as IcalStandard;
use Kigkonsult\Icalcreator\Vcalendar          as IcalVcalendar;
use Kigkonsult\PhpJsCalendar\Dto\PatchObject  as PatchObjectDto;
use Kigkonsult\PhpJsCalendar\Dto\TimeZoneRule as TimeZoneRuleDto;

class TimeZoneRule extends BaseIcal
{
    /**
     * TimeZoneRule Dto properties to iCal Standard/Daylight
     *
     * @param TimeZoneRuleDto $timeZoneRuleDto
     * @param IcalStandard|IcalDaylight $timezoneSub
     * @throws Exception
     */
    public static function processToIcal(
        TimeZoneRuleDto $timeZoneRuleDto,
        IcalStandard|IcalDaylight $timezoneSub
    ) : void
    {
        if( $timeZoneRuleDto->isStartSet()) {
            $timezoneSub->setDtstart( $timeZoneRuleDto->getStart());
        }

        if( $timeZoneRuleDto->isOffsetFromSet()) {
            $timezoneSub->setTzoffsetfrom( $timeZoneRuleDto->getOffsetFrom());
        }

        if( $timeZoneRuleDto->isOffsetToSet()) {
            $timezoneSub->setTzoffsetto( $timeZoneRuleDto->getOffsetTo());
        }

        // array of "RecurrenceRule[]"  opt until is in UTC
        if( ! empty( $timeZoneRuleDto->getRecurrenceRulesCount())) {
            foreach( $timeZoneRuleDto->getRecurrenceRules() as $recurrenceRule ) {
                $timezoneSub->setRrule( RecurrenceRule::processToIcalRecur( $recurrenceRule ));
            }
        }

        // array of "LocalDateTime[PatchObject]" - ignore PatchObject
        if( ! empty( $timeZoneRuleDto->getRecurrenceOverridesCount())) {
            foreach( array_keys( $timeZoneRuleDto->getRecurrenceOverrides()) as $value ) {
                $timezoneSub->setRdate( $value );
            }
        }

        // array of "String[Boolean]"
        if( ! empty( $timeZoneRuleDto->getNamesCount())) {
            foreach( array_keys( $timeZoneRuleDto->getNames()) as $value ) {
                $timezoneSub->setTzname( $value );
            }
        }

        // array of "String[]"
        if( ! empty( $timeZoneRuleDto->getCommentsCount())) {
            foreach( $timeZoneRuleDto->getComments() as $value ) {
                $timezoneSub->setComment( $value );
            }
        }
    }

    /**
     * Ical iCal Standard/Daylight to TimezoneRule
     *
     * @param IcalComponent|IcalStandard|IcalDaylight $iCalComp
     * @return TimeZoneRuleDto
     * @throws Exception
     */
    public static function processFromIcal(
        IcalComponent|IcalStandard|IcalDaylight $iCalComp
    ) : TimeZoneRuleDto
    {
        $timeZoneRuleDto = new TimeZoneRuleDto();

        if( $iCalComp->isDtstartSet()) {
            $timeZoneRuleDto->setStart( $iCalComp->getDtstart());
        }

        if( $iCalComp->isTzoffsetFromSet()) {
            $timeZoneRuleDto->setOffsetfrom( $iCalComp->getTzoffsetFrom());
        }

        if( $iCalComp->isTzoffsetToSet()) {
            $timeZoneRuleDto->setOffsetto( $iCalComp->getTzoffsetTo());
        }

        if( $iCalComp->isRRuleSet()) {
            $timeZoneRuleDto->addRecurrenceRule(
                RecurrenceRule::processFromIcalRecur( $iCalComp->getRRule())
            );
        }

        // array of "LocalDateTime[PatchObject]" - ignore Rdate period and PatchObject
        foreach( $iCalComp->getAllRdate( true ) as $rDatePc ) {
            if( ! $rDatePc->hasParamKey( IcalVcalendar::VALUE, IcalVcalendar::PERIOD )) {
                $timeZoneRuleDto->addRecurrenceOverride(
                    $rDatePc->getValue()[0],
                    new PatchObjectDto()
                );
            }
        }

        // array of "String[Boolean]"
        foreach( $iCalComp->getAllTzname() as $value ) {
            $timeZoneRuleDto->addName( $value );
        }

        // array of "String[]"
        foreach( $iCalComp->getAllComment() as $value ) {
            $timeZoneRuleDto->addComment( $value );
        }

        return $timeZoneRuleDto;
    }
}
