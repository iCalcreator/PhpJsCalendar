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

use DateInterval;
use DateTimeInterface;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Traits\DateInterval2StringTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\DescriptionTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\RecurrenceOverridesTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\RecurrenceRulesTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\RelatedToTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\ReplayToSendToHelpTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\SentByTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\StartTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\TimeZoneTrait;

abstract class BaseEventTask extends BaseGroupEventTask
{

    use DescriptionTrait;

    /**
     * The media type [RFC6838] of the contents of the "description" property
     *
     * Empty if "description" empty. default 'text/plain'
     *
     * @var string|null
     */
    protected ? string $descriptionContentType = null;

    /**
     * @var string
     */
    public static string $descriptionContentTypeDefault = 'text/plain';

    /**
     * If true, this object is an overridden, excluded instance of a recurring JSCalendar object, default false
     *
     * @var null|bool
     */
    protected ? bool $excluded = null;

    /**
     * @var bool
     */
    public static bool $excludedDefault = false;

    /**
     * A set of recurrence rules (repeating patterns) for date-times on which the object will not occur
     *
     * @var RecurrenceRule[]
     */
    protected array $excludedRecurrenceRules = [];

    /**
     * This specifies how this calendar object should be treated when calculating free-busy state, optional, default 'busy'
     *
     * @var string|null
     */
    protected ? string $freeBusyStatus = null;

    /**
     * @var string
     */
    public static string $freeBusyStatusDefault = 'busy';

    /**
     * @var array  PatchObject[] ??
     */
    protected array $localizations = [];

    /**
     * A map of location ids to Location objects, representing locations associated with the object
     *
     * @var array Id[Location]
     */
    protected array $locations = [];

    /**
     * @var string|null  only if the JSCalendar object represents an iTIP scheduling message
     */
    protected ? string $method = null;

    /**
     * A map of participant ids to participants, describing their participation in the calendar object
     *
     * @var array Id[Participant]
     */
    protected array $participants = [];

    /**
     * Specifies a priority for the calendar object, optional default 0
     *
     * @var int|null
     */
    protected ? int $priority = null;

    /**
     * @var int
     */
    public static int $priorityDefault = 0;

    /**
     * Indication that it should not be shared or should only have the time information shared but the details withheld
     *
     * Optional, default: "public"
     *
     * @var string|null
     */
    protected ? string $privacy = null;

    /**
     * @var string
     */
    public static string $privacyDefault = 'public';

    /**
     * @var DateTimeInterface|null  LocalDateTime (without timezone) BUT saved as UTC DateTime
     */
    protected ? DateTimeInterface $recurrenceId = null;

    /**
     * Identifies the time zone of the main JSCalendar object, of which this JSCalendar object is a recurrence instance
     *
     * This property MUST be set if the "recurrenceId" property is set.
     * It MUST NOT be set if the "recurrenceId" property is not set.
     *
     * @var string|null
     */
    protected ? string $recurrenceIdTimeZone = null;

    /**
     * the RDATE properties from iCalendar
     */
    use RecurrenceOverridesTrait;

    /**
     * the iCalendar RRULE property mapped as RecurrenceRule[]
     */
    use RecurrenceRulesTrait;

    use RelatedToTrait;

    /**
     * Represents methods by which participants may submit their response to the organizer of the calendar object
     *
     * The keys in the property value are the available methods
     * and MUST only contain ASCII alphanumeric characters (A-Za-z0-9)
     * The value is a URI for the method specified in the key
     *
     * @var string[]
     */
    protected array $replyTo= [];

    /**
     * A request status as returned from processing the most recent scheduling request for this JSCalendar object
     *
     * statcode ";" statdesc [";" extdata]
     *
     * @var string|null
     */
    protected ? string $requestStatus = null;

    use SentByTrait;

