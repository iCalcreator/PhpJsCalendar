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

use ArrayObject;
use Exception;
use Kigkonsult\Icalcreator\CalendarComponent as IcalComponent;
use Kigkonsult\Icalcreator\Participant       as IcalParticipant;
use Kigkonsult\Icalcreator\Vlocation         as IcalVlocation;
use Kigkonsult\PhpJsCalendar\Dto\Participant as ParticipantDto;

class Participant extends BaseIcal
{
    /**
     * Dto Participant properties to ical Participant and Attendee values
     *
     * Ordered as in rfc8984
     *
     * @param ParticipantDto $participantDto
     * @param IcalParticipant $icalParticipant
     * @param string[] $idEmailArr id[email]
     * @param null|IcalVlocation $iCalVlocation
     * @return array    Ical Attendee : [ attendeeValue, attendeeParams ]
     * @throws Exception
     */
    public static function processToIcal(
        ParticipantDto  $participantDto,
        IcalParticipant $icalParticipant,
        array           $idEmailArr,
        null|IcalVlocation $iCalVlocation
    ) : array
    {
        $attendeeValue  = null;
        $attendeeParams = [ IcalParticipant::X_PARTICIPANTID => $icalParticipant->getUid() ];

        $langParams     = [];
        if( $participantDto->isLanguageSet()) {
            $language   = $participantDto->getLanguage();
            $icalParticipant->setXprop( self::setXPrefix( IcalParticipant::LANGUAGE ), $language );
            $langParams = [ IcalParticipant::LANGUAGE => $language ];
            $attendeeParams += $langParams;
        }

        if( $participantDto->isNameSet()) {
            $value         = $participantDto->getName();
            $icalParticipant->setSummary( $value, $langParams );
            $attendeeParams[IcalParticipant::CN] = $value;
        }

        if( $participantDto->isExpectReplySet()) {
            $attendeeParams[IcalParticipant::RSVP] = $participantDto->getExpectReply()
                ? IcalParticipant::TRUE
                : IcalParticipant::FALSE;
        }

        $caParams = [];
        if( $participantDto->isKindSet()) {
            $kind = $participantDto->getKind();
            $attendeeParams[IcalParticipant::CUTYPE] = $kind;
            $caParams[self::setXPrefix( ParticipantDto::KIND )] = $kind;
        }

        $email = null;
        if( $participantDto->isEmailSet()) {
            $attendeeValue = $email = $participantDto->getEmail();
            $icalParticipant->setCalendaraddress( $email, $caParams );
        }

        if( $participantDto->isDescriptionSet()) {
            $icalParticipant->setDescription( $participantDto->getDescription());
        }

        // array of "String[String]"
        if( ! empty( $participantDto->getSendToCount())) {
            $key  = self::setXPrefix( self::METHOD );
            foreach( $participantDto->getSendTo() as $sendToMethod => $uri ) {
                $uri = self::removeMailtoPrefix( $uri );
                if(( ParticipantDto::IMIP !== $sendToMethod ) ||
                    ( empty( $email ) || ( 0 !== strcasecmp( $email, $uri )))) {
                    $icalParticipant->setContact( $uri, [ $key => $sendToMethod ] );
                }
            } // end foreach
        } // end if sendTo

        // array of "String[Boolean]"  ONLY one icalParticipant::participanttype accepted
        if( ! empty( $participantDto->getRolesCount())) {
            $roles = array_keys( $participantDto->getRoles());
            $first = reset( $roles );
            $icalParticipant->setParticipanttype( strtoupper( $first ));
            $attendeeParams[IcalParticipant::ROLE] = $first;
            $attendeeParams[IcalParticipant::X_PARTICIPANT_TYPE] =
                strtoupper( implode( self::$itemSeparator, $roles ));
        }

        if( null !== $iCalVlocation ) { // one should be ParticipantDto::locationId, if set
            $lid = $iCalVlocation->getUid();
            if( $iCalVlocation->isNameSet()) {
                $icalParticipant->setLocation(
                    $iCalVlocation->getName(),
                    [ IcalParticipant::X_VLOCATIONID => $lid ]
                );
            }
            $icalParticipant->setComponent( $iCalVlocation );
            $attendeeParams[IcalParticipant::X_VLOCATIONID] = $lid;
        } // end if Vlocation

        if( $participantDto->isParticipationStatusSet()) {
            $value = strtoupper( $participantDto->getParticipationStatus( false ));
            $icalParticipant->setStatus( $value );
            $attendeeParams[IcalParticipant::PARTSTAT] = $value;
        }

        if( $participantDto->isParticipationCommentSet()) {
            $value = $participantDto->getParticipationComment();
            $icalParticipant->setComment( $value );
            $attendeeParams[ self::setXPrefix( self::COMMENTS ) ] = $value;
        }

        if( $participantDto->isScheduleAgentSet()) {
            $value = $participantDto->getScheduleAgent( false );
            $key   = self::setXPrefix( self::SCHEDULEAGENT );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }

        if( $participantDto->isScheduleForceSendSet()) { // bool
            $key   = self::setXPrefix( self::SCHEDULEFORCESEND );
            $value = $participantDto->getScheduleForceSend()
                ? IcalParticipant::TRUE
                : IcalParticipant::FALSE;
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }

        if( $participantDto->isScheduleSequenceSet()) {
            $value = $participantDto->getScheduleSequence( false );
            $key   = self::setXPrefix( self::SCHEDULESEQUENCE );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = (string) $value;
        }

        // array of "String[]"
        if( ! empty( $participantDto->getScheduleStatusCount())) {
            $xParam = false;
            foreach( $participantDto->getScheduleStatus() as $value ) {
                if( ! $xParam ) {
                    $attendeeParams[ self::setXPrefix( self::SCHEDULESTATUS ) ] = $value;
                    $xParam = true;
                }
                $values     = explode( self::$SQ, $value, 3 );
                $icalParticipant->setRequeststatus(
                    $values[0],
                    $values[1] ?? null,
                    $values[2] ?? null
                );
            }
        } // end if
        if( $participantDto->isScheduleUpdatedSet()) {
            $value = $participantDto->getScheduleUpdated();
            $key   = self::setXPrefix( self::SCHEDULEUPDATED );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }

        if( $participantDto->isSentBySet()) {
            $attendeeParams[IcalParticipant::SENT_BY] = $participantDto->getSentBy();
        }

        if( $participantDto->isInvitedBySet()) {
            $invitedById = $participantDto->getInvitedBy(); // id to other participant
            if( self::isIdFoundInIdEmailArr( $invitedById, $idEmailArr, $email )) {
                $key = self::setXPrefix( self::INVITEDBY );
                $icalParticipant->setXprop(
                    $key,
                    $email,
                    [ IcalParticipant::X_PARTICIPANTID => $invitedById ]
                );
                $attendeeParams[$key] = $email;
            }
        } // end if

        // array of "Id[Boolean]"  - a number of id to other participants
        if( ! empty( $participantDto->getDelegatedToCount())) {
            foreach( array_keys( $participantDto->getDelegatedTo()) as $x => $particpantId ) {
                if( self::isIdFoundInIdEmailArr( $particpantId, $idEmailArr, $email )) {
                    $attendeeParams[IcalParticipant::DELEGATED_TO][$x] = $email;
                }
            }
        } // end if

        // array of "Id[Boolean]"  - a number of id to other participants
        if( ! empty( $participantDto->getDelegatedFromCount())) {
            foreach( array_keys( $participantDto->getDelegatedFrom()) as $x => $particpantId ) {
                if( self::isIdFoundInIdEmailArr( $particpantId, $idEmailArr, $email )) {
                    $attendeeParams[IcalParticipant::DELEGATED_FROM][$x] = $email;
                }
            }
        } // end if

        // array of "Id[Boolean]"  - a number of id to other participants
        if( ! empty( $participantDto->getMemberOfCount())) {
            foreach( array_keys( $participantDto->getMemberOf()) as $x => $particpantId ) {
                if( self::isIdFoundInIdEmailArr( $particpantId, $idEmailArr, $email )) {
                    $attendeeParams[IcalParticipant::MEMBER][$x] = $email;
                }
            }
        } // end if

        // array of "Id[Link]"   to iCal IMAGE/STRUCTURED_DATA/URL
        if( ! empty( $participantDto->getLinksCount())) {
            Link::processLinksToIcal( $participantDto->getLinks(), $icalParticipant );
        }

        if( $participantDto->isProgressSet()) {
            $value = $participantDto->getProgress();
            $key   = self::setXPrefix(self::PROGRESS );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }

        if( $participantDto->isProgressUpdatedSet()) {
            $value = $participantDto->getProgressUpdated();
            $key   = self::setXPrefix( self::PROGRESSUPDATED );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }

        if( $participantDto->isPercentCompleteSet()) {
            $value = $participantDto->getPercentComplete();
            $key   = self::setXPrefix( self::PERCENTCOMPLETE );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = (string) $value;
        }

        return [ $attendeeValue, $attendeeParams ];
    }

