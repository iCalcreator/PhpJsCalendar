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

use DateTimeInterface;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Traits\UpdatedTrait;

final class TimeZone extends BaseDto
{
    /**
     * The TZID property from iCalendar, mandatory, here always same as 'TimeZoneId' (map key)
     *
     * @var null|string
     */
    private ? string $tzId = null;

    /**
     * The LAST-MODIFIED property from iCalendar optional
     */
    use UpdatedTrait;

    /**
     * The TZURL property from iCalendar optional
     *
     * @var string|null
     */
    private ? string $url = null;

    /**
     * The TZUNTIL property from iCalendar (UTCDateTime), specified in [RFC7808], optional
     *
     * @var DateTimeInterface|null
     */
    private ? DateTimeInterface $validUntil = null;

    /**
     * The TZID-ALIAS-OF properties from iCalendar, specified in [RFC7808], to a JSON set of aliases optional
     *
     * @var array  String[Boolean]
     */
    private array $aliases = [];

    /**
     * The STANDARD sub-components from iCalendar, optional
     *
     * @var TimeZoneRule[]
     */
    private array $standard = [];

    /**
     * The DAYLIGHT sub-components from iCalendar, optional
     *
     * @var TimeZoneRule[]
     */
    private array $daylight = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TIMEZONE;
    }

    /**
     * @return null|string
     */
    public function getTzId() : ? string
    {
        return $this->tzId;
    }

    /**
     * Return bool true if tzId is not null
     *
     * @return bool
     */
    public function isTzIdSet() : bool
    {
        return ( null !== $this->tzId );
    }

    /**
     * @param string $tzId
     * @return static
     */
    public function setTzId( string $tzId ) : TimeZone
    {
        $this->tzId = $tzId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl() : ?string
    {
        return $this->url;
    }

    /**
     * Return bool true if url is not null
     *
     * @return bool
     */
    public function isUrlSet() : bool
    {
        return ( null !== $this->url );
    }

    /**
     * @param string $url
     * @return static
     */
    public function setUrl( string $url ) : TimeZone
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param null|bool $asString  default true
     * @return string|DateTimeInterface|null        DateTime with UTC, string without timezone suffix
     */
    public function getValidUntil( ? bool $asString = true ) : string|DateTimeInterface|null
    {
        return (( $this->validUntil instanceof DateTimeInterface ) && $asString )
            ? $this->validUntil->format( self::$UTCDateTimeFMT )
            : $this->validUntil;
    }

    /**
     * Return bool true if validUntil is not null
     *
     * @return bool
     */
    public function isValidUntilSet() : bool
    {
        return ( null !== $this->validUntil );
    }

    /**
     * Set validUntil
     *
     * If empty, UTC date-time now
     * If DateTime, any timezone allowed, converted to UTC DateTime
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param string|DateTimeInterface $validUntil UTCDateTime
     * @return static
     * @throws Exception
     */
    public function setValidUntil( string|DateTimeInterface $validUntil ) : TimeZone
    {
        $this->validUntil = self::toUtcDateTime( $validUntil, false );
        return $this;
    }

    /**
     * @return array
     */
    public function getAliases() : array
    {
        return $this->aliases;
    }

    /**
     * @return int
     */
    public function getAliasesCount() : int
    {
        return count( $this->aliases );
    }

    /**
     * @param string $alias
     * @param null|bool $bool default true
     * @return static
     */
    public function addAlias( string $alias, ? bool $bool = true ) : TimeZone
    {
        $this->aliases[$alias] = $bool;
        return $this;
    }

    /**
     * @param array $aliases  String[Boolean] or string[]
     * @return static
     */
    public function setAliases( array $aliases ) : TimeZone
    {
        foreach( $aliases as $key => $value ) {
            if( self::isStringKeyAndBoolValue( $key, $value )) {
                $this->addAlias( $key, $value );
            }
            else {
                $this->addAlias( $value );
            }
        }
        return $this;
    }

    /**
     * @return TimeZoneRule[]
     */
    public function getStandard() : array
    {
        return $this->standard;
    }

    /**
     * @return int
     */
    public function getStandardCount() : int
    {
        return count( $this->standard );
    }

    /**
     * @param TimeZoneRule $standard
     * @return static
     */
    public function addStandard( TimeZoneRule $standard ) : TimeZone
    {
        $this->standard[] = $standard;
        usort( $this->standard, self::$SORTCALLBACK );
        return $this;
    }

    /**
     * @param TimeZoneRule[] $standard
     * @return static
     */
    public function setStandard( array $standard ) : TimeZone
    {
        foreach( $standard as $theStandard ) {
            $this->addStandard( $theStandard );
        }
        return $this;
    }

    /**
     * @return TimeZoneRule[]
     */
    public function getDaylight() : array
    {
        return $this->daylight;
    }

    /**
     * @return int
     */
    public function getDaylightCount() : int
    {
        return count( $this->daylight );
    }

    /**
     * @param TimeZoneRule $daylight
     * @return static
     */
    public function addDaylight( TimeZoneRule $daylight ) : TimeZone
    {
        $this->daylight[] = $daylight;
        usort( $this->daylight, self::$SORTCALLBACK );
        return $this;
    }

    /**
     * @param TimeZoneRule[] $daylight
     * @return static
     */
    public function setDaylight( array $daylight ) : TimeZone
    {
        foreach( $daylight as $theDaylight ) {
            $this->addDaylight( $theDaylight );
        }
        return $this;
    }

    /**
     * @var callable
     */
    private static $SORTCALLBACK = [ __CLASS__, 'timeZoneRuleCmp' ];

    /**
     * @param TimeZoneRule $a
     * @param TimeZoneRule $b
     * @return int
     */
    private static function timeZoneRuleCmp( TimeZoneRule $a, TimeZoneRule $b ) : int
    {
        return strcmp((string) $a->getStart(), (string) $b->getStart());
    }
}
