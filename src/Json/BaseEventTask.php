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
namespace Kigkonsult\PhpJsCalendar\Json;

use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Task  as TaskDto;
use stdClass;

abstract class BaseEventTask extends BaseGroupEventTask
{
    /**
     * Parse json array for common properties to update Event|Task
     *
     * @param string[]|string[][] $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    protected static function eventTaskParse( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::DESCRIPTION] )) {
            $dto->setDescription( $jsonArray[self::DESCRIPTION] );
        }
        if( isset( $jsonArray[self::DESCRIPTIONCONTENTTYPE] )) {
            $dto->setDescriptionContentType( $jsonArray[self::DESCRIPTIONCONTENTTYPE] );
        }
        if( isset( $jsonArray[self::EXCLUDED] )) {
            $dto->setExcluded( self::jsonBool2Php( $jsonArray[self::EXCLUDED] ));
        }
        self::extractExcludedRecurrenceRule( $jsonArray, $dto );
        if( isset( $jsonArray[self::FREEBUSYSTATUS] )) {
            $dto->setFreeBusyStatus( $jsonArray[self::FREEBUSYSTATUS] );
        }
        self::extractLocalization( $jsonArray, $dto );
        self::extractLocation( $jsonArray, $dto );
        if( isset( $jsonArray[self::METHOD] )) {
            $dto->setMethod( $jsonArray[self::METHOD] );
        }
        self::extractParticipant( $jsonArray, $dto );
        if( isset( $jsonArray[self::PRIORITY] )) {
            $dto->setPriority((int) $jsonArray[self::PRIORITY] );
        }
        if( isset( $jsonArray[self::PRIVACY] )) {
            $dto->setPrivacy( $jsonArray[self::PRIVACY] );
        }
        if( isset( $jsonArray[self::RECURRENCEID] )) {
            $dto->setRecurrenceId( $jsonArray[self::RECURRENCEID] );
        }
        if( isset( $jsonArray[self::RECURRENCEIDTIMEZONE] )) {
            $dto->setRecurrenceIdTimeZone( $jsonArray[self::RECURRENCEIDTIMEZONE] );
        }
        self::extractRecurrenceRule( $jsonArray, $dto );
        self::extractRecurrenceOverride( $jsonArray, $dto );
        self::extractRelatedTo( $jsonArray, $dto );
        self::extractReplyTo( $jsonArray, $dto );
        if( isset( $jsonArray[self::REQUESTSTATUS] )) {
            $dto->setRequestStatus( $jsonArray[self::REQUESTSTATUS] );
        }
        if( isset( $jsonArray[self::SENTBY] )) {
            $dto->setSentBy( $jsonArray[self::SENTBY] );
        }
        if( isset( $jsonArray[self::SEQUENCE] )) {
            $dto->setSequence((int) $jsonArray[self::SEQUENCE] );
        }
        if( isset( $jsonArray[self::SHOWWITHOUTTIME] )) {
            $dto->setShowWithoutTime( self::jsonBool2Php( $jsonArray[self::SHOWWITHOUTTIME] ));
        }
        if( isset( $jsonArray[self::START] )) {
            $dto->setStart( $jsonArray[self::START] );
        }
        if( isset( $jsonArray[self::TIMEzONE] )) { // timeZoneId
            $dto->setTimeZone( $jsonArray[self::TIMEzONE] );
        }
        self::extractTimeZone( $jsonArray, $dto );
        self::extractVirtualLocation( $jsonArray, $dto );
        if( isset( $jsonArray[self::USEDEFAULTALERTS] )) {
            $dto->setUseDefaultAlerts( self::jsonBool2Php( $jsonArray[self::USEDEFAULTALERTS] ));
        }
        self::extractAlert( $jsonArray, $dto );
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractExcludedRecurrenceRule( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::EXCLUDEDRECURRENCERULES] )) {
            foreach( $jsonArray[self::EXCLUDEDRECURRENCERULES] as $excludedRecurrenceRule ) {
                $dto->addExcludedRecurrenceRule( RecurrenceRule::parse( $excludedRecurrenceRule ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractLocalization( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::LOCALIZATIONS] )) {
            foreach( $jsonArray[self::LOCALIZATIONS] as $languageTag => $patchObject ) {
                $dto->addLocalization( $languageTag, PatchObject::parse( $patchObject ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractLocation( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::LOCATIONS] )) {
            foreach( $jsonArray[self::LOCATIONS] as $id => $location ) {
                $dto->addLocation( $id, Location::parse( $location ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractParticipant( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::PARTICIPANTS] )) {
            foreach( $jsonArray[self::PARTICIPANTS] as $pix => $participant ) {
                $dto->addParticipant( $pix, Participant::parse( $participant ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractRecurrenceRule( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::RECURRENCERULES] )) {
            foreach( $jsonArray[self::RECURRENCERULES] as $recurrenceRule ) {
                $dto->addRecurrenceRule( RecurrenceRule::parse( $recurrenceRule ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractRecurrenceOverride( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::RECURRENCEOVERRIDES] )) {
            foreach( $jsonArray[self::RECURRENCEOVERRIDES] as $localDateTime => $patchObject) {
                $dto->addRecurrenceOverride( $localDateTime, PatchObject::parse( $patchObject ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractRelatedTo( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::RELATEDTO] )) {
            foreach( $jsonArray[self::RELATEDTO] as $rix => $relation ) {
                $dto->addRelatedTo( $rix, Relation::parse( $relation ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractReplyTo( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::REPLYTO] )) {
            foreach( $jsonArray[self::REPLYTO] as $method => $replyTo ) {
                $dto->addReplyTo( $method, $replyTo );
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractTimeZone( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::TIMEZONES] )) {
            foreach( $jsonArray[self::TIMEZONES] as $timeZoneId => $timeZone ) {
                $dto->addTimeZone( $timeZoneId, TimeZone::parse( $timeZone ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractVirtualLocation( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::VIRTUALLOCATIONS] )) {
            foreach( $jsonArray[self::VIRTUALLOCATIONS] as $id => $virtualLocation ) {
                $dto->addVirtualLocation( $id, VirtualLocation::parse( $virtualLocation ));
            }
        }
    }

    /**
     * @param array $jsonArray
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractAlert( array $jsonArray, EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::ALERTS] )) {
            foreach( $jsonArray[self::ALERTS] as $id => $alert ) {
                $dto->addAlert( $id, Alert::parse( $alert ));
            }
        }
    }

    /**
     * Write Event|Task common Dto properties to json array
     *
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    protected static function eventTaskWrite( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        if( $dto->isDescriptionSet()) {
            $jsonArray[self::DESCRIPTION] = $dto->getDescription();
        }
        if( $dto->isDescriptionContentTypeSet()) {
            $jsonArray[self::DESCRIPTIONCONTENTTYPE] = $dto->getDescriptionContentType();
        }
        if( $dto->isExcludedSet() && $dto->getExcluded()) { // skip default false
            $jsonArray[self::EXCLUDED] = self::phpBool2Json( true );
        }
        if( $dto->isFreeBusyStatusSet()) {
            $jsonArray[self::FREEBUSYSTATUS] = $dto->getFreeBusyStatus();
        }
        if( $dto->isMethodSet()) {
            $jsonArray[self::METHOD] = $dto->getMethod();
        }
        if( $dto->isPrioritySet()) {
            $jsonArray[self::PRIORITY] = $dto->getPriority();
        }
        if( $dto->isPrivacySet()) {
            $jsonArray[self::PRIVACY] = $dto->getPrivacy();
        }
        if( $dto->isRecurrenceIdSet()) {
            $jsonArray[self::RECURRENCEID] = $dto->getRecurrenceId();
        }
        if( $dto->isRecurrenceIdTimeZoneSet()) {
            $jsonArray[self::RECURRENCEIDTIMEZONE] = $dto->getRecurrenceIdTimeZone();
        }
        if( $dto->isRequestStatusSet()) {
            $jsonArray[self::REQUESTSTATUS] = $dto->getRequestStatus();
        }
        if( $dto->isSentBySet()) {
            $jsonArray[self::SENTBY] = $dto->getSentBy();
        }
        if( $dto->isSequenceSet()) {
            $jsonArray[self::SEQUENCE] = $dto->getSequence();
        }
        if( $dto->isShowWithoutTimeSet() && $dto->getShowWithoutTime()) { // skip default false
            $jsonArray[self::SHOWWITHOUTTIME] = true;
        }
        if( $dto->isStartSet()) {
            $jsonArray[self::START] = $dto->getStart();
        }
        if( $dto->isTimeZoneSet()) {
            $jsonArray[self::TIMEzONE] = $dto->getTimeZone();
        }
        self::extractJsExcludedRecurrenceRules( $dto, $jsonArray );
        self::extractJsLocalizations( $dto, $jsonArray );
        self::extractJsLocations( $dto, $jsonArray );
        self::extractJsParticipants( $dto, $jsonArray );
        self::extractJsRecurrenceRules( $dto, $jsonArray );
        self::extractJsRecurrenceOverrides( $dto, $jsonArray );
        self::extractJsRelatedTos( $dto, $jsonArray );
        self::extractJsReplyTos( $dto, $jsonArray );
        self::extractJsTimeZones( $dto, $jsonArray );
        self::extractJsVirtualLocations( $dto, $jsonArray );
        self::extractJsAlerts( $dto, $jsonArray );
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsExcludedRecurrenceRules( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of RecurrenceRule[]
        if( ! empty( $dto->getExcludedRecurrenceRulesCount())) {
            foreach( $dto->getExcludedRecurrenceRules() as $x => $excludedRecurrenceRule ) {
                $jsonArray[self::EXCLUDEDRECURRENCERULES][$x] = (object)RecurrenceRule::write( $excludedRecurrenceRule );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsLocalizations( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "String[PatchObject]"
        if( ! empty( $dto->getLocalizationsCount())) {
            $jsonArray[self::LOCALIZATIONS] = new stdClass();
            foreach( $dto->getLocalizations() as $languageTag => $patchObject ) {
                $jsonArray[self::LOCALIZATIONS]->{$languageTag} = (object) PatchObject::write( $patchObject );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsLocations( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "Id[Location]"
        if( ! empty( $dto->getLocationsCount())) {
            $jsonArray[self::LOCATIONS] = new stdClass();
            foreach( $dto->getLocations() as $id => $location ) {
                $jsonArray[self::LOCATIONS]->{$id} = (object) Location::write( $location );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsParticipants( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "Id[Participant]"
        if( ! empty( $dto->getParticipantsCount())) {
            $jsonArray[self::PARTICIPANTS] = new stdClass();
            foreach( $dto->getParticipants() as $id => $participant ) {
                $jsonArray[self::PARTICIPANTS]->{$id} = (object) Participant::write( $participant );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsRecurrenceRules( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "RecurrenceRule[]"
        if( ! empty( $dto->getRecurrenceRulesCount())) {
            foreach( $dto->getRecurrenceRules() as $x => $recurrenceRule ) {
                $jsonArray[self::RECURRENCERULES][$x] = (object)RecurrenceRule::write( $recurrenceRule );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsRecurrenceOverrides( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "LocalDateTime[PatchObject]"
        if( ! empty( $dto->getRecurrenceOverridesCount())) {
            $jsonArray[self::RECURRENCEOVERRIDES] = new stdClass();
            foreach( $dto->getRecurrenceOverrides() as $localDateTime => $patchObject ) {
                $jsonArray[self::RECURRENCEOVERRIDES]->{$localDateTime} = (object) PatchObject::write( $patchObject );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsRelatedTos( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "String[Relation]"
        if( ! empty( $dto->getRelatedToCount())) {
            $jsonArray[self::RELATEDTO] = new stdClass();
            foreach( $dto->getRelatedTo() as $uid => $relation ) {
                $jsonArray[self::RELATEDTO]->{$uid} = (object) Relation::write( $relation );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsReplyTos( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "String[String]"
        if( ! empty( $dto->getReplyToCount())) {
            foreach( $dto->getReplyTo() as $method => $replyTo ) {
                $jsonArray[self::REPLYTO][$method] = $replyTo;
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsTimeZones( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "TimeZoneId[TimeZone]"
        if( ! empty( $dto->getTimeZonesCount())) {
            $jsonArray[self::TIMEZONES] = new stdClass();
            foreach( $dto->getTimeZones() as $timeZoneId => $timeZone ) {
                $jsonArray[self::TIMEZONES]->{$timeZoneId} = (object) TimeZone::write( $timeZone );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsVirtualLocations( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        // array of "Id[VirtualLocation]"
        if( ! empty( $dto->getVirtualLocationsCount())) {
            $jsonArray[self::VIRTUALLOCATIONS] = new stdClass();
            foreach( $dto->getVirtualLocations() as $id => $virtualLocation ) {
                $jsonArray[self::VIRTUALLOCATIONS]->{$id} = (object) VirtualLocation::write( $virtualLocation );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    private static function extractJsAlerts( EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        if( $dto->isUseDefaultAlertsSet() && $dto->getUseDefaultAlerts()) { // skip default false
            $jsonArray[self::USEDEFAULTALERTS] = self::phpBool2Json( true );
        }
        if( ! empty( $dto->getAlertsCount())) {
            $jsonArray[self::ALERTS] = new stdClass();
            foreach( $dto->getAlerts() as $id => $alert ) {
                $jsonArray[self::ALERTS]->{$id} = (object) Alert::write( $alert );
            }
        }
    }

}
