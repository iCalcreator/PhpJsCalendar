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

use Kigkonsult\PhpJsCalendar\Dto\Traits\RecurrenceOverridesTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\RecurrenceRulesTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\StartTrait;

final class TimeZoneRule extends BaseDto
{
    /**
     * Here the DTSTART property from iCalendar mandatory, LocalDateTime
     */
    use StartTrait;

    /**
     * the TZOFFSETFROM property from iCalendar, mandatory
     *
     * @var string|null
     */
    private ? string $offsetFrom = null;

    /**
     * the TZOFFSETTO property from iCalendar, mandatory
     *
     * @var string|null
     */
    private ? string $offsetTo = null;

    /**
     * the iCalendar RRULE property mapped as RecurrenceRule[], optional
     */
    use RecurrenceRulesTrait;

    /**
     * the RDATE properties from iCalendar, optional
     */
    use RecurrenceOverridesTrait;

    /**
     * the TZNAME properties from iCalendar, optional
     *
     * @var mixed[]  String[Boolean]
     */
    private array $names = [];

    /**
     * the COMMENT properties from iCalendar optional
     *
     * @var string[]
     */
    private array $comments = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TIMEZONERULE;
    }

    /**
     * @return string|null
     */
    public function getOffsetFrom() : ?string
    {
        return $this->offsetFrom;
    }

    /**
     * Return bool true if offsetFrom is not null
     *
     * @return bool
     */
    public function isOffsetFromSet() : bool
    {
        return ( null!== $this->offsetFrom );
    }

    /**
     * @param string $offsetFrom
     * @return static
     */
    public function setOffsetFrom( string $offsetFrom ) : static
    {
        $this->offsetFrom = $offsetFrom;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOffsetTo() : ?string
    {
        return $this->offsetTo;
    }

    /**
     * Return bool true if offsetTo is not null
     *
     * @return bool
     */
    public function isOffsetToSet() : bool
    {
        return ( null !== $this->offsetTo );
    }

    /**
     * @param string $offsetTo
     * @return static
     */
    public function setOffsetTo( string $offsetTo ) : static
    {
        $this->offsetTo = $offsetTo;
        return $this;
    }

    /**
     * @return mixed[]   String[Boolean]
     */
    public function getNames() : array
    {
        return $this->names;
    }

    /**
     * @return int
     */
    public function getNamesCount() : int
    {
        return count( $this->names );
    }

    /**
     * @param string $name
     * @param null|bool $bool default true
     * @return static
     */
    public function addName( string $name, ? bool $bool = true ) : static
    {
        $this->names[$name] = $bool;
        return $this;
    }

    /**
     * @param string[] $names  String[Boolean] or string[]
     * @return static
     */
    public function setNames( array $names ) : static
    {
        foreach( $names as $key => $value ) {
            if( is_string( $key ) && ! is_numeric( $key ) && is_bool( $value )) {
                $this->addName( $key, $value );
            }
            else {
                $this->addName( $value );
            }
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getComments() : array
    {
        return $this->comments;
    }

    /**
     * @return int
     */
    public function getCommentsCount() : int
    {
        return count( $this->comments );
    }

    /**
     * @param string $comment
     * @return static
     */
    public function addComment( string $comment ) : static
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * @param string[] $comments
     * @return static
     */
    public function setComments( array $comments ) : static
    {
        foreach( $comments as $comment ) {
            $this->addComment( $comment );
        }
        return $this;
    }
}