    /**
     * Return bool true if id found in isEmailArr then found email will upd result, otherwise false
     *
     * @param string $id
     * @param array $idEmailArr
     * @param string|null $result
     * @return bool
     */
    public static function isIdFoundInIdEmailArr(
        string $id,
        array $idEmailArr,
        ? string & $result = null
    ) : bool
    {
        $result = null;
        if( isset( $idEmailArr[$id] )) {
            $result = $idEmailArr[$id];
            return true;
        }
        return false;
    }

    /**
     * Return bool true if email found in isEmailArr then found id hit will upd result, otherwise false
     *
     * @param string   $email       strtolower compare
     * @param string[] $idEmailArr  Participant::uid => email
     * @param null|string $result
     * @return bool
     */
    public static function isEmailFoundInIdEmailArr(
        string $email,
        array $idEmailArr,
        ? string & $result = null
    ) : bool
    {
        $result = null;
        if( null === $email ) {
            return false;
        }
        $email = strtolower( self::removeMailtoPrefix( $email ));
        foreach( $idEmailArr as $id2 => $email2 ) {
            if( strtolower( $email2 ) === $email ) {
                $result = (string) $id2;
                return true;
            }
        }
        return false;
    }

    /**
     * Ical Vevent|Vtodo Participant properties to Participant, also from Vevent|Vtodo Attendee (params:same value+uid)
     *
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param string[] $attendeeParams
     * @param string[] $idEmailArr
     * @return array   [ id, Participant, vLocations ]
     * @throws Exception
     */
    public static function processFromIcal(
        IcalComponent|IcalParticipant $icalParticipant,
        array $attendeeParams,
        array $idEmailArr
    ) : array
    {
        $participantDto = new ParticipantDto();
        $id = $icalParticipant->getUid();

        $key = self::setXPrefix( IcalParticipant::LANGUAGE );
        if( $icalParticipant->isXpropSet( $key )) {
            $xProp = $icalParticipant->getXprop(  $key );
            $participantDto->setLanguage( $xProp[1] );
            unset( $attendeeParams[IcalParticipant::LANGUAGE] );
        }

        if( $icalParticipant->isSummarySet()) {
            $participantDto->setName( $icalParticipant->getSummary());
            unset( $attendeeParams[IcalParticipant::CN] );
        }

        $email = null;
        if( $icalParticipant->isCalendaraddressSet()) {
            $calAddress = $icalParticipant->getCalendaraddress( true );
            $email      = self::removeMailtoPrefix( $calAddress->getValue());
            $participantDto->setEmail( $email );
            $key        = self::setXPrefix( ParticipantDto::KIND );
            if( $calAddress->hasParamKey( $key )) {
                $participantDto->setKind( $calAddress->getParams( $key ));
                unset( $attendeeParams[IcalParticipant::CUTYPE] );
            }
        } // end if

        if( $icalParticipant->isDescriptionSet()) {
            $participantDto->setDescription( $icalParticipant->getDescription());
        }

        $key = self::setXPrefix( self::METHOD );
        foreach( $icalParticipant->getAllContact( true ) as $contact ) {
            $sendToMethod = match ( true ) {
                $contact->hasParamKey( $key ) => $contact->getParams( $key ),
                filter_var( $contact->getValue(), FILTER_VALIDATE_URL ) => ParticipantDto::IMIP,
                default => ParticipantDto::OTHER
            };
            if(( ParticipantDto::IMIP !== $sendToMethod ) ||
                ( 0 !== strcasecmp( $email, $contact->getValue()))) {
                $participantDto->addSendTo( $sendToMethod, $contact->getValue());
            }
        } // end foreach

        if( $icalParticipant->isParticipanttypeSet()) {
            $participantDto->addRole( $icalParticipant->getParticipanttype());
        }

        // Vlocations // location
        $vLocations    = [];
        $locationNames = [];
        foreach( $icalParticipant->getComponents( IcalParticipant::VLOCATION ) as $iCalVlocation ) {
            $lid = self::processIcalVlocation( $iCalVlocation, $vLocations, $locationNames );
            if( ! $participantDto->isLocationIdSet()) {
                $participantDto->setLocationId( $lid );
                unset( $attendeeParams[IcalParticipant::X_VLOCATIONID] );
            }
        } // end foreach
        foreach( $icalParticipant->getAllLocation( true ) as $location ) {
            switch( true ) {
                case $location->hasParamKey( IcalParticipant::X_VLOCATIONID ) :
                    $lid  = $location->getParams( IcalParticipant::X_VLOCATIONID );
                    if( isset( $vLocations[$lid] )) { // not found as Vlocation (uid)
                        $iCalVlocation = $vLocations[$lid];
                    }
                    else {
                        $iCalVlocation    = new IcalVlocation();
                        $iCalVlocation->setUid( $lid );
                        $vLocations[$lid] = $iCalVlocation;
                    }
                    break;
                case in_array( strtolower( $location->getValue()), $locationNames, true ) :
                    // already found as Vlocation (name), skip
                    continue 2;
                default :
                    $iCalVlocation    = new IcalVlocation();
                    $lid              = $iCalVlocation->getUid();
                    $vLocations[$lid] = $iCalVlocation;
                    break;
            } // end switch
            $locationNames[]  = strtolower( $location->getValue());
            $iCalVlocation->setName( $location->getValue());
            if( ! $participantDto->isLocationIdSet()) {
                $participantDto->setLocationId( $lid );
                unset( $attendeeParams[IcalParticipant::X_VLOCATIONID] );
            }
       } // end foreach

        if( $icalParticipant->isStatusSet()) {
            $participantDto->setParticipationStatus( $icalParticipant->getStatus());
            unset( $attendeeParams[IcalParticipant::PARTSTAT] );
        }

        if( $icalParticipant->isCommentSet()) { // only one
            $participantDto->setParticipationComment( $icalParticipant->getComment());
            unset( $attendeeParams[self::setXPrefix( self::COMMENTS )] );
        }

        $key   = self::setXPrefix( self::INVITEDBY ); // got email, id expected
        if( $icalParticipant->isXpropSet( $key ))  {
            $xProp = $icalParticipant->getXprop( $key, null, true );
            $email = $xProp[1]->getValue();
            if( isset( $xPropVal[IcalParticipant::X_PARTICIPANTID] )) {
                $participantDto->setInvitedBy( $xPropVal[IcalParticipant::X_PARTICIPANTID] );
                unset( $attendeeParams[$key] );
            }
            elseif( self::isEmailFoundInIdEmailArr( $email, $idEmailArr, $result )) {
                $participantDto->setInvitedBy( $result ); // i.e. id
                unset( $attendeeParams[$key] );
            }
        } // end if

        foreach( [
            self::SCHEDULEAGENT,
            self::SCHEDULEFORCESEND,
            self::SCHEDULESEQUENCE,
            self::SCHEDULEUPDATED,
            self::PROGRESSUPDATED,
            self::PERCENTCOMPLETE,
            self::PROGRESS ] as $key ) {
            $key2 = self::setXPrefix( $key );
            if( ! $icalParticipant->isXpropSet( $key2 ))  {
                continue;
            }
            $xProp = $icalParticipant->getXprop( $key2 );
            $value = match( true ) {
                ( self::SCHEDULEFORCESEND === $key ) => // bool
                    ( IcalParticipant::TRUE === $xProp[1] ),
                in_array( $key, [ self::PERCENTCOMPLETE, self::SCHEDULESEQUENCE ], true  ) => // int
                    (int) $xProp[1],
                default => $xProp[1]
            };
            $setMethod = self::getSetmethodName( $key );
            $participantDto->{$setMethod}( $value );
            unset( $attendeeParams[$key2] );
        } // end foreach

        foreach( $icalParticipant->getAllRequeststatus() as $recStat ) {
            $participantDto->addScheduleStatus( implode( self::$SQ, $recStat ));
            unset( $attendeeParams[self::setXPrefix( self::SCHEDULESTATUS )] );
        } // end foreach

        // iCal IMAGE + STRUCTURED_DATA to links
        Link::processLinksFromIcal( $icalParticipant, $participantDto );

        // upd from opt attendee params NOT found in Participant
        self::processFromIcalArray( $attendeeParams, $participantDto, $idEmailArr );
        return [ $id, $participantDto, $vLocations ];
    }

