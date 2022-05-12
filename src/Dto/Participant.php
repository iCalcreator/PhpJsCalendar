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

use DateTime;
use DateTimeInterface;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Traits\DescriptionTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\LinksTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\NameTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\PercentCompleteTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\ProgressTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\ProgressUpdatedTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\ReplayToSendToHelpTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\SentByTrait;

/**
 * Class Participant
 */
final class Participant extends BaseDto
{
    use NameTrait;

    /**
     * The email address to use to contact the participant or, for example, match with an address book entry
     *
     * If set, the value MUST be a valid "addr-spec" value as defined in Section 3.4.1 of [RFC5322].
     * Optional
     *
     * @var string|null
     */
    private ? string $email = null;

    use DescriptionTrait;

    /**
     * Represents methods by which the participant may receive the invitation and updates to the calendar object
     *
     * The keys in the property value are the available methods and
     * MUST only contain ASCII alphanumeric characters (A-Za-z0-9).
     * The value is a URI for the method specified in the key.
     * Optional
     *
     * @var mixed[]   String[String]
     */
    private array $sendTo = [];

    /**
     * What kind of entity this participant is, if known.
     *
     * "individual":  a single person
     * "group":  a collection of people invited as a whole
     * "location":  a physical location that needs to be scheduled, e.g., a conference room
     * "resource":  a non-human resource other than a location, such as a projector
     * OR another value registered in the IANA "JSCalendar Enum Values" registry
     * OR a vendor-specific value
     *
     * Optional
     *
     * @var string|null
     */
    private ? string $kind = null;

    /**
     * A set of roles that this participant fulfills
     *
     * "owner":         The participant is an owner of the object.  Thissignifies they have permission to make changes
     *                  to it that affect the other participants. Nonowner participants may only change properties that
     *                  affect only themselves (for example, setting their own alerts or changing their RSVP status).
     * "attendee":      The participant is expected to be present at the event.
     * "optional":      The participant's involvement with the event is optional.
     *                  This is expected to be primarily combined with the "attendee" role.
     * "informational": The participant is copied for informational reasons and is not expected to attend.
     * "chair":         The participant is in charge of the event/task when it occurs.
     * "contact":       The participant is someone that may be contacted for information about the event.
     * OR another value registered in the IANA "JSCalendar Enum Values" registry
     * OR a vendor-specific value
     *
     * Mandatory
     *
     * @var mixed[] String[Boolean]
     */
    private array $roles = [];

    /**
     * The location (id) at which this participant is expected to be attending
     *
     * Optional
     *
     * @var string|null
     */
    private ? string $locationId = null;

    /**
     * The language tag, as defined in [RFC5646], that best describes the participant's preferred language, if known
     *
     * Optional
     *
     * @var string|null
     */
    private ? string $language = null;

    /**
     * The participation status, if any, of this participant
     *
     * "needs-action":  No status has yet been set by the participant
     * "accepted":      The invited participant will participate
     * "declined":      The invited participant will not participate
     * "tentative":     The invited participant may participate
     * "delegated":     The invited participant has delegated their attendance to another participant,
     *                  as specified in the "delegatedTo" property
     * OR another value registered in the IANA "JSCalendar Enum Values" registry
     * OR a vendor-specific value
     *
     * Optional, default: "needs-action"
     *
     * @var string|null
     */
    private ? string $participationStatus = null;

    /**
     * @var string
     */
    public static string $participationStatusDefault = 'needs-action';

    /**
     * A note from the participant to explain their participation status
     *
     * Optional
     *
     * @var string|null
     */
    private ? string $participationComment = null;

    /**
     * If true, the organizer is expecting the participant to notify them of their participation status
     *
     * Optional, default: false
     *
     * @var null|bool
     */
    private ? bool $expectReply = null;

    /**
     * @var bool
     */
    public static bool $expectReplyDefault = false;

