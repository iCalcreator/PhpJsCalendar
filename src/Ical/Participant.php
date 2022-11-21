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
        ? IcalVlocation $iCalVlocation
    ) : array
    {
        $attendeeParams = [ IcalParticipant::X_PARTICIPANTID => $icalParticipant->getUid() ];
        $langParams     = self::extractJslanguge( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsName( $participantDto, $icalParticipant, $attendeeParams, $langParams );
        self::extractJsExpectReply( $participantDto, $attendeeParams );
        $caParams       = self::extractJsKind( $participantDto, $attendeeParams );
        $attendeeValue  = self::extractCalendaraddress( $participantDto, $icalParticipant, $caParams );
        if( $participantDto->isDescriptionSet()) {
            $icalParticipant->setDescription( $participantDto->getDescription());
        }
        self::extractJsSendTo( $participantDto, $icalParticipant, $attendeeValue );
        self::extractJsRoles( $participantDto, $icalParticipant, $attendeeParams );
        if( null !== $iCalVlocation ) { // one should be ParticipantDto::locationId, if set
            self::processVlocation( $iCalVlocation, $icalParticipant, $attendeeParams );
        }
        self::extractJsParticipationStatus( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsParticipationComment( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsScheduleAgent( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJScheduleForceSend( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsScheduleSequence( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsScheduleStatus( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsScheduleUpdated( $participantDto, $icalParticipant, $attendeeParams );
        if( $participantDto->isSentBySet()) {
            $attendeeParams[IcalParticipant::SENT_BY] = $participantDto->getSentBy();
        }
        self::extractJsInvitedBy( $participantDto, $icalParticipant, $attendeeParams, $idEmailArr );
        self::extractJsDelegatedTo( $participantDto, $attendeeParams, $idEmailArr );
        self::extractJsDelegatedFrom( $participantDto, $attendeeParams, $idEmailArr );
        self::extractJsMemberOf( $participantDto, $attendeeParams, $idEmailArr );
        // array of "Id[Link]"   to iCal IMAGE/STRUCTURED_DATA/URL
        if( ! empty( $participantDto->getLinksCount())) {
            Link::processLinksToIcal( $participantDto->getLinks(), $icalParticipant );
        }
        self::extractJsProgress( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsProgressUpdated( $participantDto, $icalParticipant, $attendeeParams );
        self::extractJsPercentComplete( $participantDto, $icalParticipant, $attendeeParams );
        return [ $attendeeValue, $attendeeParams ];
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     * @return array
     */
    private static function extractJslanguge(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : array
    {
        $langParams     = [];
        if( $participantDto->isLanguageSet()) {
            $language   = $participantDto->getLanguage();
            $icalParticipant->setXprop( self::setXPrefix( IcalParticipant::LANGUAGE ), $language );
            $langParams = [ IcalParticipant::LANGUAGE => $language ];
            $attendeeParams += $langParams;
        }
        return $langParams;
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     * @param array $langParams
     * @return void
     */
    private static function extractJsName(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams,
        array $langParams
    ) : void
    {
        if( $participantDto->isNameSet()) {
            $value         = $participantDto->getName();
            $icalParticipant->setSummary( $value, $langParams );
            $attendeeParams[IcalParticipant::CN] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @return void
     */
    private static function extractJsExpectReply(
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isExpectReplySet()) {
            $attendeeParams[IcalParticipant::RSVP] = $participantDto->getExpectReply()
                ? IcalParticipant::TRUE
                : IcalParticipant::FALSE;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @return array
     */
    private static function extractJsKind(
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : array
    {
        $caParams = [];
        if( $participantDto->isKindSet()) {
            $kind = $participantDto->getKind();
            $attendeeParams[IcalParticipant::CUTYPE] = $kind;
            $caParams[self::setXPrefix( ParticipantDto::KIND )] = $kind;
        }
        return $caParams;
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $caParams
     * @return null|string
     */
    private static function extractCalendaraddress(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array $caParams
    ) : ? string
    {
        $attendeeValue = null;
        if( $participantDto->isEmailSet()) {
            $attendeeValue = $participantDto->getEmail();
            $icalParticipant->setCalendaraddress( $attendeeValue, $caParams );
        }
        return $attendeeValue;
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param null|string email
     * @return void
     */
    private static function extractJsSendTo(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        ? string $email
    ) : void
    {
        if( empty( $participantDto->getSendToCount())) {
            return;
        }
        $key  = self::setXPrefix( self::METHOD );
        foreach( $participantDto->getSendTo() as $sendToMethod => $uri ) {// array of "String[String]"
            $uri = self::removeMailtoPrefix( $uri );
            if(( ParticipantDto::IMIP !== $sendToMethod ) ||
                ( empty( $email ) || ( 0 !== strcasecmp( $email, $uri )))) {
                $icalParticipant->setContact( $uri, [ $key => $sendToMethod ] );
            }
        } // end foreach
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     * @return void
     */
    private static function extractJsRoles(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( empty( $participantDto->getRolesCount())) {
            return;
        }
        // array of "String[Boolean]"  ONLY one icalParticipant::participanttype accepted
        $roles = array_keys( $participantDto->getRoles());
        $first = reset( $roles );
        $icalParticipant->setParticipanttype( strtoupper( $first ));
        $attendeeParams[IcalParticipant::ROLE] = $first;
        $attendeeParams[IcalParticipant::X_PARTICIPANT_TYPE] =
            strtoupper( implode( self::$itemSeparator, $roles ));
    }

    /**
     * @param IcalVlocation $iCalVlocation
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     * @throws Exception
     */
    private static function processVlocation(
        IcalVlocation $iCalVlocation,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        $lid = $iCalVlocation->getUid();
        if( $iCalVlocation->isNameSet()) {
            $icalParticipant->setLocation(
                $iCalVlocation->getName(),
                [ IcalParticipant::X_VLOCATIONID => $lid ]
            );
        }
        $icalParticipant->setComponent( $iCalVlocation );
        $attendeeParams[IcalParticipant::X_VLOCATIONID] = $lid;
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsParticipationStatus(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isParticipationStatusSet()) {
            $value = strtoupper( $participantDto->getParticipationStatus( false ));
            $icalParticipant->setStatus( $value );
            $attendeeParams[IcalParticipant::PARTSTAT] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsParticipationComment(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isParticipationCommentSet()) {
            $value = $participantDto->getParticipationComment();
            $icalParticipant->setComment( $value );
            $attendeeParams[ self::setXPrefix( self::COMMENTS ) ] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsScheduleAgent(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isScheduleAgentSet()) {
            $value = $participantDto->getScheduleAgent( false );
            $key   = self::setXPrefix( self::SCHEDULEAGENT );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJScheduleForceSend(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isScheduleForceSendSet()) { // bool
            $key   = self::setXPrefix( self::SCHEDULEFORCESEND );
            $value = $participantDto->getScheduleForceSend()
                ? IcalParticipant::TRUE
                : IcalParticipant::FALSE;
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsScheduleSequence(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isScheduleSequenceSet()) {
            $value = $participantDto->getScheduleSequence( false );
            $key   = self::setXPrefix( self::SCHEDULESEQUENCE );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = (string) $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsScheduleStatus(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( empty( $participantDto->getScheduleStatusCount())) {
            return;
        }
        $xParam = false;
        foreach( $participantDto->getScheduleStatus() as $value ) { // array of "String[]"
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
        } // end foreach
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsScheduleUpdated(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isScheduleUpdatedSet()) {
            $value = $participantDto->getScheduleUpdated();
            $key   = self::setXPrefix( self::SCHEDULEUPDATED );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     * @param array $idEmailArr
     */
    private static function extractJsInvitedBy(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams,
        array $idEmailArr
    ) : void
    {
        if( ! $participantDto->isInvitedBySet()) {
            return;
        }
        $invitedById = $participantDto->getInvitedBy(); // id to other participant
        if( self::isIdFoundInIdEmailArr( $invitedById, $idEmailArr, $email )) {
            $key = self::setXPrefix( self::INVITEDBY );
            $icalParticipant->setXprop(
                $key,
                $email,
                [ IcalParticipant::X_PARTICIPANTID => $invitedById ]
            );
            $attendeeParams[$key] = $email;
        } // end if
    }

    /**
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @param array $idEmailArr
     */
    private static function extractJsDelegatedTo(
        ParticipantDto $participantDto,
        array & $attendeeParams,
        array $idEmailArr
    ) : void
    {
        if( empty( $participantDto->getDelegatedToCount())) {
            return;
        }
        // array of "Id[Boolean]"  - a number of id to other participants
        foreach( array_keys( $participantDto->getDelegatedTo()) as $x => $particpantId ) {
            if( self::isIdFoundInIdEmailArr( $particpantId, $idEmailArr, $email )) {
                $attendeeParams[IcalParticipant::DELEGATED_TO][$x] = $email;
            }
        } // end foreach
    }

    /**
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @param array $idEmailArr
     */
    private static function extractJsDelegatedfrom(
        ParticipantDto $participantDto,
        array & $attendeeParams,
        array $idEmailArr
    ) : void
    {
        if( empty( $participantDto->getDelegatedFromCount())) {
            return;
        }
        // array of "Id[Boolean]"  - a number of id to other participants
        foreach( array_keys( $participantDto->getDelegatedFrom()) as $x => $particpantId ) {
            if( self::isIdFoundInIdEmailArr( $particpantId, $idEmailArr, $email )) {
                $attendeeParams[IcalParticipant::DELEGATED_FROM][$x] = $email;
            }
        } // end foreach
    }

    /**
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @param array $idEmailArr
     */
    private static function extractJsMemberOf(
        ParticipantDto $participantDto,
        array & $attendeeParams,
        array $idEmailArr
    ) : void
    {
        if( empty( $participantDto->getMemberOfCount())) {
            return;
        }
        // array of "Id[Boolean]"  - a number of id to other participants
        foreach( array_keys( $participantDto->getMemberOf()) as $x => $particpantId ) {
            if( self::isIdFoundInIdEmailArr( $particpantId, $idEmailArr, $email )) {
                $attendeeParams[IcalParticipant::MEMBER][$x] = $email;
            }
        } // end foreach
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsProgress(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isProgressSet()) {
            $value = $participantDto->getProgress();
            $key   = self::setXPrefix(self::PROGRESS );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsProgressUpdated(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isProgressUpdatedSet()) {
            $value = $participantDto->getProgressUpdated();
            $key   = self::setXPrefix( self::PROGRESSUPDATED );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = $value;
        }
    }

    /**
     * @param ParticipantDto $participantDto
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param array $attendeeParams
     */
    private static function extractJsPercentComplete(
        ParticipantDto $participantDto,
        IcalComponent|IcalParticipant $icalParticipant,
        array & $attendeeParams
    ) : void
    {
        if( $participantDto->isPercentCompleteSet()) {
            $value = $participantDto->getPercentComplete();
            $key   = self::setXPrefix( self::PERCENTCOMPLETE );
            $icalParticipant->setXprop( $key, $value );
            $attendeeParams[$key] = (string) $value;
        }
    }

    /**
     * Return IcalVlocation for participant if location id set and found in vLocations
     *
     * @param ParticipantDto $participantDto
     * @param array $vLocations
     * @param array & $pVlocationLids
     * @return IcalVlocation|null
     */
    public static function getVlocationUsingLocationId(
        ParticipantDto $participantDto,
        array $vLocations,
        array & $pVlocationLids
    ) : ? IcalVlocation
    {
        $participantVlocation = null;
        if( $participantDto->isLocationIdSet()) {
            $lid = $participantDto->getLocationId();
            if( isset( $vLocations[$lid] ) ) {
                $participantVlocation = $vLocations[$lid];
//              unset( $vLocations[$lid] ); // may also be used in another participant
                $pVlocationLids[] = $lid;
            }
        } // end if
        return $participantVlocation;
    }

    /**
     * Return bool true if id found in isEmailArr then found email will upd result, otherwise false
     *
     * @param string $id
     * @param array $idEmailArr
     * @param string|null $result
     * @return bool
     */
    private static function isIdFoundInIdEmailArr(
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
        if( empty( $email )) {
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
        self::extractIcalXlanguage( $icalParticipant, $participantDto, $attendeeParams );
        if( $icalParticipant->isSummarySet()) {
            $participantDto->setName( $icalParticipant->getSummary());
            unset( $attendeeParams[IcalParticipant::CN] );
        }
        $email = self::extractIcalCalendaraddress( $icalParticipant, $participantDto, $attendeeParams );
        if( $icalParticipant->isDescriptionSet()) {
            $participantDto->setDescription( $icalParticipant->getDescription());
        }
        self::extractIcalContact( $icalParticipant, $participantDto, $email );
        if( $icalParticipant->isParticipanttypeSet()) {
            $participantDto->addRole( $icalParticipant->getParticipanttype());
        }
        // Vlocations + location
        $vLocations = self::extractIcalLocations( $icalParticipant, $participantDto,$attendeeParams );
        self::extractIcalStatus( $icalParticipant, $participantDto, $attendeeParams );
        self::extractIcalComment( $icalParticipant, $participantDto, $attendeeParams );
        self::extractIcalXinvitedby( $icalParticipant, $participantDto, $attendeeParams, $idEmailArr );
        // process iCal X-props
        // SCHEDULEAGENT, -FORCESEND, -SEQUENCE, -UPDATED, PROGRESSUPDATED, PERCENTCOMPLETE, PROGRESS
        self::extractIcalSpecXprops( $icalParticipant, $participantDto, $attendeeParams );
        self::extractIcaRequeststatus( $icalParticipant, $participantDto, $attendeeParams );
        // iCal IMAGE + STRUCTURED_DATA to links
        Link::processLinksFromIcal( $icalParticipant, $participantDto );
        // upd from opt attendee params NOT found in Participant
        self::processFromIcalArray( $attendeeParams, $participantDto, $idEmailArr );
        return [ $id, $participantDto, $vLocations ];
    }

    /**
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @return void
     */
    private static function extractIcalXlanguage(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : void
    {
        $key = self::setXPrefix( IcalParticipant::LANGUAGE );
        if( $icalParticipant->isXpropSet( $key )) {
            $xProp = $icalParticipant->getXprop(  $key );
            $participantDto->setLanguage( $xProp[1] );
            unset( $attendeeParams[IcalParticipant::LANGUAGE] );
        }
    }

        /**
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @return string|null
     */
    private static function extractIcalCalendaraddress(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : ? string
    {
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
        return $email;
    }

    /**
     * Extract Contacts from iCal
     *
     * @param IcalComponent|IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param null|string $email
     * @return void
     */
    private static function extractIcalContact(
        IcalComponent|IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        ? string $email
    ) : void
    {
        if( ! $icalParticipant->isContactSet()) {
            return;
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
    }

    /**
     * Extract Vlocations and locations from iCal
     *
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto,
     * @param array $attendeeParams
     * @return array
     * @throws Exception
     */
    private static function extractIcalLocations(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : array
    {
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
                    if( isset( $vLocations[$lid] )) { // found as Vlocation (on uid)
                        $iCalVlocation = $vLocations[$lid];
                    }
                    else {
                        $iCalVlocation    = new IcalVlocation();
                        $iCalVlocation->setUid( $lid );
                        $vLocations[$lid] = $iCalVlocation;
                    }
                    break;
                case self::foundInArray( $location->getValue(), $locationNames ) :
                    // already found as Vlocation (name), skip
                    continue 2;
                default :
                    $iCalVlocation    = new IcalVlocation();
                    $lid              = $iCalVlocation->getUid();
                    $vLocations[$lid] = $iCalVlocation;
                    break;
            } // end switch
            $locationNames[] = strtolower( $location->getValue());
            $iCalVlocation->setName( $location->getValue());
            if( ! $participantDto->isLocationIdSet()) {
                $participantDto->setLocationId( $lid );
                unset( $attendeeParams[IcalParticipant::X_VLOCATIONID] );
            }
        } // end foreach
        return $vLocations;
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
            if( ! self::foundInArray( $locationName, $locationNames )) {
                $locationNames[] = strtolower( $locationName );
            }
        }
        return $lid;
    }

    /**
     * Return bool true if (strtolower) needle found in haystack
     * @param string $needle
     * @param array $haystack
     * @return bool
     */
    private static function foundInArray( string $needle, array $haystack ) : bool
    {
        return in_array( strtolower( $needle ), $haystack, true );
    }

    /**
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @rturn void
     */
    private static function extractIcalStatus(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : void
    {
        if( $icalParticipant->isStatusSet()) {
            $participantDto->setParticipationStatus( $icalParticipant->getStatus());
            unset( $attendeeParams[IcalParticipant::PARTSTAT] );
        }
    }

    /**
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @rturn void
     */
    private static function extractIcalComment(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : void
    {
        if( $icalParticipant->isCommentSet()) { // only one
            $participantDto->setParticipationComment( $icalParticipant->getComment());
            unset( $attendeeParams[self::setXPrefix( self::COMMENTS )] );
        }
    }

    /**
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @param array $idEmailArr
     * @rturn void
     */
    private static function extractIcalXinvitedby(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams,
        array $idEmailArr
    ) : void
    {
        $key   = self::setXPrefix( self::INVITEDBY ); // got email, id expected
        if( ! $icalParticipant->isXpropSet( $key )) {
            return;
        }
        $xProp = $icalParticipant->getXprop( $key, null, true );
        $email = $xProp[1]->getValue();
        if( $xProp[1]->hasParamKey( IcalParticipant::X_PARTICIPANTID )) {
            $participantDto->setInvitedBy( $xProp[1]->getParams( IcalParticipant::X_PARTICIPANTID ));
            unset( $attendeeParams[$key] );
        }
        elseif( self::isEmailFoundInIdEmailArr( $email, $idEmailArr, $result )) {
            $participantDto->setInvitedBy( $result ); // i.e. id
            unset( $attendeeParams[$key] );
        }
    }

    /**
     * @var array
     */
    private static array $SPECKEYS = [
        self::SCHEDULEAGENT,
        self::SCHEDULEFORCESEND,
        self::SCHEDULESEQUENCE,
        self::SCHEDULEUPDATED,
        self::PROGRESS,
        self::PROGRESSUPDATED,
        self::PERCENTCOMPLETE
    ];

    /**
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     * @return void
     */
    private static function extractIcalSpecXprops(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : void
    {
        foreach( self::$SPECKEYS as $key ) {
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
    }

    /**
     * @param IcalParticipant $icalParticipant
     * @param ParticipantDto $participantDto
     * @param array $attendeeParams
     */
    private static function extractIcaRequeststatus(
        IcalParticipant $icalParticipant,
        ParticipantDto $participantDto,
        array & $attendeeParams
    ) : void
    {
        if( ! $icalParticipant->isRequeststatusSet()) {
            return;
        }
        foreach( $icalParticipant->getAllRequeststatus() as $recStat ) {
            $participantDto->addScheduleStatus( implode( self::$SQ, $recStat ));
            unset( $attendeeParams[self::setXPrefix( self::SCHEDULESTATUS )] );
        } // end foreach
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
        self::extractKind( $pao, $participantDto );
        self::extractRole( $pao, $participantDto );
        if( isset( $pao->{IcalParticipant::X_VLOCATIONID} )) {
            $participantDto->setLocationId( $pao->{IcalParticipant::X_VLOCATIONID} );
        }
        if( isset( $pao->{IcalParticipant::PARTSTAT} )) {
            $participantDto->setParticipationStatus( $pao->{IcalParticipant::PARTSTAT} );
        }
        self::extractParticipationComment( $pao, $participantDto );
        self::extractInvitedBy( $pao, $participantDto, $idEmailArr );
        self::extractIcalParamsXprops( $pao, $participantDto );
        self::extractSentBy( $pao, $participantDto );
        self::processIcalParamMultiEmail( $pao, $idEmailArr, $participantDto );
    }

    /**
     * @param ArrayObject $pao
     * @param ParticipantDto $participantDto
     * @return void
     */
    private static function extractKind(
        ArrayObject $pao,
        ParticipantDto $participantDto
    ) : void
    {
        $key = self::setXPrefix( ParticipantDto::KIND );
        if( isset( $pao->{$key} ) && ! empty( $pao->{$key} )) {
            $participantDto->setKind( $pao->{$key} );
        }
        elseif( isset( $pao->{IcalParticipant::CUTYPE} )) {
            $participantDto->setKind( $pao->{IcalParticipant::CUTYPE} );
        }
    }

    /**
     * @param ArrayObject $pao
     * @param ParticipantDto $participantDto
     * @return void
     */
    private static function extractRole(
        ArrayObject $pao,
        ParticipantDto $participantDto
    ) : void
    {
        foreach( [ IcalParticipant::X_PARTICIPANT_TYPE, IcalParticipant::ROLE ] as $actorKey ) {
            if( isset( $pao->{$actorKey} )) {
                $participantDto->setRoles( explode( self::$itemSeparator, $pao->{$actorKey} ));
            }
        } // end foreach
    }

    /**
     * @param ArrayObject $pao
     * @param ParticipantDto $participantDto
     * @return void
     */
    private static function extractParticipationComment(
        ArrayObject $pao,
        ParticipantDto $participantDto
    ) : void
    {
        $key = self::setXPrefix( self::COMMENTS );
        if( isset( $pao->{$key} )) {
            $participantDto->setParticipationComment( $pao->{$key} );
        }
    }

    /**
     * @param ArrayObject $pao
     * @param ParticipantDto $participantDto
     * @param array $idEmailArr
     * @return void
     */
    private static function extractInvitedBy(
        ArrayObject $pao,
        ParticipantDto $participantDto,
        array $idEmailArr
    ) : void
    {
        $key = self::setXPrefix( self::INVITEDBY ); // have email, expect id
        if( isset( $pao->{$key} ) &&
            self::isEmailFoundInIdEmailArr( $pao->{$key}, $idEmailArr, $result )) {
            $participantDto->setInvitedBy( $result );
        }
    }

    /**
     * @param ArrayObject $pao
     * @param ParticipantDto $participantDto
     * @return void
     */
    private static function extractSentBy(
        ArrayObject $pao,
        ParticipantDto $participantDto
    ) : void
    {
        if( isset( $pao->{IcalParticipant::SENT_BY} )) {
            $participantDto->setSentBy(
                self::removeMailtoPrefix( $pao->{IcalParticipant::SENT_BY} )
            );
        }
    }

    /**
     * @param ArrayObject $pao
     * @param ParticipantDto $participantDto
     * @return void
     */
    private static function extractIcalParamsXprops(
        ArrayObject $pao,
        ParticipantDto $participantDto
    ) : void
    {
        foreach( self::$SPECKEYS as $key ) {
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
    }

    /**
     * @param ArrayObject $pao
     * @param array $idEmailArr
     * @param ParticipantDto $participantDto
     * @return void
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
