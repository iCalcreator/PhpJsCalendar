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
use Kigkonsult\Icalcreator\Daylight       as IcalDaylight;
use Kigkonsult\Icalcreator\Standard       as IcalStandard;
use Kigkonsult\Icalcreator\Vtimezone      as IcalVtimezone;
use Kigkonsult\PhpJsCalendar\Dto\TimeZone as TimeZoneDto;

class TimeZone extends BaseIcal
{
    /**
     * TimeZone properties to iCal Vtimezone
     *
     * @param string $id
     * @param TimeZoneDto $timeZoneDto
     * @return IcalVtimezone
     * @throws Exception
     */
    public static function processToIcal( string $id, TimeZoneDto $timeZoneDto  ) : IcalVtimezone
    {
        static$S   = '/';
        $vtimezone = new IcalVtimezone();
        $vtimezone->setTzid( $timeZoneDto->getTzId() ?: ltrim( $id, $S ));

        if( $timeZoneDto->isUpdatedSet()) {
            $vtimezone->setLastmodified( $timeZoneDto->getUpdated());
        }
        if( $timeZoneDto->isUrlSet()) {
            $vtimezone->setTzurl( $timeZoneDto->getUrl());
        }
        if( $timeZoneDto->isValidUntilSet()) {
            $vtimezone->setTzuntil( $timeZoneDto->getValidUntil());
        }
        // array of "String[Boolean]"
        if( ! empty( $timeZoneDto->getAliasesCount())) {
            foreach( array_keys( $timeZoneDto->getAliases()) as $value ) {
                $vtimezone->setTzidaliasof( $value );
            }
        }
        // array of "TimeZoneRule[]"
        if( ! empty( $timeZoneDto->getStandardCount())) {
            foreach( $timeZoneDto->getStandard() as $standard ) {
                TimeZoneRule::processToIcal( $standard, $vtimezone->newStandard());
            }
        }
        if( ! empty( $timeZoneDto->getDaylightCount())) {
            foreach( $timeZoneDto->getDaylight() as $daylight ) {
                TimeZoneRule::processToIcal( $daylight, $vtimezone->newDaylight() );
            }
        }
        return $vtimezone;
    }

    /**
     * Ical iCal Vtimezone property to TimeZone
     *
     * @param string $timeZoneId
     * @param IcalVtimezone $vtimezone
     * @return TimeZoneDto
     * @throws Exception
     * @since 0.9.2 - 2022-08-24
     */
    public static function processFromIcal(
        string $timeZoneId,
        IcalVtimezone $vtimezone
    ) : TimeZoneDto
    {
        $timeZoneDto = new TimeZoneDto();

        $timeZoneDto->setTzId( $vtimezone->isTzidSet() ? $vtimezone->getTzid() : $timeZoneId );

        if( $vtimezone->isLastmodifiedSet()) {
            $timeZoneDto->setUpdated( $vtimezone->getLastmodified());
        }

        if( $vtimezone->isTzurlSet()) {
            $timeZoneDto->setUrl( $vtimezone->getTzurl());
        }

        if( $vtimezone->isTzuntilSet()) {
            $timeZoneDto->setValidUntil( $vtimezone->getTzuntil());
        }

        // array of "String[Boolean]"
        foreach( $vtimezone->getAllTzidaliasof() as $tzidAliasOf ) {
            $timeZoneDto->addAlias( $tzidAliasOf );
        }

        // arrays of "TimeZoneRule[]"
        foreach( $vtimezone->getComponents() as $component ) {
            $timezoneRule = TimeZoneRule::processFromIcal( $component );
            if( $component instanceof IcalStandard ) {
                $timeZoneDto->addStandard( $timezoneRule );
            }
            elseif( $component instanceof IcalDaylight ) {
                $timeZoneDto->addDaylight( $timezoneRule );
            }
        }

        return $timeZoneDto;
    }
}