    /**
     * @param IcalComponent|IcalVlocation $iCalVlocation
     * @param array $vLocations
     * @param array $locationNames
     * @return string
     * @throws Exception
     */
    private static function processIcalVlocation(
        IcalComponent|IcalVlocation $iCalVlocation,
        array & $vLocations,
        array & $locationNames
    ) : string
    {
        $lid              = $iCalVlocation->getUid();
        $vLocations[$lid] = $iCalVlocation;
        if( $iCalVlocation->isNameSet()) {
            $locationName = $iCalVlocation->getName();
            if( ! in_array( $locationName, $locationNames, true )) {
                $locationNames[] = $locationName;
            }
        }
        return $lid;
    }

    /**
     * Update Participant Dto from Ical Vevent|Vtodo Attendee property params
     *
     * Same as above?
     *
     * @param string[] $params
     * @param ParticipantDto $participantDto
     * @param string[] $idEmailArr
     * @return void
     */
    public static function processFromIcalArray(
        array $params,
        ParticipantDto $participantDto,
        array $idEmailArr
    ) : void
    {
        if( empty( $params )) {
            return;
        }

        $pao = new ArrayObject( $params, ArrayObject::ARRAY_AS_PROPS );

        if( isset( $pao->{IcalParticipant::LANGUAGE} )) {
            $participantDto->setLanguage( $pao->{IcalParticipant::LANGUAGE} );
        }

        if( isset( $pao->{IcalParticipant::RSVP} )) {
            $participantDto->setExpectReply(( IcalParticipant::TRUE === $pao->{IcalParticipant::RSVP} ));
        }

        if( isset( $pao->{IcalParticipant::CN} )) {
            $participantDto->setName( $pao->{IcalParticipant::CN} );
        }

        $key = self::setXPrefix( ParticipantDto::KIND );
        if( isset( $pao->{$key} ) && ! empty( $pao->{$key} )) {
            $participantDto->setKind( $pao->{$key} );
        }
        elseif( isset( $pao->{IcalParticipant::CUTYPE} )) {
            $participantDto->setKind( $pao->{IcalParticipant::CUTYPE} );
        }

        foreach( [ IcalParticipant::X_PARTICIPANT_TYPE, IcalParticipant::ROLE ] as $actorKey ) {
            if( isset( $pao->{$actorKey} )) {
                $participantDto->setRoles( explode( self::$itemSeparator, $pao->{$actorKey} ));
            }
        } // end foreach

        if( isset( $pao->{IcalParticipant::X_VLOCATIONID} )) {
            $participantDto->setLocationId( $pao->{IcalParticipant::X_VLOCATIONID} );
        }

        if( isset( $pao->{IcalParticipant::PARTSTAT} )) {
            $participantDto->setParticipationStatus( $pao->{IcalParticipant::PARTSTAT} );
        }

        $key = self::setXPrefix( self::COMMENTS );
        if( isset( $pao->{$key} )) {
            $participantDto->setParticipationComment( $pao->{$key} );
        }

        $key = self::setXPrefix( self::INVITEDBY ); // have email, expect id
        if( isset( $pao->{$key} ) &&
            self::isEmailFoundInIdEmailArr( $pao->{$key}, $idEmailArr, $result )) {
            $participantDto->setInvitedBy( $result );
        }

        foreach( [
            self::SCHEDULEAGENT,
            self::SCHEDULEFORCESEND,
            self::SCHEDULESEQUENCE,
            self::SCHEDULEUPDATED,
            self::PROGRESSUPDATED,
            self::PERCENTCOMPLETE,
            self::PROGRESS ] as $key ) {
            $key2      = self::setXPrefix( $key );
            if( ! isset( $pao->{$key2} )) {
                continue;
            }
            $setMethod = self::getSetmethodName( $key );
            $value     = match( true ) {
                ( self::SCHEDULEFORCESEND === $key ) =>
                    ( IcalParticipant::TRUE === $pao->{$key2} ),
                in_array( $key, [ self::PERCENTCOMPLETE, self::SCHEDULESEQUENCE ], true  ) =>
                    (int) $pao->{$key2},
                default => $pao->{$key2}
            };
            $participantDto->{$setMethod}( $value );
        } // end foreach

        if( isset( $pao->{IcalParticipant::SENT_BY} )) {
            $participantDto->setSentBy(
                self::removeMailtoPrefix( $pao->{IcalParticipant::SENT_BY} )
            );
        }

        self::processIcalParamMultiEmail( $pao, $idEmailArr, $participantDto );
    }

    /**
     * @param ArrayObject $pao
     * @param array $idEmailArr
     * @param ParticipantDto $participantDto
     */
    private static function processIcalParamMultiEmail(
        ArrayObject $pao,
        array $idEmailArr,
        ParticipantDto $participantDto
    ) : void
    {
        static $ICALPARAMMULTIKEYS = [
            'addDelegatedTo'   => IcalParticipant::DELEGATED_TO,
            'addDelegatedFrom' => IcalParticipant::DELEGATED_FROM,
            'addMemberOf'      => IcalParticipant::MEMBER
        ];
        foreach( $ICALPARAMMULTIKEYS as $method => $emailKey ) {
            if( isset( $pao->{$emailKey} ) ) {
                foreach((array) $pao->{$emailKey} as $email ) {
                    if( self::isEmailFoundInIdEmailArr( $email, $idEmailArr, $result ) ) {
                        $participantDto->{$method}( $result );
                    }
                } // end foreach
            } // end if
        } // end foreach
    }

    /**
     * @param string $key
     * @return string
     */
    private static function getSetmethodName( string $key ) : string
    {
        static $PREFIX = 'set';
        return $PREFIX . ucfirst( $key );

    }
}
