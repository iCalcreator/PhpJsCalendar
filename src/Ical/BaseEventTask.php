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
                $iCal->setExrule( RecurrenceRule::processToIcalRecur( $excludedRecurrenceRule, $tzid ));
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
                    PatchObject::processToIcalXparams( $patchObject )
                );
            }
        }

        // array of "Id[Location]"
        $vLocations = [];  // lid[Vloctions]
        if( ! empty( $dto->getLocationsCount())) {
            $locationCnt = 0;
            foreach( $dto->getLocations() as $lid => $location ) {
                $vLocations[$lid] = Location::processToIcal( $lid,  $location, $locale );
                if( 0 === $locationCnt ) {  // only one Ical location accepted
                    [ $locationValue, $locationParams ] = Location::processToIcalLocationArr( $lid,  $location, $locale );
                    if( ! empty( $locationValue )) { // skip location without name
                        $iCal->setLocation( $locationValue, $locationParams );
                    }
                }
                ++$locationCnt;
            } // end foreach
        } // end locations

        // array of "Id[Participant]"
        if( ! empty( $dto->getParticipantsCount())) {
            $particpants = [];
            $idEmailArr  = []; // used to map participant id to email, [ id => email ]
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
                    Participant::processToIcal(
                        $participant,
                        $iCal->newParticipant()->setUid((string) $pid ),
                        $idEmailArr,
                        $participantVlocation
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
                $iCal->setRrule( RecurrenceRule::processToIcalRecur( $recurrenceRule, $tzid ));
            }
        }
        // PatchObject ??
        // array of "LocalDateTime[PatchObject]"
        if( ! empty( $dto->getRecurrenceOverridesCount())) {
            foreach( $dto->getRecurrenceOverrides() as $localDateTime => $patchObject) {
                $iCal->setRdate( $localDateTime, PatchObject::processToIcalXparams( $patchObject ));
            }
        }
        // array of "String[Relation]"
        if( ! empty( $dto->getRelatedToCount())) {
            foreach( $dto->getRelatedTo() as $uid => $relation ) {
                $iCal->setRelatedto( $uid, Relation::processToIcalXparams( $relation ));
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
                $iCal->setComponent( VirtualLocation::processToIcal( $id, $virtualLocation ));
            }
        }

        $iCalVtimezones = [];
        // array of "TimeZoneId[TimeZone]"
        if( ! empty( $dto->getTimeZonesCount())) {
            foreach( $dto->getTimeZones() as $timeZoneId => $timeZone ) {
                $iCalVtimezones[$timeZoneId] = TimeZone::processToIcal( $timeZoneId, $timeZone );
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
                Alert::processToIcal( $id, $alert, $iCal->newValarm());
            }
        }

        return [ $iCalVtimezones, $startDateTime ];
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
        $methodKey = self::setXPrefix( self::METHOD );
        if( $iCalComponent->isXpropSet( $methodKey )) {
            $dto->setMethod( $iCalComponent->getXprop( $methodKey )[1] );
        }

        if( $iCalComponent->isDescriptionSet()) {
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
        } // end if

        if( $iCalComponent->isPrioritySet()) {
            $dto->setPriority( $iCalComponent->getPriority() );
        }

        if( $iCalComponent->IsClassSet()) {
            $dto->setPrivacy( $iCalComponent->getClass());
        }

        if( $iCalComponent->isRecurrenceidSet()) {
            $contents = $iCalComponent->getRecurrenceid();
            $dto->setRecurrenceId( $contents );
            $dto->setRecurrenceIdTimeZone( $contents->getTimezone()->getName());
        }

        if( $iCalComponent->isRequestStatusSet()) {
            $dto->setRequeststatus( implode( self::$SQ, $iCalComponent->getRequestStatus()));
        }

        $key = self::setXPrefix( self::SENTBY );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setSentBy( $iCalComponent->getXprop( $key)[1] );
        }

        if( $iCalComponent->isSequenceSet()) {
            $dto->setSequence( $iCalComponent->getSequence());
        }

        $tzid = $startDateTime = null;
        if( $iCalComponent->isDtstartSet()) {
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
        } // end dtstart set

        $key = self::setXPrefix( self::EXCLUDED );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setExcluded( $iCalComponent::TRUE === $iCalComponent->getXprop( $key )[1] );
        }

        if( $iCalComponent->isExruleSet()) {
            $dto->addExcludedRecurrenceRule(
                RecurrenceRule::processFromIcalRecur( $iCalComponent->getExrule(),
                    $tzid )
            );
        }

        // Vlocations, Attendees && Participants
        $dtoLocations       = []; // lid[location]   may also contain Vlocation(s) from Participants
        $virtualLocationKey = self::setXPrefix( self::VIRTUALLOCATION ); // also used below
        foreach( $iCalComponent->getComponents( $iCalComponent::VLOCATION ) as $vlocation ) { // 1. iCal components
            if( $vlocation->isXpropSet( $virtualLocationKey )) {
                continue; // VirtualLocation found, skip here
            }
            [ $lid, $dtoLocation ] = Location::processFromIcal( $vlocation );
            if( ! isset( $dtoLocations[$lid] )) { // add location if not found
                $dtoLocations[$lid] = $dtoLocation;
            }
        } // end foreach

        $attendees   = []; // email[params]
        $idEmailArr  = []; // id[email]
        foreach( $iCalComponent->getAllAttendee( true ) as $attendee ) {
            $calAddr = self::removeMailtoPrefix( $attendee->getValue());
            $attendeeParams = $attendee->getParams();
            if( isset( $attendeeParams[$iCalComponent::X_PARTICIPANTID] )) {
                $id  = $attendeeParams[$iCalComponent::X_PARTICIPANTID];
            }
            else {
                $id  = $dto::getNewUid();
                $attendeeParams[$iCalComponent::X_PARTICIPANTID] = $id;
            }
            $attendees[$calAddr] = $attendee->getParams();
            $idEmailArr[$id]     = $calAddr;
        } // end foreach
        foreach( $iCalComponent->getComponents( $iCalComponent::PARTICIPANT ) as $icalParticipant ) {
            $attendeeParams = [];
            self::processIcalParticipant(
                $icalParticipant,
                $attendees,
                $attendeeParams,
                $idEmailArr
            );
            [ $id, $participant, $vLocations ] =
                Participant::processFromIcal( $icalParticipant, $attendeeParams, $idEmailArr );
            foreach( $vLocations as $vlocation ) {
                [ $lid, $dtoLocation ] = Location::processFromIcal( $vlocation );
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
            Participant::processFromIcalArray( $attendeeParams, $participantDto, $idEmailArr );
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
        if( $iCalComponent->isLocationSet()) {
            $iCalPropLocations[] = $iCalComponent->getLocation( null, true );  // 2. ical property location
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
            if( $iCalPropLocation->hasParamKey( $iCalComponent::X_VLOCATIONID )) {
                $locationId = $iCalPropLocation->getParams( $iCalComponent::X_VLOCATIONID );
                if( ! isset( $dtoLocations[$locationId] ) ) {
                    // add location if  'Location id'  not found
                    [ $lid, $dtoLocation ] = Location::fromIcalLocation( $iCalPropLocation );
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
                [ $lid, $dtoLocation ] = Location::fromIcalLocation( $iCalPropLocation );
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

        if( $iCalComponent->isRruleSet()) {
            $dto->addRecurrenceRule( RecurrenceRule::processFromIcalRecur( $iCalComponent->getRrule(), $tzid ));
        }
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

        foreach( $iCalComponent->getAllRelatedto( true ) as $relatedto ) {
            [ $id, $relation ] = Relation::processFromIcalRelatedTo( $relatedto );
            $dto->addRelatedTo( $id, $relation );
        }

        // check for iCal Vlocations with VirtualLocation
        foreach( $iCalComponent->getComponents( $iCalComponent::VLOCATION ) as $vlocation ) {
            if( ! $vlocation->isXpropSet( $virtualLocationKey )) {
                continue;
            }
            [ $id, $virtualLocation ] = VirtualLocation::processFromIcal( $vlocation );
            $dto->addVirtualLocation( $id, $virtualLocation );
        } // end foreach

        $key = self::setXPrefix( self::FREEBUSYSTATUS );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setFreeBusyStatus( $iCalComponent->getXprop( $key)[1] );
        }

        $imipFound = false;
        if( $iCalComponent->isOrganizerSet()) {
            $dto->addReplyTo( $dto::IMIP, $iCalComponent->getOrganizer());
            $imipFound = true;
        }

        // Localizations and replyTo as iCal xProps
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

        // get dto-timezones from dto and locations
        $dtoTimezones = $dto->getLocationsTimezones();
        if( $dto->isTimeZoneSet()) {
            $tzid     = $dto->getTimeZone();
            if( ! in_array( $tzid, $dtoTimezones, true )) {
                $dtoTimezones[] = $tzid;
            }
        } // end if
        // accept only dto-timezones
        foreach( $iCalVtimezones as $timeZoneId => $vtimezone ) {
            if( in_array( $timeZoneId, $dtoTimezones, true )) {
                $dto->addTimeZone(
                    (string) $timeZoneId,
                    TimeZone::processFromIcal( $timeZoneId, $vtimezone )
                );
            }
        } // end foreach

        $key = self::setXPrefix( self::USEDEFAULTALERTS );
        if( $iCalComponent->isXpropSet( $key )) {
            $dto->setUseDefaultAlerts(( $iCalComponent::TRUE === $iCalComponent->getXprop( $key )[1] ));
        }

        foreach( $iCalComponent->getComponents( $iCalComponent::VALARM ) as  $alarm ) {
            [ $uid, $alert ] = Alert::processFromIcal( $alarm );
            $dto->addAlert( $uid, $alert );
        }

        return $startDateTime ?: null;
    }

    /**
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendees       [ * calAddr => attendeeParams ];
     * @param array $attendeeParams  [ * key => value ]
     * @param array $idEmailArr      [ * id => calAddr ]
     * @throws Exception
     */
    protected static function processIcalParticipant(
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendees,
        array & $attendeeParams,
        array & $idEmailArr
    ) : void
    {
        if( $icalParticipant->isCalendaraddressSet()) {
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
        } // end if isCalendaraddressSet
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
