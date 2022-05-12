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
namespace Kigkonsult\PhpJsCalendar\Dto\Traits;

use DateTimeInterface;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\PatchObject;

trait RecurrenceOverridesTrait
{
    /**
     * Maps recurrence ids (the date-time produced by the recurrence rule) to the overridden properties of the recurrence instance
     *
     * @var mixed[]    LocalDateTime[PatchObject]
     */
    protected array $recurrenceOverrides = [];

    /**
     * @return mixed[]  LocalDateTimeString[PatchObject]
     */
    public function getRecurrenceOverrides() : array
    {
        return $this->recurrenceOverrides;
    }

    /**
     * @return int
     */
    public function getRecurrenceOverridesCount() : int
    {
        return count( $this->recurrenceOverrides );
    }

    /**
     * @param string|DateTimeInterface $localDateTime LocalDateTime, saved as DateTime with UTC (note, not 'in' UTC)
     * @param PatchObject $patchObject
     * @return static
     * @throws Exception
     */
    public function addRecurrenceOverride(
        string|DateTimeInterface $localDateTime,
        PatchObject $patchObject
    ) : static
    {
        /*
         * A pointer in the PatchObject MUST be ignored if it starts with one of the following prefixes:
         * @type
         *  excludedRecurrenceRules
         *  method
         *  privacy
         *  prodId
         *  recurrenceId
         *  recurrenceIdTimeZone
         *  recurrenceOverrides
         *  recurrenceRules
         *  relatedTo
         *  replyTo
         *  sentBy
         *  timeZones
         *  uid
         */
        $localDateTime  = self::toUtcDateTime( $localDateTime, true );
        $dateTimeString = $localDateTime->format( self::$LocalDateTimeFMT );
        $this->recurrenceOverrides[$dateTimeString] = $patchObject;
        return $this;
    }

    /**
     * @param array $recurrenceOverrides
     * @return static
     * @throws Exception
     */
    public function setRecurrenceOverrides( array $recurrenceOverrides ) : static
    {
        foreach( $recurrenceOverrides as $localDateTime => $patchObject ) {
            $this->addrecurrenceOverride( $localDateTime, $patchObject );
        }
        return $this;
    }
}
