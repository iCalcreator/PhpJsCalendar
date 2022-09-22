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
namespace Kigkonsult\PhpJsCalendar\Dto;

/**
 * Day of the week on which to repeat in a RecurrenceRule object
 */
final class NDay extends BaseDto
{
    /**
     * A day of the week on which to repeat, mandatory
     *
     * The allowed values are the same as for the "firstDayOfWeek" recurrenceRule property:
     * It MUST be one of the following values: "mo", "tu", "we", "th", "fr", "sa", "su"
     *
     * @var null|string
     */
    private ? string $day = null;

    /**
     * It represents only a specific instance within the recurrence period
     *
     * The value can be positive or negative but MUST NOT be zero
     *
     * @var int|null
     */
    private ? int $nthOfPeriod = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::NDAY;
    }

    /**
     * Class factory method
     *
     * @param string $day
     * @param int|null $nthOfPeriod  int <> 0
     * @return static
     */
    public static function factoryDay( string $day, ? int $nthOfPeriod = null ) : NDay
    {
        $instance = new self();
        $instance->setDaY( $day );
        if( $nthOfPeriod !== null ) { // NOT null/zero
            $instance->setNthOfPeriod( $nthOfPeriod );
        }
        return $instance;
    }

    /**
     * @return null|string
     */
    public function getDay() : ? string
    {
        return $this->day;
    }

    /**
     * Return bool true if day is not null
     *
     * @return bool
     */
    public function isDaySet() : bool
    {
        return ( null !== $this->day );
    }

    /**
     * @param string $day
     * @return static
     */
    public function setDay( string $day ) : NDay
    {
        $this->day = strtolower( $day );
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNthOfPeriod() : ?int
    {
        return $this->nthOfPeriod;
    }

    /**
     * Return bool true if nthOfPeriod is not null
     *
     * @return bool
     */
    public function isNthOfPeriodSet() : bool
    {
        return ( null !== $this->nthOfPeriod );
    }

    /**
     * @param int $nthOfPeriod   int <> 0
     * @return static
     */
    public function setNthOfPeriod( int $nthOfPeriod ) : NDay
    {
        if( ! empty( $nthOfPeriod )) { // NOT null/zero
            $this->nthOfPeriod = $nthOfPeriod;
        }
        return $this;
    }
}