    /**
     * Incremented by one every time a change is made to the object, optional, default: 0
     *
     * Not if the change only modifies the "participants" property
     *
     * @var int|null   UnsignedInt
     */
    protected ? int $sequence = null;

    /**
     * @var int
     */
    public static int $sequenceDefault = 0;

    /**
     * When the time is not important to display to the user when rendering this calendar object
     *
     * Commonly known as "all-day" events, default false
     *
     * @var null|bool
     */
    protected ? bool $showWithoutTime = null;

    /**
     * @var bool
     */
    public static bool $showWithoutTimeDefault = false;

    use StartTrait;

    use TimeZoneTrait;

    /**
     * Identifies the time zone the object is scheduled in or is null for floating time
     *
     * @var array TimeZoneId[TimeZone]
     */
    protected array $timeZones = [];

    /**
     * @var array Id[VirtualLocation]
     */
    protected array $virtualLocations= [];

    /**
     * If true, use the user's default alerts and ignore the value of the "alerts" property
     *
     * Optional, default: false
     *
     * @var null|bool
     */
    protected ? bool $useDefaultAlerts = null;

    /**
     * @var bool
     */
    public static bool $useDefaultAlertsDefault = false;

    /**
     * @var array  Id[Alert]
     */
    protected array $alerts = [];

    /**
     * Class constructor
     *
     * @throws Exception
     */
    /*
    public function __construct()
    {
        parent::__construct();
    }
    */

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null   null if description not set
     */
    public function getDescriptionContentType( ? bool $defaultIfNotSet = false ) : ?string
    {
        $isEmpty = empty( $this->descriptionContentType );
        return match( true ) {
            empty( $this->description ) => null,
            ( ! $defaultIfNotSet && $isEmpty ) => null,
            $isEmpty => self::$descriptionContentTypeDefault,
            default =>  $this->descriptionContentType
        };
    }

    /**
     * Return bool true if description is set and descriptionContentType is not null
     *
     * @return bool   false if description not set
     */
    public function isDescriptionContentTypeSet() : bool
    {
        return ( $this->isDescriptionSet() && ( null !== $this->descriptionContentType ));
    }

