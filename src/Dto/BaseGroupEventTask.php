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
use Kigkonsult\PhpJsCalendar\Dto\Traits\LinksTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\TitleTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\UpdatedTrait;

abstract class BaseGroupEventTask extends BaseDto
{
    /**
     * This is a set of categories that relate to the calendar object.  The
     * set is represented as a map, with the keys being the categories
     * specified as URIs.  The value for each key in the map MUST be true.
     *
     * @var array  String[Boolean]
     */
    protected array $categories = [];

    /**
     * This is a color clients MAY use when displaying this calendar object.
     * The value is a color name taken from the set of names defined in
     * Section 4.3 of CSS Color Module Level 3 [COLORS] or an RGB value in
     * hexadecimal notation, as defined in Section 4.2.1 of CSS Color Module
     * Level 3.
     *
     * @var string|null
     */
    protected ? string $color = null;

    /**
     * This is the date and time this object was initially created, optional
     *
     * @var null|DateTimeInterface   UTCDateTime
     */
    protected ? DateTimeInterface $created = null;

    /**
     * This is a set of keywords or tags that relate to the object.  The set
     * is represented as a map, with the keys being the keywords.  The value
     * for each key in the map MUST be true.
     *
     * Optional
     *
     * @var array  String[Boolean]
     */
    protected array $keywords = [];

    use LinksTrait;

    /**
     * @var string|null  the language tag, as defined in [RFC5646]
     */
    protected ? string $locale = null;

    /**
     * This is the identifier for the product that last updated the
     * JSCalendar object.  This should be set whenever the data in the
     * object is modified (i.e., whenever the "updated" property is set).
     *
     * The vendor of the implementation MUST ensure that this is a globally
     * unique identifier, using some technique such as a Formal Public
     * Identifier (FPI) value, as defined in [ISO.9070.1991].
     *
     * @var string
     */
    protected string $prodId = 'Kigkonsult.se PhpJsCalendar ' . self::VERSION;

    use TitleTrait;

    /**
     * This is a globally unique identifier used to associate objects
     * representing the same event, task, group, or other object across
     * different systems, calendars, and views.  For recurring events and
     * tasks, the UID is associated with the base object and therefore is
     * the same for all occurrences; the combination of the UID with a
     * "recurrenceId" identifies a particular instance.
     *
     *  The generator of the identifier MUST guarantee that the identifier is
     * unique.  [RFC4122] describes a range of established algorithms to
     * generate universally unique identifiers (UUIDs).  UUID version 4,
     * described in Section 4.4 of [RFC4122], is RECOMMENDED.
     *
     * For compatibility with UIDs [RFC5545], implementations MUST be able
     * to receive and persist values of at least 255 octets for this
     * property, but they MUST NOT truncate values in the middle of a UTF-8
     * multi-octet sequence.
     *
     * @var string
     */
    protected string $uid;

    /**
     * The date and time the data in this object was last modified
     */
    use UpdatedTrait;

    /**
     * Class constructor
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->uid = BaseDto::getNewUid();
    }

    /**
     * @return array   String[Boolean]
     */
    public function getCategories() : array
    {
        return $this->categories;
    }

    /**
     * @return int
     */
    public function getCategoriesCount() : int
    {
        return count( $this->categories );
    }

    /**
     * @param string $category
     * @param null|bool $bool default true
     * @return static
     */
    public function addCategory( string $category, ? bool $bool = true ) : static
    {
        $this->categories[$category] = $bool;
        return $this;
    }

    /**
     * @param array $categories  String[Boolean] or string[]
     * @return static
     */
    public function setCategories( array $categories ) : static
    {
        foreach( $categories as $key => $value ) {
            if( self::isStringKeyAndBoolValue( $key, $value )) {
                $this->addCategory( $key, $value );
            }
            else {
                $this->addCategory( $value );
            }
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor() : ?string
    {
        return $this->color;
    }

    /**
     * Return bool true if color is not null
     *
     * @return bool
     */
    public function isColorSet() : bool
    {
        return ( null !== $this->color );
    }

    /**
     * @param string $color
     * @return static
     */
    public function setColor( string $color ) : static
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Return created, null if created > updated
     *
     * @param null|bool $asString  default true
     * @return string|DateTimeInterface|null     DateTime in UTC, string with suffix 'Z'
     */
    public function getCreated( ? bool $asString = true ) : null|string|DateTimeInterface
    {
        return ( ! empty( $this->created ) && $asString )
            ? $this->created->format( self::$UTCDateTimeFMT )
            : $this->created;
    }

    /**
     * Return bool true if created is not null
     *
     * @return bool
     */
    public function isCreatedSet() : bool
    {
        return ( null !== $this->created );
    }

    /**
     * Set created
     *
     * If empty, UTC date-time now
     * If DateTime, any timezone allowed, converted to UTC DateTime
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param string|DateTimeInterface $created any timeZone, saved in UTC DateTime
     * @return static
     * @throws Exception
     */
    public function setCreated( string|DateTimeInterface $created ) : static
    {
        $this->created = self::toUtcDateTime( $created, false );
        return $this;
    }

    /**
     * @return array
     */
    public function getKeywords() : array
    {
        return $this->keywords;
    }

    /**
     * @return int
     */
    public function getKeywordsCount() : int
    {
        return count( $this->keywords );
    }

    /**
     * Add single keyword/tag
     *
     * @param string $keyword
     * @param null|bool $bool default true
     * @return static
     */
    public function addKeyword( string $keyword, ? bool $bool = true ) : static
    {
        $this->keywords[$keyword] = $bool;
        return $this;
    }

    /**
     * @param array $keywords   String[Boolean] or string[]
     * @return static
     */
    public function setKeywords( array $keywords ) : static
    {
        foreach( $keywords as $key => $value ) {
            if( self::isStringKeyAndBoolValue( $key, $value )) {
                $this->addKeyword( $key, $value );
            }
            else {
                $this->addKeyword( $value );
            }
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale() : ?string
    {
        return $this->locale;
    }

    /**
     * Return bool true if locale is not null
     *
     * @return bool
     */
    public function isLocaleSet() : bool
    {
        return ( nUll !== $this->locale );
    }

    /**
     * @param string $locale
     * @return static
     */
    public function setLocale( string $locale ) : static
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getProdId() : string
    {
        return $this->prodId;
    }

    /**
     * @return string
     */
    public function getUid() : string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return static
     */
    public function setUid( string $uid ) : static
    {
        $this->uid = $uid;
        return $this;
    }
}
