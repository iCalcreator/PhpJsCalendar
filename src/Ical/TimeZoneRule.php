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
use Kigkonsult\Icalcreator\Daylight;
use Kigkonsult\Icalcreator\Standard;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\PhpJsCalendar\Dto\PatchObject  as PatchObjectDto;
use Kigkonsult\PhpJsCalendar\Dto\TimeZoneRule as TimeZoneRuleDto;

class TimeZoneRule extends BaseIcal
{
    /**
     * Ical TimeZoneRule Dto properties to iCal Standard/Daylight
     *
     * @param TimeZoneRuleDto $timeZoneRuleDto
     * @param Standard|Daylight $timezoneSub
     * @throws Exception
     */
    public static function processTo( TimeZoneRuleDto $timeZoneRuleDto, Standard|Daylight $timezoneSub ) : void
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
                $timezoneSub->setRrule( RecurrenceRule::processTo( $recurrenceRule ));
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
     * @param Standard|Daylight $component
     * @return TimeZoneRuleDto
     * @throws Exception
     */
    public static function processFrom( Standard|Daylight $component ) : TimeZoneRuleDto
    {
        $timeZoneRuleDto = new TimeZoneRuleDto();

        if( $component->isDtstartSet()) {
            $timeZoneRuleDto->setStart( $component->getDtstart());
        }

        if( $component->isTzoffsetFromSet()) {
            $timeZoneRuleDto->setOffsetfrom( $component->getTzoffsetFrom());
        }

        if( $component->isTzoffsetToSet()) {
            $timeZoneRuleDto->setOffsetto( $component->getTzoffsetTo());
        }

        if( $component->isRRuleSet()) {
            $timeZoneRuleDto->addRecurrenceRule( RecurrenceRule::processFrom( $component->getRRule()));
        }

        // array of "LocalDateTime[PatchObject]" - ignore Rdate period and PatchObject
        while( false !== ( $value = $component->getRdate( null, true ))) {
            if( ! $value->hasParamKey( Vcalendar::VALUE ) ||
                ( Vcalendar::PERIOD !== $value->getParams( Vcalendar::VALUE ))) {
                $timeZoneRuleDto->addRecurrenceOverride( $value->value[0], new PatchObjectDto() );
            }
        }

        // array of "String[Boolean]"
        while( false !== ( $value = $component->getTzname())) {
            $timeZoneRuleDto->addName( $value );
        }

        // array of "String[]"
        while( false !== ( $value = $component->getComment())) {
            $timeZoneRuleDto->addComment( $value );
        }

        return $timeZoneRuleDto;
    }
}
