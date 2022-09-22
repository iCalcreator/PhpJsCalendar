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

use Exception;

final class Group extends BaseGroupEventTask
{
    /**
     * @var string  Media type
     */
    public static string $mediaType = 'application/jscalendar+json;type=group';

    /**
     * Group property order as in rfc
     *
     * @var string[]
     */
    public static array $ElementOrder = [
        self::OBJECTTYPE,
        self::UID,
        self::PRODID,
        self::CREATED,
        self::UPDATED,
        self::TITLE,
        self::DESCRIPTION,
        self::DESCRIPTIONCONTENTTYPE,
        self::LOCALE,
        self::LINKS,
        self::KEYWORDS,
        self::CATEGORIES,
        self::COLOR,
        self::TIMEZONES,
        self::ENTRIES,
        self::SOURCE
    ];

    /**
     * Collection of group Event|Task members
     *
     * @var array
     */
    private array $entries = [];

    /**
     * The source from which updated versions of this group may be retrieved.  The value MUST be a URI.
     *
     * @var string|null
     */
    private ? string $source = null;

    /**
     * Class constructor
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::GROUP;
    }

    /**
     * Class factory method
     *
     * @param null|string $title
     * @return static
     * @throws Exception
     */
    public static function factory( ? string $title = null ) : Group
    {
        $instance = new self();
        if( null !== $title ) {
            $instance->setTitle( $title );
        }
        return $instance;
    }

    /**
     * @return array   *(Event|Task)
     */
    public function getEntries() : array
    {
        return $this->entries;
    }

    /**
     * @return int
     */
    public function getEntriesCount() : int
    {
        return count( $this->entries );
    }

    /**
     * Add Event/Task entry
     *
     * Entries are sorted by key:
     *    Event : start + duration
     *    Task  1 : start + estimatedDuration
     *    Task  2 : task::getEstimatedStart (due + estimatedDuration)
     * sequence
     * uid
     *
     * @param Event|Task $entry
     * @return static
     * @throws Exception
     */
    public function addEntry( Event|Task $entry ) : Group
    {
        $key = $entry->isStartSet() ? $entry->getStart() : self::$SP0;
        if( $entry instanceof Event ) {
            if( $entry->isDurationSet()) {
                $key .= $entry->getDuration();
            }
        }
        else {
            if( ! $entry->isStartSet() && $entry->isDueSet() && $entry->isEstimatedDurationSet()) {
                $key .= $entry->getEstimatedStart();
            }
            if( $entry->isEstimatedDurationSet()) {
                $key .= $entry->getEstimatedDuration();
            }
        }
        $key .= $entry->getUid();
        $key .= $entry->getSequence( true );
        $this->entries[$key] = $entry;
        ksort( $this->entries );
        return $this;
    }

    /**
     * @param Event[]|Task[] $entries or both...
     * @return static
     * @throws Exception
     */
    public function setEntries( array $entries ) : Group
    {
        foreach( $entries as $entry ) {
            $this->addEntry( $entry );
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource() : ? string
    {
        return $this->source;
    }

    /**
     * Return bool true if source is not null
     *
     * @return bool
     */
    public function isSourceSet() : bool
    {
        return ( null !== $this->source );
    }

    /**
     * @param string $source
     * @return static
     */
    public function setSource( string $source ) : Group
    {
        $this->source = $source;
        return $this;
    }
}