    /**
     * Who is responsible for sending scheduling messages with this calendar object to the participant
     *
     * "server":  The calendar server will send the scheduling messages
     * "client":  The calendar client will send the scheduling messages
     * "none":    No scheduling messages are to be sent to this participant
     * OR another value registered in the IANA "JSCalendar Enum Values" registry
     * OR a vendor-specific value
     *
     * Optional, default: "server"
     *
     * @var string|null
     */
    private ? string $scheduleAgent = null;

    /**
     * @var string
     */
    public static string $scheduleAgentDefault = 'server';

    /**
     * A client may set the property on a participant to true to request
     * that the server send a scheduling message to the participant when
     * it would not normally do so (e.g., if no significant change is
     * made the object or the scheduleAgent is set to client).
     *
     * The property MUST NOT be stored in the JSCalendar object on the server or appear in a scheduling message.
     *
     * Optional, default: false
     *
     * @var null|bool
     */
    private ? bool $scheduleForceSend = null;

    /**
     * @var bool
     */
    public static bool $scheduleForceSendDefault = false;

    /**
     * The sequence number of the last response from the participant
     *
     * Optional, default: 0
     *
     * @var null|int  UnsignedInt
     */
    private ? int $scheduleSequence = null;

    /**
     * @var int
     */
    public static int $scheduleSequenceDefault = 0;

    /**
     * A list of status codes, returned from the processing of the most recent scheduling message sent to this participant
     *
     * The status codes MUST be valid "statcode" values as defined in the ABNF in Section 3.8.8.3 of [RFC5545]
     * This property MUST NOT be included in scheduling messages.
     *
     * Optional
     *
     * @var String[]
     */
    private array $scheduleStatus = [];

    /**
     * The timestamp for the most recent response from this participant (...when using iTIP)
     *
     * Optional
     *
     * @var DateTimeInterface|null   UTCDateTime
     */
    private ? DateTimeInterface $scheduleUpdated = null;

    use SentByTrait;

    /**
     * The id of the participant who added this participant to the event/task, if known
     *
     * Optional
     *
     * @var string|null   Id
     */
    private ? string $invitedBy = null;

    /**
     * Participant ids that this participant has delegated their participation to
     *
     * Each key in the set MUST be the id of a participant.
     * The value for each key in the map MUST be true.
     *
     * Optional
     *
     * @var mixed[]   Id[Boolean]
     */
    private array $delegatedTo = [];

    /**
     * Set of participant ids that this participant is acting as a delegate for
     *
     * Each key in the set MUST be the id of a participant.
     * The value for each key in the map MUST be true.
     *
     * Optional
     *
     * @var mixed[]   Id[Boolean]
     */
    private array $delegatedFrom = [];

    /**
     * A set of group participants that were invited to this calendar object,
     * which caused this participant to be invited due to their membership in the group(s)
     *
     * Each key in the set MUST be the id of a participant.
     * The value for each key in the map MUST be true.
     *
     * Optional
     *
     * @var mixed[]   Id[Boolean]
     */
    private array $memberOf = [];

    use LinksTrait;

    /**
     * The progress of the participant for a task
     */
    use ProgressTrait;

    /**
     * Specifies the date-time the "progress" property was last set on this participant, only for participants of a Task
     */
    use ProgressUpdatedTrait;