    /**
     * Media types MUST be subtypes of type "text" and SHOULD be "text/plain" or "text/html" [MEDIATYPES].
     *
     * They MAY include parameters, and the "charset" parameter value MUST be "utf-8", if specified.
     * Descriptions of type "text/html" MAY contain "cid" URLs [RFC2392] to reference links in the calendar object
     * by use of the "cid" property of the Link object.
     *
     * @param string $descriptionContentType
     * @return static
     */
    public function setDescriptionContentType( string $descriptionContentType ) : static
    {
        $this->descriptionContentType = $descriptionContentType;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return null|bool
     */
    public function getExcluded( ? bool $defaultIfNotSet = false ) : ? bool
    {
        return (( null === $this->excluded ) && $defaultIfNotSet) ? self::$excludedDefault : $this->excluded;
    }

    /**
     * Return bool true if excluded is not null
     *
     * @return bool
     */
    public function isExcludedSet() : bool
    {
        return ( null !== $this->excluded );
    }

    /**
     * @param bool $excluded
     * @return static
     */
    public function setExcluded( bool $excluded ) : static
    {
        $this->excluded = $excluded;
        return $this;
    }

    /**
     * @return RecurrenceRule[]
     */
    public function getExcludedRecurrenceRules() : array
    {
        return $this->excludedRecurrenceRules;
    }

    /**
     * @return int
     */
    public function getExcludedRecurrenceRulesCount() : int
    {
        return count( $this->excludedRecurrenceRules );
    }

    /**
     * @param RecurrenceRule $excludedRecurrenceRule
     * @return static
     */
    public function addExcludedRecurrenceRule( RecurrenceRule $excludedRecurrenceRule ) : static
    {
        $this->excludedRecurrenceRules[] = $excludedRecurrenceRule;
        return $this;
    }

    /**
     * @param RecurrenceRule[] $excludedRecurrenceRules
     * @return static
     */
    public function setExcludedRecurrenceRules( array $excludedRecurrenceRules ) : static
    {
        foreach( $excludedRecurrenceRules as $excludedRecurrenceRule ) {
            $this->addExcludedRecurrenceRule( $excludedRecurrenceRule );
        }
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getFreeBusyStatus( ? bool $defaultIfNotSet = false ) : ? string
    {
        return ( ! $this->isFreeBusyStatusSet() && $defaultIfNotSet )
            ? self::$freeBusyStatusDefault
            : $this->freeBusyStatus;
    }

    /**
     * Return bool true if freeBusyStatus is not null
     *
     * @return bool
     */
    public function isFreeBusyStatusSet() : bool
    {
        return ( null !== $this->freeBusyStatus );
    }

    /**
     * @param string $freeBusyStatus
     * @return static
     */
    public function setFreeBusyStatus( string $freeBusyStatus ) : static
    {
        $this->freeBusyStatus = $freeBusyStatus;
        return $this;
    }

    /**
     * @return array
     */
    public function getLocalizations() : array
    {
        return $this->localizations;
    }

    /**
     * @return int
     */
    public function getLocalizationsCount() : int
    {
        return count( $this->localizations );
    }

    /**
     * @param string $languageTag
     * @param PatchObject $patchObject
     * @return static
     */
    public function addLocalization( string $languageTag, PatchObject $patchObject ) : static
    {
        $this->localizations[$languageTag] = $patchObject;
        return $this;
    }

    /**
     * @param PatchObject[] $localizations  String[PatchObject]
     * @return static
     */
    public function setLocalizations( array $localizations ) : static
    {
        foreach( $localizations as $languageTag => $patchObject ) {
            $this->addLocalization( $languageTag, $patchObject );
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getLocations() : array
    {
        return $this->locations;
    }

    /**
     * @return int
     */
    public function getLocationsCount() : int
    {
        return count( $this->locations );
    }

    /**
     * @param int|string $id
     * @param Location $location
     * @return static
     */
    public function addLocation( int|string $id, Location $location ) : static
    {
        $this->locations[(string)$id] = $location;
        ksort( $this->locations );
        return $this;
    }

    /**
     * @param Location[] $locations
     * @return static
     */
    public function setLocations( array $locations ) : static
    {
        foreach( $locations as $id => $location ) {
            $this->addLocation( $id, $location );
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMethod() : ?string
    {
        return $this->method;
    }

    /**
     * Return bool true if method is not null
     *
     * @return bool
     */
    public function isMethodSet() : bool
    {
        return ( null !== $this->method );
    }

    /**
     * @param string $method
     * @return static
     */
    public function setMethod( string $method ) : static
    {
        $this->method = strtolower( $method );
        return $this;
    }

    /**
     * @return Participant[]
     */
    public function getParticipants() : array
    {
        return $this->participants;
    }

    /**
     * @return int
     */
    public function getParticipantsCount() : int
    {
        return count( $this->participants );
    }

    /**
     * @param int|string $id
     * @param Participant $participant
     * @return static
     */
    public function addParticipant( int|string $id, Participant $participant ) : static
    {
        $this->participants[(string)$id] = $participant;
        return $this;
    }

    /**
     * @param Participant[] $participants
     * @return static
     */
    public function setParticipants( array $participants ) : static
    {
        foreach( $participants as $id => $participant ) {
            $this->addParticipant( $id, $participant );
        }
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return int|null
     */
    public function getPriority( ? bool $defaultIfNotSet = false ) : ? int
    {
        return ( ! $this->isPrioritySet() && $defaultIfNotSet )
            ? self::$priorityDefault
            : $this->priority;
    }

    /**
     * Return bool true if priority is not null
     *
     * @return bool
     */
    public function isPrioritySet() : bool
    {
        return ( null !== $this->priority );
    }

    /**
     * The priority is specified as an integer in the range 0 to 9.
     *
     * A value of 0 specifies an undefined priority, for which the treatment will
     * vary by situation.  A value of 1 is the highest priority.  A value of
     * 2 is the second highest priority.  Subsequent numbers specify a
     * decreasing ordinal priority.  A value of 9 is the lowest priority.
     * Other integer values are reserved for future use.
     *
     * @param int $priority
     * @return static
     */
    public function setPriority( int $priority ) : static
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getPrivacy( ? bool $defaultIfNotSet = false ) : ? string
    {
        return ( ! $this->isPrivacySet() && $defaultIfNotSet )
            ? self::$privacyDefault
            : $this->privacy;
    }

    /**
     * Return bool true if privacy is not null
     *
     * @return bool
     */
    public function isPrivacySet() : bool
    {
        return ( null !== $this->privacy );
    }

    /**
     *
     * @param string $privacy
     * @return static
     */
    public function setPrivacy( string $privacy ) : static
    {
        $this->privacy = strtolower( $privacy );
        return $this;
    }

    /**
     * @param null|bool $asString  default true
     * @return null|string|DateTimeInterface   DateTime with UTC, string without timezone suffix
     */
    public function getRecurrenceId( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        return (( $this->recurrenceId instanceof DateTimeInterface ) && $asString )
            ? $this->recurrenceId->format( self::$LocalDateTimeFMT )
            : $this->recurrenceId;
    }

    /**
     * Return bool true if recurrenceId is not null
     *
     * @return bool
     */
    public function isRecurrenceIdSet() : bool
    {
        return ( null !== $this->recurrenceId );
    }

    /**
     * Set recurrenceId, DateTime or string
     *
     * If DateTime, any timezone allowed, saved as DateTime with input:date[time] with UTC timezone
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param string|DateTimeInterface $recurrenceId  LocalDateTime, saved as DateTime with UTC (note, not in UTC)
     * @return static
     * @throws Exception
     */
    public function setRecurrenceId( string | DateTimeInterface $recurrenceId ) : static
    {
        $this->recurrenceId = self::toUtcDateTime( $recurrenceId );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecurrenceIdTimeZone() : ?string
    {
        return $this->recurrenceIdTimeZone;
    }

    /**
     * Return bool true if recurrenceIdTimeZone is not null
     *
     * @return bool
     */
    public function isRecurrenceIdTimeZoneSet() : bool
    {
        return ( null !== $this->recurrenceIdTimeZone );
    }

    /**
     * @param string $recurrenceIdTimeZone
     * @return static
     */
    public function setRecurrenceIdTimeZone( string $recurrenceIdTimeZone ) : static
    {
        $this->recurrenceIdTimeZone = $recurrenceIdTimeZone;
        return $this;
    }

    /**
     * @return array
     */
    public function getReplyTo() : array
    {
        return $this->replyTo;
    }

    /**
     * @return int
     */
    public function getReplyToCount() : int
    {
        return count( $this->replyTo );
    }

    /**
     * method :
     * "imip":  The organizer accepts an iCalendar Message-Based
     *          Interoperability Protocol (iMIP) [RFC6047] response at this email
     *          address.  The value MUST be a "mailto:" URI.
     *
     * "web":   Opening this URI in a web browser will provide the user with
     *          a page where they can submit a reply to the organizer.  The value
     *          MUST be a URL using the "https:" scheme.
     *
     * "other": The organizer is identified by this URI, but the method for submitting the response is undefined.
     *
     * @param string $method
     * @param string $replyTo
     * @return $this
     */
    public function addReplyTo( string $method, string $replyTo ) : static
    {
        self::assureOptValuePrefix( $replyTo, $method );
        $this->replyTo[$method] = $replyTo;
        ksort( $this->replyTo );
        return $this;
    }

    use ReplayToSendToHelpTrait;

    /**
     * Array, key => value
     *
     * @param string[] $replyTo
     * @return static
     */
    public function setReplyTo( array $replyTo ) : static
    {
        foreach( $replyTo as $method => $value ) {
            $this->addReplyTo( $method, $value );
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRequestStatus() : ?string
    {
        return $this->requestStatus;
    }

    /**
     * Return bool true if requestStatus is not null
     *
     * @return bool
     */
    public function isRequestStatusSet() : bool
    {
        return ( null !== $this->requestStatus );
    }

    /**
     * @param string $requestStatus
     * @return static
     */
    public function setRequestStatus( string $requestStatus ) : static
    {
        $this->requestStatus = $requestStatus;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return int|null  default 0
     */
    public function getSequence( ? bool $defaultIfNotSet = false ) : ? int
    {
        return ( ! $this->isSequenceSet() && $defaultIfNotSet )
            ? self::$sequenceDefault
            : $this->sequence;
    }

    /**
     * Return bool true if sequence is not null
     *
     * @return bool
     */
    public function isSequenceSet() : bool
    {
        return ( null !== $this->sequence );
    }

    /**
     * @param int $sequence
     * @return static
     */
    public function setSequence( int $sequence ) : static
    {
        self::assertUnsignedInt( $sequence, self::SEQUENCE );
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return null|bool    default false
     */
    public function getShowWithoutTime( ? bool $defaultIfNotSet = false ) : ? bool
    {
        return ( ! $this->isShowWithoutTimeSet() && $defaultIfNotSet )
            ? self::$showWithoutTimeDefault
            : $this->showWithoutTime;
    }

    /**
     * Return bool true if showWithoutTime is not null
     *
     * @return bool
     */
    public function isShowWithoutTimeSet() : bool
    {
        return ( null !== $this->showWithoutTime );
    }

    /**
     * @param bool $showWithoutTime
     * @return static
     */
    public function setShowWithoutTime( bool $showWithoutTime ) : static
    {
        $this->showWithoutTime = $showWithoutTime;
        return $this;
    }

    /**
     * @return array
     */
    public function getTimeZones() : array
    {
        return $this->timeZones;
    }

    /**
     * @return int
     */
    public function getTimeZonesCount() : int
    {
        return count( $this->timeZones );
    }

    /**
     * @param string $timeZoneId
     * @param TimeZone $timeZone
     * @return static
     */
    public function addTimeZone( string $timeZoneId, TimeZone $timeZone ) : static
    {
        $this->timeZones[$timeZoneId] = $timeZone;
        ksort( $this->timeZones );
        return $this;
    }

    /**
     * @param TimeZone[] $timeZones  TimeZoneId[TimeZone]  here (map key) TimeZoneId same as TimeZone::tzId
     * @return static
     */
    public function setTimeZones( array $timeZones ) : static
    {
        foreach( $timeZones as $timeZoneId => $timeZone ) {
            $this->addTimeZone( $timeZoneId, $timeZone );
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getVirtualLocations() : array
    {
        return $this->virtualLocations;
    }

    /**
     * @return int
     */
    public function getVirtualLocationsCount() : int
    {
        return count( $this->virtualLocations );
    }

    /**
     * @param int|string|VirtualLocation $id
     * @param null|VirtualLocation $virtualLocation
     * @return static
     * @throws Exception
     */
    public function addVirtualLocation( int|string|VirtualLocation $id, ? VirtualLocation $virtualLocation = null ) : static
    {
        if( $id instanceof VirtualLocation ) {
            $virtualLocation = $id;
            $id              = self::getNewUid();
        }
        $this->virtualLocations[(string)$id] = $virtualLocation;
        return $this;
    }

    /**
     * @param VirtualLocation[] $virtualLocations
     * @return static
     * @throws Exception
     */
    public function setVirtualLocations( array $virtualLocations ) : static
    {
        foreach( $virtualLocations as $id => $virtualLocation ) {
            $this->addVirtualLocation( $id, $virtualLocation );
        }
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return null|bool
     */
    public function getUseDefaultAlerts( ? bool $defaultIfNotSet = false ) : ? bool
    {
        return ( ! $this->isUseDefaultAlertsSet() && $defaultIfNotSet )
            ? self::$useDefaultAlertsDefault
            : $this->useDefaultAlerts;
    }

    /**
     * Return bool true if useDefaultAlerts is not null
     *
     * @return bool
     */
    public function isUseDefaultAlertsSet() : bool
    {
        return ( null !== $this->useDefaultAlerts );
    }

    /**
     * @param bool $useDefaultAlerts
     * @return static
     */
    public function setUseDefaultAlerts( bool $useDefaultAlerts ) : static
    {
        $this->useDefaultAlerts = $useDefaultAlerts;
        return $this;
    }

    /**
     * @return array
     */
    public function getAlerts() : array
    {
        return $this->alerts;
    }

    /**
     * @return int
     */
    public function getAlertsCount() : int
    {
        return count( $this->alerts );
    }

    /**
     * @param string $id
     * @param Alert $alert
     * @return static
     */
    public function addAlert( string $id, Alert $alert ) : static
    {
        $this->alerts[$id] = $alert;
        return $this;
    }

    /**
     * @param Alert[] $alerts
     * @return static
     */
    public function setAlerts( array $alerts ) : static
    {
        foreach( $alerts as $id => $alert ) {
            $this->addAlert( $id,  $alert );
        }
        return $this;
    }

    /**
     * Return array, all Locations timezones
     *
     * @return string[]
     */
    public function getLocationsTimezones() : array
    {
        $timezones = [];
        foreach( $this->locations as $location ) {
            if( $location->isTimezoneSet()) {
                $tzid = $location->getTimezone();
                if( ! in_array( $tzid, $timezones, true )) {
                    $timezones[] = $tzid;
                }
            }
        } // end foreach
        return $timezones;
    }

    /** class static methods */

    /**
     * Modify DateTime from DateInterval, excludes microseconds, fraction
     *
     * @param DateTimeInterface     $dateTime
     * @param DateInterval $dateInterval
     * @return DateTimeInterface
     */
    protected static function modifyDateTimeFromDateInterval(
        DateTimeInterface $dateTime,
        DateInterval $dateInterval
    ) : DateTimeInterface
    {
        static $KEYS = [
            'y' => ' year',
            'm' => ' month',
            'd' => ' day',
            'h' => ' hour',
            'i' => ' minute',
            's' => ' second'
        ];
        static $invert   = 'invert';
        static $MINUS    = '-';
        static $PLUS     = '+';
        $dateTime        = clone $dateTime;
        $dateIntervalArr = (array) $dateInterval;
        $operator        = ( 0 < $dateIntervalArr[$invert] ) ? $MINUS : $PLUS;
        foreach( $KEYS as $diKey => $dtKey ) {
            if( 0 < $dateIntervalArr[$diKey] ) {
                $dateTime->modify(
                    self::getModifyString ( $operator, $dateIntervalArr[$diKey], $dtKey )
                );
            }
        }
        return $dateTime;
    }

    /**
     * Return DateTime modifier string
     *
     * @param string $operator
     * @param int    $number
     * @param string $unit
     * @return string
     */
    protected static function getModifyString ( string $operator, int $number, string $unit ) : string
    {
        return $operator . $number . $unit . self::getOptPluralSuffix( $number );
    }

    /**
     * Return opt modifier plural suffix
     *
     * @param int $number
     * @return string
     */
    protected static function getOptPluralSuffix ( int $number ) : string
    {
        static $PLS = 's';
        return ( 1 < $number ) ? $PLS : self::$SP0;
    }

    use DateInterval2StringTrait;
}
