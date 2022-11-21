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
namespace Kigkonsult\PhpJsCalendar;

interface BaseInterface
{
    /**
     * PhpJsCalendar version
     */
    public const VERSION = '1.2';

    /**
     * Object types
     */
    public const ABSOLUTETRIGGER = 'AbsoluteTrigger';
    public const ALERT           = 'Alert';
    public const EVENT           = 'Event';
    public const GROUP           = 'Group';
    public const LINK            = 'Link';
    public const LOCATION        = 'Location';
    public const NDAY            = 'NDay';
    public const OFFSETTRIGGER   = 'OffsetTrigger';
    public const PARTCIPANT      = 'Participant';
    public const PATCHOBJECT     = 'PatchObject';
    public const RECURRENCERULE  = 'RecurrenceRule';
    public const RELATION        = 'Relation';
    public const TASK            = 'Task';
    public const TIMEZONE        = 'TimeZone';
    public const TIMEZONERULE    = 'TimeZoneRule';
    public const UNKNOWNTRIGGER  = 'UnknownTrigger';
    public const VIRTUALLOCATION = 'VirtualLocation';

    /**
     * json array keys, property names (and default value)
     */
    public const OBJECTTYPE              = '@type';

    public const ACKNOWLEDGED            = 'acknowledged';
    public const ACTION                  = 'action';
    public const ALERTS                  = 'alerts';
    public const ALIASES                 = 'aliases';
    public const BYDAY                   = 'byDay';
    public const BYHOUR                  = 'byHour';
    public const BYMINUTE                = 'byMinute';
    public const BYMONTH                 = 'byMonth';
    public const BYMONTHDAY              = 'byMonthDay';
    public const BYSECOND                = 'bySecond';
    public const BYSETPOSITION           = 'bySetPosition';
    public const BYWEEKNO                = 'byWeekNo';
    public const BYYEARDAY               = 'byYearDay';
    public const CATEGORIES              = 'categories';
    public const CID                     = 'cid';
    public const COLOR                   = 'color';
    public const COMMENTS                = 'comments';
    public const CONTENTTYPE             = 'contentType';
    public const COORDINATES             = 'coordinates';
    public const COUNT                   = 'count';
    public const CREATED                 = 'created';
    public const DAY                     = 'day';
    public const DAYLIGHT                = 'daylight';
    public const DELEGATEDFROM           = 'delegatedFrom';
    public const DELEGATEDTO             = 'delegatedTo';
    public const DESCRIPTION             = 'description';
    public const DESCRIPTIONCONTENTTYPE  = 'descriptionContentType';
    public const DISPLAY                 = 'display';
    public const DUE                     = 'due';
    public const DURATION                = 'duration';
    public const EMAIL                   = 'email';
    public const END                     = 'end';
    public const ENTRIES                 = 'entries';
    public const ESTIMATEDDURATION       = 'estimatedDuration';
    public const EXCLUDED                = 'excluded';
    public const EXCLUDEDRECURRENCERULES = 'excludedRecurrenceRules';
    public const EXPECTREPLY             = 'expectReply';
    public const FEATURES                = 'features';
    public const FIRSTDAYOFWEEK          = 'firstDayOfWeek';
    public const FREEBUSYSTATUS          = 'freeBusyStatus';
    public const FREQUENCY               = 'frequency';
    public const HREF                    = 'href';
    public const INTERVAL                = 'interval';
    public const INVITEDBY               = 'invitedBy';
    public const KEYWORDS                = 'keywords';
    public const KIND                    = 'kind';
    public const LANGUAGE                = 'language';
    public const LINKS                   = 'links';
    public const LOCALE                  = 'locale';
    public const LOCALIZATIONS           = 'localizations';
    public const LOCATIONID              = 'locationId';
    public const LOCATIONS               = 'locations';
    public const LOCATIONTYPES           = 'locationTypes';
    public const MEMBEROF                = 'memberOf';
    public const METHOD                  = 'method';
    public const NAME                    = 'name';
    public const NAMES                   = 'names';
    public const NTHOFPERIOD             = 'nthOfPeriod';
    public const OFFSET                  = 'offset';
    public const OFFSETFROM              = 'offsetFrom';
    public const OFFSETTO                = 'offsetTo';
    public const PARTICIPANTS            = 'participants';
    public const PARTICIPATIONCOMMENT    = 'participationComment';
    public const PARTICIPATIONSTATUS     = 'participationStatus';
    public const PERCENTCOMPLETE         = 'percentComplete';
    public const PRIORITY                = 'priority';
    public const PRIVACY                 = 'privacy';
    public const PRODID                  = 'prodId';
    public const PROGRESS                = 'progress';
    public const PROGRESSUPDATED         = 'progressUpdated';
    public const RECURRENCEID            = 'recurrenceId';
    public const RECURRENCEIDTIMEZONE    = 'recurrenceIdTimeZone';
    public const RECURRENCEOVERRIDES     = 'recurrenceOverrides';
    public const RECURRENCERULES         = 'recurrenceRules';
    public const REL                     = 'rel';
    public const RELATEDTO               = 'relatedTo';
    public const RELATIoN                = 'relation';
    public const RELATIVETO              = 'relativeTo';
    public const REPLYTO                 = 'replyTo';
    public const REQUESTSTATUS           = 'requestStatus';
    public const ROLES                   = 'roles';
    public const RSCALE                  = 'rscale';
    public const SENTBY                  = 'sentBy';
    public const STANDARD                = 'standard';
    public const SCHEDULEAGENT           = 'scheduleAgent';
    public const SCHEDULEFORCESEND       = 'scheduleForceSend';
    public const SCHEDULESEQUENCE        = 'scheduleSequence';
    public const SCHEDULESTATUS          = 'scheduleStatus';
    public const SCHEDULEUPDATED         = 'scheduleUpdated';
    public const SENDTO                  = 'sendTo';
    public const SEQUENCE                = 'sequence';
    public const SHOWWITHOUTTIME         = 'showWithoutTime';
    public const SIZE                    = 'size';
    public const SKIP                    = 'skip';
    public const SOURCE                  = 'source';
    public const START                   = 'start';
    public const STATUS                  = 'status';
    public const TIMEzONE                = 'timeZone';
    public const TIMEZONES               = 'timeZones';
    public const TITLE                   = 'title';
    public const TRIGGER                 = 'trigger';
    public const TZID                    = 'tzId';
    public const UID                     = 'uid';
    public const UNTIL                   = 'until';
    public const UPDATED                 = 'updated';
    public const URI                     = 'uri';
    public const URL                     = 'url';
    public const USEDEFAULTALERTS        = 'useDefaultAlerts';
    public const VALIDUNTIL              = 'validUntil';
    public const VIRTUALLOCATIONS        = 'virtualLocations';
    public const WHEN                    = 'when';
}