    /**
     * Represents the percent completion of the participant for this task
     */
    use PercentCompleteTrait;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::PARTCIPANT;
    }

    /**
     * Class factory method
     *
     * @param string|null $email
     * @param string|null $name
     * @return static
     */
    public static function factory( ? string $email = null, ? string $name = null ) : static
    {
        $instance= new self();
        if( null !== $email ) {
            $instance->setEmail( $email );
        }
        if( null !== $name ) {
            $instance->setName( $name );
        }
        return $instance;
    }

    /**
     * @return string|null
     */
    public function getEmail() : ? string
    {
        return $this->email;
    }

    /**
     * Return bool true if email is not null
     *
     * @return bool
     */
    public function isEmailSet() : bool
    {
        return ( null !== $this->email );
    }

    /**
     * @param null|string $email
     * @return static
     */
    public function setEmail( ? string $email ) : static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getSendTo() : array
    {
        return $this->sendTo;
    }

    /**
     * @return mixed[]
     */
    public function getSendToMethods() : array
    {
        return array_keys( $this->sendTo );
    }

    /**
     * @return int
     */
    public function getSendToCount() : int
    {
        return count( $this->sendTo );
    }

    /**
     * @param string $method
     * @param string $uri
     * @return static
     */
    public function addSendTo( string $method, string $uri ) : static
    {
        self::assureOptValuePrefix( $uri, $method );
        $this->sendTo[$method] = $uri;
        return $this;
    }

    use ReplayToSendToHelpTrait;

    /**
     * @param string[] $sendTo   *(method => uri)
     * @return static
     */
    public function setSendTo( array $sendTo ) : static
    {
        foreach( $sendTo as $method => $theSendTo ) {
            $this->addSendTo( $method, $theSendTo );
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKind() : ?string
    {
        return $this->kind;
    }

    /**
     * Return bool true if kind is not null
     *
     * @return bool
     */
    public function isKindSet() : bool
    {
        return ( null !== $this->kind );
    }

    /**
     * @param null|string $kind
     * @return static
     */
    public function setKind( ? string $kind ) : static
    {
        $this->kind = $kind ? strtolower( $kind ) : null;
        return $this;
    }

    /**
     * @return mixed[] String[Boolean]
     */
    public function getRoles() : array
    {
        return $this->roles;
    }

    /**
     * @return int
     */
    public function getRolesCount() : int
    {
        return count( $this->roles );
    }

    /**
     * @param string $role
     * @param null|bool $bool default true
     * @return static
     */
    public function addRole( string $role, ? bool $bool = true ) : static
    {
        $this->roles[strtolower( $role )] = $bool;
        return $this;
    }

    /**
     * @param mixed[] $roles  String[Boolean] or string[]
     * @return static
     */
    public function setRoles( array $roles ) : static
    {
        foreach( $roles as $key => $value ) {
            if( is_string( $key ) && ! is_numeric( $key ) && is_bool( $value )) {
                $this->addRole( $key, $value );
            }
            else {
                $this->addRole( $value );
            }
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocationId() : ? string
    {
        return $this->locationId;
    }

    /**
     * Return bool true if locationId is not null
     *
     * @return bool
     */
    public function isLocationIdSet() : bool
    {
        return ( null !== $this->locationId );
    }

    /**
     * @param null|string $locationId
     * @return static
     */
    public function setLocationId( ? string $locationId ) : static
    {
        $this->locationId = $locationId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguage() : ?string
    {
        return $this->language;
    }

    /**
     * Return bool true if language is not null
     *
     * @return bool
     */
    public function isLanguageSet() : bool
    {
        return ( null !== $this->language );
    }

    /**
     * @param null|string $language
     * @return static
     */
    public function setLanguage( ? string $language ) : static
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getParticipationStatus( ? bool $defaultIfNotSet = false ) : ?string
    {
        return ( ! $this->isParticipationStatusSet() && $defaultIfNotSet )
            ? self::$participationStatusDefault
            : $this->participationStatus;
    }
    /**
     * Return bool true if participationStatus is not null
     *
     * @return bool
     */
    public function isParticipationStatusSet() : bool
    {
        return ( null !== $this->participationStatus );
    }

    /**
     * @param null|string $participationStatus
     * @return static
     */
    public function setParticipationStatus( ? string $participationStatus ) : static
    {
        $this->participationStatus = strtolower( $participationStatus );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getParticipationComment() : ? string
    {
        return $this->participationComment;
    }

    /**
     * Return bool true if participationComment is not null
     *
     * @return bool
     */
    public function isParticipationCommentSet() : bool
    {
        return ( null !== $this->participationComment );
    }

    /**
     * @param null|string $participationComment
     * @return static
     */
    public function setParticipationComment( ? string $participationComment ) : static
    {
        $this->participationComment = $participationComment;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return null|bool
     */
    public function getExpectReply( ? bool $defaultIfNotSet = false ) : ? bool
    {
        return ( ! $this->isExpectReplySet() && $defaultIfNotSet )
            ? self::$expectReplyDefault
            : $this->expectReply;
    }

    /**
     * Return bool true if expectReply is not null
     *
     * @return bool
     */
    public function isExpectReplySet() : bool
    {
        return ( null !== $this->expectReply );
    }

    /**
     * @param bool $expectReply
     * @return static
     */
    public function setExpectReply( bool $expectReply ) : static
    {
        $this->expectReply = $expectReply;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getScheduleAgent( ? bool $defaultIfNotSet = false ) : ? string
    {
        return ( ! $this->isScheduleAgentSet() && $defaultIfNotSet )
            ? self::$scheduleAgentDefault
            : $this->scheduleAgent;
    }

    /**
     * Return bool true if scheduleAgent is not null
     *
     * @return bool
     */
    public function isScheduleAgentSet() : bool
    {
        return ( null !== $this->scheduleAgent );
    }

    /**
     * @param null|string $scheduleAgent
     * @return static
     */
    public function setScheduleAgent( ? string $scheduleAgent ) : static
    {
        $this->scheduleAgent = strtolower( $scheduleAgent );
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return null|bool
     */
    public function getScheduleForceSend( ? bool $defaultIfNotSet = false ) : ? bool
    {
        return ( ! $this->isScheduleForceSendSet() && $defaultIfNotSet )
            ? self::$scheduleForceSendDefault
            : $this->scheduleForceSend;
    }

    /**
     * Return bool true if scheduleForceSend is not null
     *
     * @return bool
     */
    public function isScheduleForceSendSet() : bool
    {
        return ( null !== $this->scheduleForceSend );
    }

    /**
     * @param bool $scheduleForceSend
     * @return static
     */
    public function setScheduleForceSend( bool $scheduleForceSend ) : static
    {
        $this->scheduleForceSend = $scheduleForceSend;
        return $this;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return null|int
     */
    public function getScheduleSequence( ? bool $defaultIfNotSet = false ) : ? int
    {
        return ( ! $this->isScheduleSequenceSet() && $defaultIfNotSet )
            ? self::$scheduleSequenceDefault
            : $this->scheduleSequence;
    }

    /**
     * Return bool true if scheduleSequence is not null
     *
     * @return bool
     */
    public function isScheduleSequenceSet() : bool
    {
        return ( null !== $this->scheduleSequence );
    }

    /**
     * @param int $scheduleSequence
     * @return static
     */
    public function setScheduleSequence( int $scheduleSequence ) : static
    {
        $this->scheduleSequence = $scheduleSequence;
        return $this;
    }

    /**
     * @return String[]
     */
    public function getScheduleStatus() : array
    {
        return $this->scheduleStatus;
    }

    /**
     * @return int
     */
    public function getScheduleStatusCount() : int
    {
        return count( $this->scheduleStatus );
    }

    /**
     * @param String $scheduleStatus
     * @return static
     */
    public function addScheduleStatus( string $scheduleStatus ) : static
    {
        $this->scheduleStatus[] = $scheduleStatus;
        return $this;
    }

    /**
     * @param String[] $scheduleStatus
     * @return static
     */
    public function setScheduleStatus( array $scheduleStatus ) : static
    {
        foreach( $scheduleStatus as $scheduleSts ) {
            $this->addScheduleStatus( $scheduleSts );
        }
        return $this;
    }

    /**
     * @param null|bool $asString   default true
     * @return null|string|DateTimeInterface   DateTime in UTC, string with suffix 'Z'
     */
    public function getScheduleUpdated( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        return (( $this->scheduleUpdated instanceof DateTimeInterface ) && $asString )
            ? $this->scheduleUpdated->format( BaseDto::$UTCDateTimeFMT )
            : $this->scheduleUpdated;
    }

    /**
     * Return bool true if scheduleUpdated is not null
     *
     * @return bool
     */
    public function isScheduleUpdatedSet() : bool
    {
        return ( null !== $this->scheduleUpdated );
    }

    /**
     * Set scheduleUpdated
     *
     * If empty, UTC date-time now
     * If DateTime, any timezone allowed, converted to UTC DateTime
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param null|string|DateTimeInterface $scheduleUpdated UTCDateTime
     * @return static
     * @throws Exception
     */
    public function setScheduleUpdated( null | string | DateTimeInterface $scheduleUpdated = null ) : static
    {
        $this->scheduleUpdated = self::toUtcDateTime( $scheduleUpdated ?? new DateTime(), false );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInvitedBy() : ?string
    {
        return $this->invitedBy;
    }

    /**
     * Return bool true if invitedBy is not null
     *
     * @return bool
     */
    public function isInvitedBySet() : bool
    {
        return ( null !== $this->invitedBy );
    }

    /**
     * @param null|string $invitedBy
     * @return static
     */
    public function setInvitedBy( ? string $invitedBy ) : static
    {
        $this->invitedBy = $invitedBy;
        return $this;
    }

    /**
     * @return mixed[]  Id[Boolean]
     */
    public function getDelegatedTo() : array
    {
        return $this->delegatedTo;
    }

    /**
     * @return int
     */
    public function getDelegatedToCount() : int
    {
        return count( $this->delegatedTo );
    }

    /**
     * @param string $delegatedTo  Id[Boolean] or string[]
     * @param null|bool $bool
     * @return static
     */
    public function addDelegatedTo( string $delegatedTo, ? bool $bool = true ) : static
    {
        $this->delegatedTo[$delegatedTo] = $bool;
        return $this;
    }

    /**
     * @param array $delegatedTo  Id[Boolean] or string[]
     * @return static
     */
    public function setDelegatedTo( array $delegatedTo ) : static
    {
        foreach( $delegatedTo as $key => $value ) {
            if( is_string( $key ) && ! is_numeric( $key ) && is_bool( $value )) {
                $this->addDelegatedTo( $key, $value );
            }
            else {
                $this->addDelegatedTo( $value );
            }
        }
        return $this;
    }

    /**
     * @return array Id[Boolean]
     */
    public function getDelegatedFrom() : array
    {
        return $this->delegatedFrom;
    }

    /**
     * @return int
     */
    public function getDelegatedFromCount() : int
    {
        return count( $this->delegatedFrom );
    }

    /**
     * @param string $delegatedFrom
     * @param null|bool $bool
     * @return static
     */
    public function addDelegatedFrom( string $delegatedFrom, ? bool $bool = true ) : static
    {
        $this->delegatedFrom[$delegatedFrom] = $bool;
        return $this;
    }

    /**
     * @param array $delegatedFrom  Id[Boolean] or string[]
     * @return static
     */
    public function setDelegatedFrom( array $delegatedFrom ) : static
    {
        foreach( $delegatedFrom as $key => $value ) {
            if( is_string( $key ) && ! is_numeric( $key ) && is_bool( $value )) {
                $this->addDelegatedFrom( $key, $value );
            }
            else {
                $this->addDelegatedFrom( $value );
            }
        }
        return $this;
    }

    /**
     * @return array  Id[Boolean]
     */
    public function getMemberOf() : array
    {
        return $this->memberOf;
    }

    /**
     * @return int
     */
    public function getMemberOfCount() : int
    {
        return count( $this->memberOf );
    }

    /**
     * @param string $memberOf
     * @param null|bool $bool
     * @return static
     */
    public function addMemberOf( string $memberOf, ? bool $bool = true ) : static
    {
        $this->memberOf[$memberOf] = $bool;
        return $this;
    }

    /**
     * @param array $memberOf Id[Boolean] or string[]
     * @return static
     */
    public function setMemberOf( array $memberOf ) : static
    {
        foreach( $memberOf as $key => $value ) {
            if( is_string( $key ) && ! is_numeric( $key ) && is_bool( $value )) {
                $this->addMemberOf( $key, $value );
            }
            else {
                $this->addMemberOf( $value );
            }
        }
        return $this;
    }
}
