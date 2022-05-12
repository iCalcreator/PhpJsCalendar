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
use Kigkonsult\Icalcreator\Util\CalAddressFactory;
use Kigkonsult\Icalcreator\Vevent;
use Kigkonsult\Icalcreator\Vtimezone;
use Kigkonsult\Icalcreator\Vtodo;
use Kigkonsult\PhpJsCalendar\Dto\Event       as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Participant as ParticipantDto;
use Kigkonsult\PhpJsCalendar\Dto\Task        as TaskDto;

abstract class BaseEventTask extends BaseGroupEventTask
{
    /**
     * Ical Event|Task common properties to Vevent/Vtodo properties
     *
     * @param EventDto|TaskDto $dto
     * @param Vevent|Vtodo $iCal
     * @return mixed[]   [ id[Vtimezone], startDateTime ]
     * @throws Exception
     */
    protected static function eventTaskProcessTo( EventDto|TaskDto $dto, Vevent|Vtodo $iCal ) : array
    {
        if( $dto->isMethodSet()) {
            $iCal->setXprop( self::setXPrefix( self::METHOD ), $dto->getMethod());
        }

        $locale = $dto->getLocale();
        if( $dto->isDescriptionSet()) {
            $value  = $dto->getDescription();
            $params = $dto->isDescriptionContentTypeSet()
                ? [ self::setXPrefix( self::DESCRIPTIONCONTENTTYPE ) => $dto->getDescriptionContentType() ]
                : [];
            if( ! empty( $locale )) {
                $params[$iCal::LANGUAGE] = $locale;
            }
            $iCal->setDescription( $value, $params );
        }

        if( $dto->isExcludedSet()) {
            $iCal->setXprop(
                self::setXPrefix( self::EXCLUDED ),
                $dto->getExcluded() ? $iCal::TRUE : $iCal::FALSE
            );
        }

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

        if( $dto->isRecurrenceIdSet()) {
            $value  = $dto->getRecurrenceId();
            if( $dto->isRecurrenceIdTimeZoneSet()) {
                $value = new DateTime( $value, new DateTimeZone( $dto->getRecurrenceIdTimeZone())); // UTC??
            }
            $iCal->setRecurrenceid( $value );
        }

        if( $dto->isRequestStatusSet()) {
            $requeststatus = explode( self::$SQ, $dto->getRequestStatus(), 3 );
            $iCal->setRequeststatus( $requeststatus[0], $requeststatus[1], $requeststatus[2] ?: null );
        }

        if( $dto->isSentBySet()) { // ?? organizer
            $iCal->setXprop( self::setXPrefix( self::SENTBY ), $dto->getSentBy());
        }

        if( $dto->isSequenceSet()) {
            $iCal->setSequence( $dto->getSequence());
        }

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

        // array of RecurrenceRule[] iCal accept ONE only
        if( ! empty( $dto->getExcludedRecurrenceRulesCount())) {
            foreach( array_slice( $dto->getExcludedRecurrenceRules(), 0, 1 ) as $excludedRecurrenceRule ) {
                $iCal->setExrule( RecurrenceRule::processTo( $excludedRecurrenceRule, $tzid ));
            }
        }

        // array of "String[PatchObject]"
        if( ! empty( $dto->getLocalizationsCount())) {
            $localizationsCnt = 0;
            $localizationsKey = self::setXPrefix( self::LOCALIZATIONS ) . self::$D;
            foreach( $dto->getLocalizations() as $languageTag => $patchObject ) {
                $iCal->setXprop(
                    $localizationsKey . ++$localizationsCnt,
                    $languageTag,
                    PatchObject::processTo( $patchObject )
                );
            }
        }

        // array of "Id[Location]"
        $vLocations = [];  // lid[Vloctions]
        if( ! empty( $dto->getLocationsCount())) {
            $locationCnt = 0;
            foreach( $dto->getLocations() as $lid => $location ) {
                $vLocations[$lid] = Location::processTo( $lid,  $location, $locale );
                if( 0 === $locationCnt ) {
                    [ $locationValue, $locationParams ] = Location::processToLocationArr( $lid,  $location, $locale );
                    $iCal->setLocation( $locationValue, $locationParams ); // only one property accepted
                }
                ++$locationCnt;
            } // end foreach
        } // end locations

        // array of "Id[Participant]"
        if( ! empty( $dto->getParticipantsCount())) {
            $particpants = [];
            $idEmailArr  = []; // used to map participant id to email
            foreach( $dto->getParticipants() as $pid => $participant ) {
                $particpants[$pid]    = $participant;
                if( $participant->isEmailSet()) {
                    $idEmailArr[$pid] = $participant->getEmail();
                }
            }
            foreach( $particpants as $pid => $participant ) {
                $participantVlocation = null;
                if( $participant->isLocationIdSet()) {
                    $lid = $participant->getLocationId();
                    if( isset( $vLocations[$lid] ) ) {
                        $participantVlocation = $vLocations[$lid];
//                      unset( $vLocations[$lid] );
                    }
                }
                [ $attendeeValue, $attendeeParams ] =
                    Participant::processTo(
                        $pid,  $participant, $iCal->newParticipant(), $idEmailArr, $participantVlocation
                    );
                if( ! empty($attendeeValue )) {
                    $iCal->setAttendee( $attendeeValue, $attendeeParams );
                }
            } // end foreach
        } // end participants
        foreach( $vLocations as $vlocation ) {
            $iCal->setComponent( $vlocation ); // all but participant Vlocation as Vevent/Vtodo Vlocations
        } // end foreach

        // array of "RecurrenceRule[]" - iCal accepts ONE only
        if( ! empty( $dto->getRecurrenceRulesCount())) {
            foreach( array_slice( $dto->getRecurrenceRules(), 0, 1 ) as $recurrenceRule ) {
                $iCal->setRrule( RecurrenceRule::processTo( $recurrenceRule, $tzid ));
            }
        }
        // PatchObject ??
        // array of "LocalDateTime[PatchObject]"
        if( ! empty( $dto->getRecurrenceOverridesCount())) {
            foreach( $dto->getRecurrenceOverrides() as $localDateTime => $patchObject) {
                $iCal->setRdate( $localDateTime, PatchObject::processTo( $patchObject ));
            }
        }
        // array of "String[Relation]"
        if( ! empty( $dto->getRelatedToCount())) {
            foreach( $dto->getRelatedTo() as $uid => $relation ) {
                $iCal->setRelatedto( $uid, Relation::processTo( $relation ));
            }
        }
        // array of "String[String]"  for imip method ical organizer, others x-prop
        if( ! empty( $dto->getReplyToCount())) {
            $replyToKey = self::setXPrefix( self::REPLYTO . self::$D );
            foreach( $dto->getReplyTo() as $replyToMethod => $replyTo ) {
                if( $dto::IMIP === $replyToMethod ) {
                    $iCal->setOrganizer( $replyTo );
                }
                else {
                    $iCal->setXprop( $replyToKey . strtoupper( $replyToMethod ), $replyTo );
                }
            }
        }

        // array of "Id[VirtualLocation]"
        if( ! empty( $dto->getVirtualLocationsCount())) {
            foreach( $dto->getVirtualLocations() as $id => $virtualLocation ) {
                $iCal->setComponent( VirtualLocation::processTo( $id, $virtualLocation ));
            }
        }

        $vtimezones = [];
        // array of "TimeZoneId[TimeZone]"
        if( ! empty( $dto->getTimeZonesCount())) {
            foreach( $dto->getTimeZones() as $timeZoneId => $timeZone ) {
                $vtimezones[$timeZoneId] = TimeZone::processTo( $timeZoneId, $timeZone );
            }
        }

        if( $dto->isUseDefaultAlertsSet()) {
            $iCal->setXprop( self::setXPrefix(
                self::USEDEFAULTALERTS ),
                $dto->getUseDefaultAlerts() ? $iCal::TRUE : $iCal::FALSE
            );
        }
        if( ! empty( $dto->getAlertsCount())) {
            foreach( $dto->getAlerts() as $id => $alert ) {
                Alert::processTo( $id, $alert, $iCal->newValarm());
            }
        }

        return [ $vtimezones, $startDateTime ];
    }

