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
namespace Kigkonsult\PhpJsCalendar\Ical;

use DateTime;
use DateTimeZone;
use Exception;
use Kigkonsult\Icalcreator\CalendarComponent as IcalComponent;
use Kigkonsult\Icalcreator\Participant       as IcalParticipant;
use Kigkonsult\Icalcreator\Vevent            as IcalVevent;
use Kigkonsult\Icalcreator\Vlocation         as IcalVlocation;
use Kigkonsult\Icalcreator\Vtimezone         as IcalVtimezone;
use Kigkonsult\Icalcreator\Vtodo             as IcalVtodo;
use Kigkonsult\PhpJsCalendar\Dto\Event       as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Participant as ParticipantDto;
use Kigkonsult\PhpJsCalendar\Dto\Task        as TaskDto;

abstract class BaseEventTask extends BaseGroupEventTask
{
    /**
     * Event|Task properties to Vevent/Vtodo properties
     *
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @return array   [ *id[IcalVtimezone], startDateTime ]
     * @throws Exception
     */
    protected static function eventTaskProcessToIcal(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : array
    {
        if( $dto->isMethodSet()) {
            $iCal->setXprop( self::setXPrefix( self::METHOD ), $dto->getMethod());
        }
        $locale = $dto->getLocale();
        self::extractJsDescription( $dto, $iCal, $locale );
        self::extractJsExcluded( $dto, $iCal );
        // create Vfreebusy ?
        if( $dto->isFreeBusyStatusSet()) {
            $iCal->setXprop( self::setXPrefix( self::FREEBUSYSTATUS ), $dto->getFreeBusyStatus());
        }
        if( $dto->isPrioritySet()) {
            $iCal->setPriority( $dto->getPriority());
        }
        if( $dto->isPrivacySet()) {
            $iCal->setClass( strtoupper( $dto->getPrivacy()));
        }
        self::extractJsRecurrenceId( $dto, $iCal );
        self::extractJsRequestStatus( $dto, $iCal );
        if( $dto->isSentBySet()) { // ?? organizer
            $iCal->setXprop( self::setXPrefix( self::SENTBY ), $dto->getSentBy());
        }
        if( $dto->isSequenceSet()) {
            $iCal->setSequence( $dto->getSequence());
        }
        [ $startDateTime, $tzid ] = self::extractJsStartTzid( $dto, $iCal );
        self::extractJsExcludedRecurrence( $dto, $iCal, $tzid );
        self::extractJsLocalizations( $dto, $iCal );

        $vLocations     = self::extractJsLocations( $dto, $locale ); // lid[Vlocations]
        $pVlocationLids = self::extractJsParticipants( $dto, $iCal, $vLocations );
        self::setOneIcalLocation( $iCal, $vLocations, $pVlocationLids, $locale );
        self::setIcalVlocations( $iCal, $vLocations, $pVlocationLids );

        self::extractJsRecurrenceRules( $dto, $iCal, $tzid );
        self::extractJsRecurrenceOverrides( $dto, $iCal );
        self::extractJsRelatedTo( $dto, $iCal );
        self::extractJsReplyTo( $dto, $iCal );
        self::extractJsVirtualLocations( $dto, $iCal );
        $iCalVtimezones = self::extractJsTimeZones( $dto );
        self::extractJsAlerts( $dto, $iCal );
        return [ $iCalVtimezones, $startDateTime ];
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @param string|null $locale
     */
    private static function extractJsDescription(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal,
        ? string $locale
    ) : void
    {
        if( ! $dto->isDescriptionSet()) {
            return;
        }
        $value  = $dto->getDescription();
        $params = $dto->isDescriptionContentTypeSet()
            ? [ self::setXPrefix( self::DESCRIPTIONCONTENTTYPE ) => $dto->getDescriptionContentType() ]
            : [];
        if( ! empty( $locale )) {
            $params[$iCal::LANGUAGE] = $locale;
        }
        $iCal->setDescription( $value, $params );
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     */
    private static function extractJsExcluded(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if( $dto->isExcludedSet()) {
            $iCal->setXprop(
                self::setXPrefix( self::EXCLUDED ),
                $dto->getExcluded() ? $iCal::TRUE : $iCal::FALSE
            );
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @throws Exception
     */
    private static function extractJsRecurrenceId(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if( $dto->isRecurrenceIdSet()) {
            $value  = $dto->getRecurrenceId();
            if( $dto->isRecurrenceIdTimeZoneSet()) {
                $value = new DateTime( $value, new DateTimeZone( $dto->getRecurrenceIdTimeZone())); // UTC??
            }
            $iCal->setRecurrenceid( $value );
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     */
    private static function extractJsRequestStatus(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if( $dto->isRequestStatusSet()) {
            $requeststatus = explode( self::$SQ, $dto->getRequestStatus(), 3 );
            $iCal->setRequeststatus( $requeststatus[0], $requeststatus[1], $requeststatus[2] ?: null );
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @return array
     * @throws Exception
     */
    private static function extractJsStartTzid(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : array
    {
        $startDateTime = null;
        $tzid          = $dto->getTimeZone();
        if( $dto->isStartSet()) {
            $params = [];
            if( $dto->isShowWithoutTimeSet() && $dto->getShowWithoutTime()) {
                $params[self::setXPrefix( self::SHOWWITHOUTTIME )] = $iCal::TRUE;
                $params[$iCal::VALUE] = $iCal::DATE;
            } // end if
            if( ! empty( $tzid )) {
                $params[$iCal::VALUE] = $iCal::DATE_TIME; // for clarity
                $params[$iCal::TZID]  = $tzid;
            }
            $iCal->setDtstart( $dto->getStart(), $params );
            $startDateTime = clone $iCal->getDtstart();
        }
        return [ $startDateTime, $tzid ];
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @param string|null $tzid
     * @throws Exception
     */
    private static function extractJsExcludedRecurrence(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal,
        ? string $tzid
    ) : void
    {
        if( empty( $dto->getExcludedRecurrenceRulesCount())) {
            return;
        }
        // array of RecurrenceRule[] iCal accept ONE only
        foreach( array_slice( $dto->getExcludedRecurrenceRules(), 0, 1 ) as $excludedRecurrenceRule ) {
            $iCal->setExrule( RecurrenceRule::processToIcalRecur( $excludedRecurrenceRule, $tzid ));
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     */
    private static function extractJsLocalizations(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if( empty( $dto->getLocalizationsCount())) {
            return;
        }
        $localizationsCnt = 0;
        $localizationsKey = self::setXPrefix( self::LOCALIZATIONS ) . self::$D;
        // array of "String[PatchObject]"
        foreach( $dto->getLocalizations() as $languageTag => $patchObject ) {
            $iCal->setXprop(
                $localizationsKey . ++$localizationsCnt,
                $languageTag,
                PatchObject::processToIcalXparams( $patchObject )
            );
        } // end foreach
    }

    /**
     * Return array, lid => Vloctions
     *
     * @param EventDto|TaskDto $dto
     * @param string|null $locale
     * @return array
     * @throws Exception
     */
    private static function extractJsLocations(
        EventDto|TaskDto $dto,
        ? string $locale
    ) : array
    {
        if( empty( $dto->getLocationsCount())) {
            return [];
        }
        $vLocations = [];
        foreach( $dto->getLocations() as $lid => $location ) {
            $vLocations[$lid] = Location::processToIcalVlocation( $lid,  $location, $locale );
        }
        return $vLocations;
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @param array $vLocations   lid => Vloctions
     * @return array   Vloctions (lid) used by participants
     * @throws Exception
     */
    private static function extractJsParticipants(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal,
        array & $vLocations
    ) : array
    {
        if( empty( $dto->getParticipantsCount())) {
            return [];
        }
        [ $particpants, $idEmailArr] = self::getDtoParticipantsIdEmails( $dto );
        $pVlocationLids = [];
        foreach( $particpants as $pid => $participant ) {
            $participantVlocation =
                Participant::getVlocationUsingLocationId( $participant, $vLocations, $pVlocationLids );
            [ $attendeeValue, $attendeeParams ] = Participant::processToIcal(
                $participant,
                $iCal->newParticipant()->setUid((string) $pid ),
                $idEmailArr,
                $participantVlocation
            );
            if( ! empty( $attendeeValue )) { // skip no-email participant
                $iCal->setAttendee( $attendeeValue, $attendeeParams );
            }
        } // end foreach
        return $pVlocationLids;
    }

    /**
     * Return array, particpants and all id+email pars
     *
     * @param EventDto|TaskDto $dto
     * @return array[]
     */
    private static function getDtoParticipantsIdEmails( EventDto|TaskDto $dto ) : array
    {
        $particpants = [];
        $idEmailArr  = []; // used to map participant id to email, [ id => email ]
        foreach( $dto->getParticipants() as $pid => $participant ) {
            $particpants[$pid]    = $participant;
            if( $participant->isEmailSet()) {
                $idEmailArr[$pid] = $participant->getEmail();
            }
        } // end foreach
        return [ $particpants, $idEmailArr ];
    }

    /**
     * Take first Vlocation WITH name and set as iCal::location, skip the rest
     *
     * @param IcalVevent|IcalVtodo $iCal
     * @param IcalVlocation[] $vLocations
     * @param string[]        $pVlocationLids
     * @param string|null     $locale
     */
    private static function setOneIcalLocation(
        IcalVevent|IcalVtodo $iCal,
        array $vLocations,
        array $pVlocationLids,
        ? string $locale
    ) : void
    {
        if( empty( $vLocations )) {
            return;
        }
        $vLocationsLids = array_keys( $vLocations );
        foreach( $vLocationsLids as $lid ) { // try to use the first found Vlocation NOT in participants
            if( ! in_array( $lid, $pVlocationLids, true ) &&
                Location::setIcalLocationFromIcalVlocation( $lid, $vLocations[$lid], $iCal, $locale )) {
                return;
            }
        } // end foreach
        foreach( $vLocationsLids as $lid ) { // otherwise pick the first one
            if( Location::setIcalLocationFromIcalVlocation( $lid, $vLocations[$lid], $iCal, $locale )) {
                break;
            }
        } // end foreach
    }

    /**
     * Set all but participant Vlocations as Vevent/Vtodo Vlocations
     *
     * @param IcalVevent|IcalVtodo $iCal
     * @param array $vLocations
     * @param array $pVlocationLids
     */
    private static function setIcalVlocations(
        IcalVevent|IcalVtodo $iCal,
        array $vLocations,
        array $pVlocationLids
    ) : void
    {
        foreach( $vLocations as $lid => $vlocation ) {
            if( ! in_array( $lid, $pVlocationLids, true )) {
                $iCal->setComponent( $vlocation );
            }
        } // end foreach
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @param string|null $tzid
     * @throws Exception
     */
    private static function extractJsRecurrenceRules(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal,
        ? string $tzid
    ) : void
    {
        if( empty( $dto->getRecurrenceRulesCount())) {
            return;
        }
        // array of "RecurrenceRule[]" - iCal accepts ONE only
        foreach( array_slice( $dto->getRecurrenceRules(), 0, 1 ) as $recurrenceRule ) {
            $iCal->setRrule( RecurrenceRule::processToIcalRecur( $recurrenceRule, $tzid ));
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @throws Exception
     */
    private static function extractJsRecurrenceOverrides(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if(  empty( $dto->getRecurrenceOverridesCount())) {
            return;
        }
        // array of "LocalDateTime[PatchObject]"
        foreach( $dto->getRecurrenceOverrides() as $localDateTime => $patchObject) {
            $iCal->setRdate( $localDateTime, PatchObject::processToIcalXparams( $patchObject ));
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     */
    private static function extractJsRelatedTo(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        // array of "String[Relation]"
        if( empty( $dto->getRelatedToCount())) {
            return;
        }
        foreach( $dto->getRelatedTo() as $uid => $relation ) {
            $iCal->setRelatedto( $uid, Relation::processToIcalXparams( $relation ));
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     */
    private static function extractJsReplyTo(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if( empty( $dto->getReplyToCount())) {
            return;
        }
        $replyToKey = self::setXPrefix( self::REPLYTO . self::$D );
        // array of "String[String]"  for imip method ical organizer, others x-prop
        foreach( $dto->getReplyTo() as $replyToMethod => $replyTo ) {
            if( $dto::IMIP === $replyToMethod ) {
                $iCal->setOrganizer( $replyTo );
            }
            else {
                $iCal->setXprop( $replyToKey . strtoupper( $replyToMethod ), $replyTo );
            }
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @throws Exception
     */
    private static function extractJsVirtualLocations(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if( empty( $dto->getVirtualLocationsCount())) {
            return;
        }
        // array of "Id[VirtualLocation]"
        foreach( $dto->getVirtualLocations() as $id => $virtualLocation ) {
            $iCal->setComponent( VirtualLocation::processToIcal( $id, $virtualLocation ));
        }
    }

    /**
     * @param EventDto|TaskDto $dto
     * @return array
     * @throws Exception
     */
    private static function extractJsTimeZones( EventDto|TaskDto $dto ) : array
    {
        $iCalVtimezones = [];
        // array of "TimeZoneId[TimeZone]"
        if( ! empty( $dto->getTimeZonesCount())) {
            foreach( $dto->getTimeZones() as $timeZoneId => $timeZone ) {
                $iCalVtimezones[$timeZoneId] = TimeZone::processToIcal( $timeZoneId, $timeZone );
            }
        }
        return $iCalVtimezones;
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param IcalVevent|IcalVtodo $iCal
     * @throws Exception
     */
    private static function extractJsAlerts(
        EventDto|TaskDto $dto,
        IcalVevent|IcalVtodo $iCal
    ) : void
    {
        if( $dto->isUseDefaultAlertsSet()) {
            $iCal->setXprop( self::setXPrefix(
                self::USEDEFAULTALERTS ),
                $dto->getUseDefaultAlerts() ? $iCal::TRUE : $iCal::FALSE
            );
        }
        if( empty( $dto->getAlertsCount())) {
            return;
        }
        foreach( $dto->getAlerts() as $id => $alert ) {
            Alert::processToIcal( $id, $alert, $iCal->newValarm());
        }
    }

    /**
     * Ical Vevent|Vtodo properties to Event|Task properties
     *
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @param IcalVtimezone[] $iCalVtimezones
     * @return null|DateTime  startDateTime
     * @throws Exception
     */
    protected static function eventTaskProcessFromIcal(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto,
        array $iCalVtimezones
    ) : ? DateTime
    {
        self::extractIcalXmethod( $iCalComponent, $dto, );
        self::extractIcalDescription( $iCalComponent, $dto );
        if( $iCalComponent->isPrioritySet()) {
            $dto->setPriority( $iCalComponent->getPriority() );
        }
        if( $iCalComponent->IsClassSet()) {
            $dto->setPrivacy( $iCalComponent->getClass());
        }
        self::extractIcalRecurrenceid( $iCalComponent, $dto );
        if( $iCalComponent->isRequestStatusSet()) {
            $dto->setRequeststatus( implode( self::$SQ, $iCalComponent->getRequestStatus()));
        }
        self::extractIcalSentBy( $iCalComponent, $dto );
        if( $iCalComponent->isSequenceSet()) {
            $dto->setSequence( $iCalComponent->getSequence());
        }
        [ $startDateTime, $tzid ] = self::extractIcalDtstartTzid( $iCalComponent, $dto );
        self::extractIcalExcluded( $iCalComponent, $dto );
        self::extractIcalExrule( $iCalComponent, $dto, $tzid );
        // Vlocations, Attendees && Participants
        $virtualLocationKey = self::setXPrefix( self::VIRTUALLOCATION ); // also used below
        $dtoLocations = self::extractIcalVlocationsWithoutKey( $iCalComponent, $virtualLocationKey ); // 1. iCal components
        // $attendees (email[params]), $idEmailArr (id[email])
        [ $attendees, $idEmailArr ] = self::extractIcalAttendees( $iCalComponent );
        self::extractIcalParticipants( $iCalComponent, $dto, $attendees, $idEmailArr, $dtoLocations );
        // any Attendee(s) NOT found as Participant on calAdress
        self::processAttendeesNotInParticipants( $dto, $attendees, $idEmailArr );
        /*
         * Check locations
         *
         * 1: Participant(s) vlocations (from Participants above)
         * 2: Component Vlocations (properties) locations, NOT VirtualLocations !
         * 3: iCalComponent::getLocation()
//       * 4: iCalComponent::getXprop( X-LOCATION skipped
         */
        $iCalPropLocations = []; // iCal location properties value array
        if( $iCalComponent->isLocationSet()) {  // 2. ical property location (ony one)
            $iCalPropLocations[] = $iCalComponent->getLocation( null, true );
        }
        // 3: ical::getXprop( X-LOCATION   // skip.. .
        self::processIcalPropLocations( $iCalPropLocations, $dtoLocations );
        self::addDtoLocations( $dto, $dtoLocations );
        if( $iCalComponent->isRruleSet()) {
            $dto->addRecurrenceRule( RecurrenceRule::processFromIcalRecur( $iCalComponent->getRrule(), $tzid ));
        }
        self::extractIcalRdates( $iCalComponent, $dto );
        self::extractIcalRelatedtos( $iCalComponent, $dto );
        // check for iCal Vlocations with VirtualLocationId
        self::extractIcalVlocationsWithKey( $iCalComponent, $dto, $virtualLocationKey );
        self::extractIcalFreeBusyStatus( $iCalComponent, $dto );
        $imipFound = self::extractIcalOrganizer( $iCalComponent, $dto );
        // Localizations and replyTo as iCal xProps
        self::extractIcalLocalizationsReplyToXprops( $iCalComponent, $dto, $imipFound );
        self::processIcalVtimezones( $dto, $iCalVtimezones );
        self::extractIcalAlarms( $iCalComponent, $dto );
        return $startDateTime ?: null;
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     */
    private static function extractIcalXmethod(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto,
    ) : void
    {
        $methodKey = self::setXPrefix( self::METHOD );
        if( $iCalComponent->isXpropSet( $methodKey )) {
            $dto->setMethod( $iCalComponent->getXprop( $methodKey )[1] );
        }
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     */
    private static function extractIcalDescription(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto,
    ) : void
    {
        if( ! $iCalComponent->isDescriptionSet()) {
            return;
        }
        $contents = $iCalComponent->getDescription( true );
        $dto->setDescription( $contents->getValue());
        if( $contents->hasParamKey( $iCalComponent::LANGUAGE )) {
            $dto->setLocale( $contents->getParams( $iCalComponent::LANGUAGE ));
        }
        $descrCtKey = self::setXPrefix(self::DESCRIPTIONCONTENTTYPE );
        if( $contents->hasParamKey( $descrCtKey )) {
            $dto->setDescriptionContentType( $contents->getParams( $descrCtKey ));
        }
        elseif( $iCalComponent->isXpropSet( $descrCtKey )) {
            $dto->setDescriptionContentType( $iCalComponent->getXprop( $descrCtKey )[1] );
        }
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractIcalRecurrenceid(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : void
    {
        if( $iCalComponent->isRecurrenceidSet()) {
            $contents = $iCalComponent->getRecurrenceid();
            $dto->setRecurrenceId( $contents );
            $dto->setRecurrenceIdTimeZone( $contents->getTimezone()->getName());
        }
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractIcalSentBy(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : void
    {
        $key = self::setXPrefix( self::SENTBY );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setSentBy( $iCalComponent->getXprop( $key )[1] );
        }
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @return array
     * @throws Exception
     */
    private static function extractIcalDtstartTzid(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : array
    {
        $startDateTime = $tzid = null;
        if( ! $iCalComponent->isDtstartSet()) {
            return [ $startDateTime, $tzid ];
        }
        $startDateTime = $iCalComponent->getDtstart( true );
        $dto->setStart( $startDateTime->getValue());
        if( $startDateTime->hasXparamKey(self::SHOWWITHOUTTIME, $iCalComponent::TRUE )) {
            $dto->setShowWithoutTime( true );
        }
        if( $startDateTime->hasParamKey( $iCalComponent::TZID )) {
            $tzid = $startDateTime->getParams( $iCalComponent::TZID );
            $dto->setTimeZone( $tzid );
        }
        $startDateTime = clone $startDateTime->getValue();
        return [ $startDateTime, $tzid ];
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     */
    private static function extractIcalExcluded(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : void
    {
        $key = self::setXPrefix( self::EXCLUDED );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setExcluded( $iCalComponent::TRUE === $iCalComponent->getXprop( $key )[1] );
        }
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @param string|null $tzid
     * @throws Exception
     */
    private static function extractIcalExrule(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto,
        ? string $tzid
    ) : void
    {
        if( $iCalComponent->isExruleSet()) {
            $dto->addExcludedRecurrenceRule(
                RecurrenceRule::processFromIcalRecur( $iCalComponent->getExrule(),
                    $tzid )
            );
        }
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param string $virtualLocationKey
     * @return array
     * @throws Exception
     */
    private static function extractIcalVlocationsWithoutKey(
        IcalVevent|IcalVtodo $iCalComponent,
        string $virtualLocationKey
    ) : array
    {
        $dtoLocations = []; // lid[location]   may also (later) contain Vlocation(s) from Participants
        foreach( $iCalComponent->getComponents( $iCalComponent::VLOCATION ) as $vlocation ) { // 1. iCal components
            if( $vlocation->isXpropSet( $virtualLocationKey )) {
                continue; // VirtualLocation found, skip here
            }
            [ $lid, $dtoLocation ] = Location::processFromIcalVlocation( $vlocation );
            if( ! isset( $dtoLocations[$lid] )) { // add location if not found
                $dtoLocations[$lid] = $dtoLocation;
            }
        } // end foreach
        return $dtoLocations;
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @return array[]
     * @throws Exception
     */
    private static function extractIcalAttendees( IcalVevent|IcalVtodo $iCalComponent ) : array
    {
        $attendees   = []; // email[params]
        $idEmailArr  = []; // id[email]
        foreach( $iCalComponent->getAllAttendee( true ) as $attendee ) {
            $calAddr = self::removeMailtoPrefix( $attendee->getValue());
            $attendeeParams = $attendee->getParams();
            if( isset( $attendeeParams[$iCalComponent::X_PARTICIPANTID] )) {
                $id  = $attendeeParams[$iCalComponent::X_PARTICIPANTID];
            }
            else {
                $id  = EventDto::getNewUid(); // also for TaskDto
                $attendeeParams[$iCalComponent::X_PARTICIPANTID] = $id;
            }
            $attendees[$calAddr] = $attendee->getParams();
            $idEmailArr[$id]     = $calAddr;
        } // end foreach
        return [ $attendees, $idEmailArr ];
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @param array $attendees
     * @param array $idEmailArr
     * @param array $dtoLocations
     * @throws Exception
     */
    private static function extractIcalParticipants(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto,
        array & $attendees,
        array & $idEmailArr,
        array & $dtoLocations
    ) : void
    {
        foreach( $iCalComponent->getComponents( IcalVevent::PARTICIPANT ) as $icalParticipant ) {
            $attendeeParams = [];
            self::processIcalParticipant( $icalParticipant,$attendees, $attendeeParams,$idEmailArr );
            [ $id, $participant, $vLocations ] =
                Participant::processFromIcal( $icalParticipant, $attendeeParams, $idEmailArr );
            foreach( $vLocations as $vlocation ) {
                [ $lid, $dtoLocation ] = Location::processFromIcalVlocation( $vlocation );
                if( ! isset( $dtoLocations[$lid] )) { // add location if not found
                    $dtoLocations[$lid] = $dtoLocation;
                }
            } // end if
            $dto->addParticipant( $id, $participant );
        } // end foreach  $participants
    }

    /**
     * Process any Attendee(s) NOT found as Participant on calAdress
     *
     * @param EventDto|TaskDto $dto
     * @param array $attendees
     * @param array $idEmailArr
     * @throws Exception
     */
    private static function processAttendeesNotInParticipants(
        EventDto|TaskDto $dto,
        array $attendees,
        array $idEmailArr
    ) : void
    {
        foreach( $attendees as $calAddr => $attendeeParams ) {
            $pid            = ParticipantDto::getNewUid();
            $participantDto = ParticipantDto::factory( $calAddr );
            Participant::processFromIcalArray( $attendeeParams, $participantDto, $idEmailArr );
            $dto->addParticipant( $pid, $participantDto );
        }
    }

    /**
     * @param array $iCalPropLocations
     * @param array $dtoLocations  ( lid => LocationDto )
     * @throws Exception
     */
    private static function processIcalPropLocations(
        array $iCalPropLocations,
        array & $dtoLocations
    ) : void
    {
        $locationNames = [];
        foreach( $dtoLocations as $dtoLocation ) {
            $locationNames[] = $dtoLocation->getName();
        }
        foreach( $iCalPropLocations as $iCalPropLocation ) {
            if( $iCalPropLocation->hasParamKey( IcalVevent::X_VLOCATIONID )) {
                $locationId = $iCalPropLocation->getParams( IcalVevent::X_VLOCATIONID );
                if( ! isset( $dtoLocations[$locationId] )) {
                    // add location if  'Location id'  not found
                    [ $lid, $dtoLocation ]  = Location::fromIcalLocation( $iCalPropLocation );
                    if( ! isset( $dtoLocations[$lid] ) ) {
                        $dtoLocations[$lid] = $dtoLocation;
                        $locationNames[]    = $dtoLocation->getName();
                    }
                    continue;
                }
            } // end if
            // compare location name, skip if found
            if( ! in_array( $iCalPropLocation->getValue(), $locationNames, true )) {
                // not found in locationNames
                [ $lid, $dtoLocation ]  = Location::fromIcalLocation( $iCalPropLocation );
                if( ! isset( $dtoLocations[$lid] )) {
                    $dtoLocations[$lid] = $dtoLocation;
                    $locationNames[]    = $dtoLocation->getName();
                }
            }
        } // end foreach $iCalPropLocations
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param array $dtoLocations   ( lid => LocationDto )
     */
    private static function addDtoLocations(
        EventDto|TaskDto $dto,
        array $dtoLocations
    ) : void
    {
        ksort( $dtoLocations ); // sort on Vlocation uid
        foreach( $dtoLocations as $lid => $dtoLocation ) {
            $dto->addLocation( $lid, $dtoLocation );
        }
    }


        /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractIcalRdates(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : void
    {
        foreach( $iCalComponent->getAllRdate( true ) as $rDate ) {
            if( ! $rDate->hasParamKey( $iCalComponent::VALUE, $iCalComponent::PERIOD )) {
                $poIcal = PatchObject::singleton();
                foreach( $rDate->getValue() as $date ) {
                    $dto->addRecurrenceOverride(
                        $date->format( $dto::$LocalDateTimeFMT ),
                        $poIcal->processFromIcalXparams( $rDate->getParams())
                    );
                }
            }
        } // end foreach
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     */
    private static function extractIcalRelatedtos(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : void
    {
        foreach( $iCalComponent->getAllRelatedto( true ) as $relatedto ) {
            [ $id, $relation ] = Relation::processFromIcalRelatedTo( $relatedto );
            $dto->addRelatedTo( $id, $relation );
        }
    }

    /**
     * Check for iCal Vlocations with VirtualLocationId
     *
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @param string $virtualLocationKey
     * @throws Exception
     */
    private static function extractIcalVlocationsWithKey(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto,
        string $virtualLocationKey
    ) : void
    {
        // check for iCal Vlocations with VirtualLocationId
        foreach( $iCalComponent->getComponents( $iCalComponent::VLOCATION ) as $vlocation ) {
            if( ! $vlocation->isXpropSet( $virtualLocationKey )) {
                continue;
            }
            [ $id, $virtualLocation ] = VirtualLocation::processFromIcal( $vlocation );
            $dto->addVirtualLocation( $id, $virtualLocation );
        } // end foreach
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     */
    private static function extractIcalFreeBusyStatus(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : void
    {
        $key = self::setXPrefix( self::FREEBUSYSTATUS );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setFreeBusyStatus( $iCalComponent->getXprop( $key)[1] );
        }
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @return bool
     */
    private static function extractIcalOrganizer(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : bool
    {
        $imipFound = false;
        if( $iCalComponent->isOrganizerSet()) {
            $dto->addReplyTo( $dto::IMIP, $iCalComponent->getOrganizer());
            $imipFound = true;
        }
        return $imipFound;
    }

    /**
     * Extract Localizations and replyTo from iCal xProps
     *
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @param bool $imipFound
     * @throws Exception
     */
    private static function extractIcalLocalizationsReplyToXprops(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto,
        bool $imipFound
    ) : void
    {
        // Localizations and replyTo from iCal xProps
        $localizationsKey = self::setXPrefix( self::LOCALIZATIONS ) . self::$D;
        $replyToKey       = self::setXPrefix( self::REPLYTO . self::$D );
        $poIcal           = PatchObject::singleton();
        foreach( $iCalComponent->getAllXprop( true ) as $xProp ) {
            if( str_starts_with( $xProp[0], $localizationsKey )) {
                $dto->addLocalization(
                    $xProp[1]->getValue(),
                    $poIcal->processFromIcalXparams( $xProp[1]->getParams())
                );
            }
            elseif( str_starts_with( $xProp[0], $replyToKey )) { // X-REPLYTO-...
                $replyToMethod = strtolower( explode( self::$D, $xProp[0], 3 )[2] );
                if( ! $imipFound || ( $dto::IMIP !== $replyToMethod )) {
                    $dto->addReplyTo( $replyToMethod, $xProp[1]->getValue());
                }
            }
        } // end foreach
    }

    /**
     * @param EventDto|TaskDto $dto
     * @param array $iCalVtimezones
     * @throws Exception
     */
    private static function processIcalVtimezones(
        EventDto|TaskDto $dto,
        array $iCalVtimezones
    ) : void
    {
        $dtoTimezones = $dto->getLocationsTimezones(); // dto-timezones array from locations and dto
        if( $dto->isTimeZoneSet()) {
            $tzid     = $dto->getTimeZone();
            if( ! in_array( $tzid, $dtoTimezones, true )) {
                $dtoTimezones[] = $tzid;
            }
        } // end if
        foreach( $iCalVtimezones as $timeZoneId => $vtimezone ) {
            if( in_array( $timeZoneId, $dtoTimezones, true )) { // accept only dto-timezones
                $dto->addTimeZone(
                    (string) $timeZoneId,
                    TimeZone::processFromIcal( $timeZoneId, $vtimezone )
                );
            }
        } // end foreach
    }

    /**
     * @param IcalVevent|IcalVtodo $iCalComponent
     * @param EventDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractIcalAlarms(
        IcalVevent|IcalVtodo $iCalComponent,
        EventDto|TaskDto $dto
    ) : void
    {
        $key = self::setXPrefix( self::USEDEFAULTALERTS );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setUseDefaultAlerts(( $iCalComponent::TRUE === $iCalComponent->getXprop( $key )[1] ));
        }
        foreach( $iCalComponent->getComponents( $iCalComponent::VALARM ) as  $alarm ) {
            [ $uid, $alert ] = Alert::processFromIcal( $alarm );
            $dto->addAlert( $uid, $alert );
        }
    }

    /**
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendees       [ *(calAddr => attendeeParams) ];
     * @param array $attendeeParams  [ *(key => value) ]
     * @param array $idEmailArr      [ *(id => calAddr) ]
     * @throws Exception
     */
    protected static function processIcalParticipant(
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendees,
        array & $attendeeParams,
        array & $idEmailArr
    ) : void
    {
        if( ! $icalParticipant->isCalendaraddressSet()) {
            return;
        }
        $email = self::removeMailtoPrefix( $icalParticipant->getCalendaraddress());
        if( self::emailKeyInEmailArr( $email, $attendees )) {
            $attendeeParams = $attendees[$email];
            unset( $attendees[$email] ); // found attendee, removed
        }
        if( Participant::isEmailFoundInIdEmailArr( $email, $idEmailArr )) {
            // if email found in idEmailArr, unset
            $prevKey = array_keys( $idEmailArr, $email, true )[0];
            unset( $idEmailArr[$prevKey] );
        }
        $idEmailArr[$icalParticipant->getUid()] = $email;
    }

    /**
     * Return bool true if array[key], any case
     *
     * @param string $email
     * @param array $emailArray
     * @return bool
     */
    protected static function emailKeyInEmailArr( string $email, array $emailArray ) : bool
    {
        foreach( $emailArray as $calAddr => $attendeeParams ) {
            if( strtolower( $calAddr ) === strtolower( $email )) {
                return true;
            }
        }
        return false;
    }
}