    /**
     * Ical Vevent|Vtodo common properties to Event|Task properties
     *
     * @param Vevent|Vtodo $iCal
     * @param EventDto|TaskDto $dto
     * @param Vtimezone[] $vtimezones
     * @return null|DateTime
     * @throws Exception
     */
    protected static function eventTaskProcessFrom(
        Vevent|Vtodo $iCal,
        EventDto|TaskDto $dto,
        array $vtimezones
    ) : ? DateTime
    {
        $methodKey = self::setXPrefix( self::METHOD );
        if( $iCal->isXpropSet( $methodKey )) {
            $dto->setMethod( $iCal->getXprop( $methodKey )[1] );
        }

        if( $iCal->isDescriptionSet()) {
            $contents = $iCal->getDescription( true );
            $dto->setDescription( $contents->value );
            if( $contents->hasParamKey( $iCal::LANGUAGE )) {
                $dto->setLocale( $contents->getParams( $iCal::LANGUAGE ));
            }
            $descrCtKey = self::setXPrefix(self::DESCRIPTIONCONTENTTYPE );
            if( $contents->hasParamKey( $descrCtKey )) {
                $dto->setDescriptionContentType( $contents->getParams( $descrCtKey ));
            }
            elseif( $iCal->isXpropSet( $descrCtKey )) {
                $dto->setDescriptionContentType( $iCal->getXprop( $descrCtKey )[1] );
            }
        }

        if( $iCal->isPrioritySet()) {
            $dto->setPriority( $iCal->getPriority() );
        }

        if( $iCal->IsClassSet()) {
            $dto->setPrivacy( $iCal->getClass());
        }

        if( $iCal->isRecurrenceidSet()) {
            $contents = $iCal->getRecurrenceid();
            $dto->setRecurrenceId( $contents );
            $dto->setRecurrenceIdTimeZone( $contents->getTimezone()->getName());
        }

        if( $iCal->isRequestStatusSet()) {
            $dto->setRequeststatus( implode( self::$SQ, $iCal->getRequestStatus()));
        }

        $key = self::setXPrefix( self::SENTBY );
        if( $iCal->isXpropSet( $key )) {
            $dto->setSentBy( $iCal->getXprop( $key)[1] );
        }

        if( $iCal->isSequenceSet()) {
            $dto->setSequence( $iCal->getSequence());
        }

        $tzid = $startDateTime = null;
        if( $iCal->isDtstartSet()) {
            $startDateTime = $iCal->getDtstart( true );
            $dto->setStart( $startDateTime->value );
            if( $startDateTime->hasXparamKey(self::SHOWWITHOUTTIME, $iCal::TRUE )) {
                $dto->setShowWithoutTime( true );
            }
            if( $startDateTime->hasParamKey( $iCal::TZID )) {
                $tzid = $startDateTime->getParams( $iCal::TZID );
                $dto->setTimeZone( $tzid );
            }
            $startDateTime = clone $startDateTime->value;
        } // end dtstart set

        $key = self::setXPrefix( self::EXCLUDED );
        if( $iCal->isXpropSet( $key )) {
            $dto->setExcluded( $iCal::TRUE === $iCal->getXprop( $key )[1] );
        }

        if( $iCal->isExruleSet()) {
            $dto->addExcludedRecurrenceRule( RecurrenceRule::processFrom( $iCal->getExrule(), $tzid ));
        }

        // Vlocations, Attendees && Participants
        $dtoLocations       = []; // lid[location]   may also contain Vlocation(s) from Participants
        $virtualLocationKey = self::setXPrefix( self::VIRTUALLOCATION ); // also used below
        $iCal->resetCompCounter();
        while( false !== ( $vlocation = $iCal->getComponent( $iCal::VLOCATION ))) { // 1. iCal components
            if( $vlocation->isXpropSet( $virtualLocationKey )) {
                continue; // VirtualLocation found, skip here
            }
            [ $lid, $dtoLocation ] = Location::processFrom( $vlocation );
            if( ! isset( $dtoLocations[$lid] )) { // add location if not found
                $dtoLocations[$lid] = $dtoLocation;
            }
        } // end while

        $attendees   = []; // email[params]
        $idEmailArr  = []; // id[email]
        while( false !== ( $attendee = $iCal->getAttendee( null, true ))) {
            $calAddr = CalAddressFactory::removeMailtoPrefix( $attendee->value );
            $attendeeParams = $attendee->params;
            if( isset( $attendeeParams[$iCal::X_PARTICIPANTID] )) {
                $id  = $attendeeParams[$iCal::X_PARTICIPANTID];
            }
            else {
                $id  = $dto::getNewUid();
                $attendeeParams[$iCal::X_PARTICIPANTID] = $id;
            }
            $attendees[$calAddr] = $attendee->params;
            $idEmailArr[$id]     = $calAddr;
        } // end while
        $iCal->resetCompCounter();
        while( false !== ( $icalParticipant = $iCal->getComponent( $iCal::PARTICIPANT ))) {
            $attendeeParams = [];
            if( $icalParticipant->isCalendaraddressSet()) {
                $email = CalAddressFactory::removeMailtoPrefix( $icalParticipant->getCalendaraddress());
                if( isset( $attendees[$email] )) {
                    $attendeeParams = $attendees[$email];
                    unset( $attendees[$email] ); // found attendee, removed
                }
                if( in_array( $email, $idEmailArr, true )) {
                    // if email found in idEmailArr, unset
                    $prevKey = array_keys( $idEmailArr, $email, true )[0];
                    unset( $idEmailArr[$prevKey] );
                }
                $idEmailArr[$icalParticipant->getUid()] = $email;
            } // end if isCalendaraddressSet
            [ $id, $participant, $vLocations ] =
                Participant::processFrom( $icalParticipant, $attendeeParams, $idEmailArr );
            foreach( $vLocations as $vlocation ) {
                [ $lid, $dtoLocation ] = Location::processFrom( $vlocation );
                if( ! isset( $dtoLocations[$lid] )) { // add location if not found
                    $dtoLocations[$lid] = $dtoLocation;
                }
            } // end if
            $dto->addParticipant( $id, $participant );
        } // end foreach  $participants
        // any Attendee(s) NOT found as Participant on calAdress)
        foreach( $attendees as $calAddr => $attendeeParams ) {
            $pid            = ParticipantDto::getNewUid();
            $participantDto = ParticipantDto::factory( $calAddr );
            Participant::processFromArray( $attendeeParams, $participantDto, $idEmailArr );
            $dto->addParticipant( $pid, $participantDto );
        }

        /*
         * Check locations
         *
         * 1: Participant(s) vlocations (from Participants above)
         * 2: Component Vlocations (properties) locations, NOT VirtualLocations !
         * 3: iCalComponent::getLocation()
//       * 4: iCalComponent::getXprop( X-LOCATION skipped
         */

        $iCalPropLocations = []; // iCal location properties value array
        if( $iCal->isLocationSet()) {
            $iCalPropLocations[] = $iCal->getLocation( null, true );  // 2. ical property location
        } // end if iCal comp location exists
        /*
        $locationKey = self::setXPrefix( self::LOCATION ); // 3: ical::getXprop( X-LOCATION   // skip.. .
        foreach(  $xProps as $xPropName => $contents ) {
            if( str_starts_with( $xPropName, $locationKey )) {
                $locations2[] = $contents;
          }
        } // end foreach
        */
        $locationNames = [];
        foreach( $dtoLocations as $dtoLocation ) {
            $locationNames[] = $dtoLocation->getName();
        }

        foreach( $iCalPropLocations as $iCalPropLocation ) {
            if( $iCalPropLocation->hasParamKey( $iCal::X_VLOCATIONID ) &&
                ! isset( $dtoLocations[$iCalPropLocation->getParams( $iCal::X_VLOCATIONID )] )) {
                // add location if  'Location id'  not found
                [ $lid, $dtoLocation ] = Location::fromIcaLocation( $iCalPropLocation );
                if( ! isset( $dtoLocations[$lid] )) {
                    $dtoLocations[$lid] = $dtoLocation;
                    $locationNames[]    = $dtoLocation->getName();
                }
                continue;
            } // end if
            // compare location name, skip if found
            if( ! in_array( $iCalPropLocation->value, $locationNames, true )) {
                // not found in locationNames
                [ $lid, $dtoLocation ] = Location::fromIcaLocation( $iCalPropLocation );
                if( ! isset( $dtoLocations[$lid] )) {
                    $dtoLocations[$lid] = $dtoLocation;
                    $locationNames[] = $dtoLocation->getName();
                }
            }
        } // end foreach $iCalPropLocations
        ksort( $dtoLocations ); // sort on Vlocation uid
        foreach( $dtoLocations as $lid => $dtoLocation ) {
            $dto->addLocation( $lid, $dtoLocation );
        }

        if( $iCal->isRruleSet()) {
            $dto->addRecurrenceRule( RecurrenceRule::processFrom( $iCal->getRrule(), $tzid ));
        }
        while( false !== ( $contents = $iCal->getRdate( null, true ))) {
            if( ! $contents->hasParamKey( $iCal::VALUE ) || ! $contents->hasParamValue( $iCal::PERIOD )) {
                $poIcal = PatchObject::singleton();
                foreach( $contents->value as $rdate ) {
                    $dto->addRecurrenceOverride(
                        $rdate->format( $dto::$LocalDateTimeFMT ),
                        $poIcal->processFrom( $contents->getParams())
                    );
                }
            }
        } // end while

        while( false !== ( $contents = $iCal->getRelatedto( null, true ))) {
            [ $id, $relation ] = Relation::processFrom( $contents );
            $dto->addRelatedTo( $id, $relation );
        }

        // check for iCal Vlocations with VirtualLocation
        $iCal->resetCompCounter();
        while( false !== ( $vlocation = $iCal->getComponent( $iCal::VLOCATION ))) {
            if( ! $vlocation->isXpropSet( $virtualLocationKey )) {
                continue;
            }
            [ $id, $virtualLocation ] = VirtualLocation::processFrom( $vlocation );
            $dto->addVirtualLocation( $id, $virtualLocation );
        }


        $key = self::setXPrefix( self::FREEBUSYSTATUS );
        if( $iCal->isXpropSet( $key )) {
            $dto->setFreeBusyStatus( $iCal->getXprop( $key)[1] );
        }

        $imipFound = false;
        if( $iCal->isOrganizerSet()) {
            $dto->addReplyTo( $dto::IMIP, $iCal->getOrganizer());
            $imipFound = true;
        }

        // Localizations and replyTo as iCal xProps
        $localizationsKey = self::setXPrefix( self::LOCALIZATIONS ) . self::$D;
        $replyToKey       = self::setXPrefix( self::REPLYTO . self::$D );
        $poIcal           = PatchObject::singleton();
        while( false !== ( $xProp = $iCal->getXprop( null, null, true ))) {
            if( str_starts_with( $xProp[0], $localizationsKey )) {
                $dto->addLocalization(
                    $xProp[1]->value,
                    $poIcal->processFrom( $xProp[1]->getParams())
                );
            }
            elseif( str_starts_with( $xProp[0], $replyToKey )) { // X-REPLYTO-...
                $replyToMethod = strtolower( explode( self::$D, $xProp[0], 3 )[2] );
                if( ! $imipFound || ( $dto::IMIP !== $replyToMethod )) {
                    $dto->addReplyTo( $replyToMethod, $xProp[1]->value );
                }
            }
        } // end while

        // get dto-timezones from dto and locations
        $dtoTimezones = $dto->getLocationsTimezones();
        if( $dto->isTimeZoneSet()) {
            $tzid     = $dto->getTimeZone();
            if( ! in_array( $tzid, $dtoTimezones, true )) {
                $dtoTimezones[] = $tzid;
            }
        }
        // accept only dto-timezones
        foreach( $vtimezones as $timeZoneId => $vtimezone ) {
            if( in_array( $timeZoneId, $dtoTimezones, true )) {
                $dto->addTimeZone( (string) $timeZoneId, TimeZone::processFrom( $timeZoneId, $vtimezone ) );
            }
        }

        $key = self::setXPrefix( self::USEDEFAULTALERTS );
        if( $iCal->isXpropSet( $key )) {
            $dto->setUseDefaultAlerts( $iCal::TRUE === $iCal->getXprop( $key )[1] );
        }

        $iCal->resetCompCounter();
        while( false !== ( $alarm = $iCal->getComponent( $iCal::VALARM ))) {
            [ $uid, $alert ] = Alert::processFrom( $alarm );
            $dto->addAlert( $uid, $alert );
        }

        return $startDateTime ?: null;
    }
}
