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
use Kigkonsult\PhpJsCalendar\Dto\Participant as Dto;
use stdClass;

class Participant extends BaseJson
{
    /**
     * Parse json array to populate new Participant
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::NAME] )) {
            $dto->setName( $jsonArray[self::NAME] );
        }
        if( isset( $jsonArray[self::EMAIL] )) {
            $dto->setEmail( $jsonArray[self::EMAIL] );
        }
        if( isset( $jsonArray[self::DESCRIPTION] )) {
            $dto->setDescription( $jsonArray[self::DESCRIPTION] );
        }
        if( isset( $jsonArray[self::SENDTO] )) {
            foreach( $jsonArray[self::SENDTO] as $method => $uri ) {
                $dto->addSendTo( $method, $uri );
            }
        }
        if( isset( $jsonArray[self::KIND] )) {
            $dto->setKind( $jsonArray[self::KIND] );
        }
        if( isset( $jsonArray[self::ROLES] )) {
            foreach( $jsonArray[self::ROLES] as $role => $bool ) {
                $dto->addRole( $role, self::jsonBool2Php( $bool ));
            }
        }
        if( isset( $jsonArray[self::LOCATIONID] )) {
            $dto->setLocationId( $jsonArray[self::LOCATIONID] );
        }
        if( isset( $jsonArray[self::LANGUAGE] )) {
            $dto->setLanguage( $jsonArray[self::LANGUAGE] );
        }
        if( isset( $jsonArray[self::PARTICIPATIONSTATUS] )) {
            $dto->setParticipationStatus( $jsonArray[self::PARTICIPATIONSTATUS] );
        }
        if( isset( $jsonArray[self::PARTICIPATIONCOMMENT] )) {
            $dto->setParticipationComment( $jsonArray[self::PARTICIPATIONCOMMENT] );
        }
        if( isset( $jsonArray[self::EXPECTREPLY] )) {
            $dto->setExpectReply( self::jsonBool2Php( $jsonArray[self::EXPECTREPLY] ));
        }
        if( isset( $jsonArray[self::SCHEDULEAGENT] )) {
            $dto->setScheduleAgent( $jsonArray[self::SCHEDULEAGENT] );
        }
        if( isset( $jsonArray[self::SCHEDULEFORCESEND] )) {
            $dto->setScheduleForceSend( self::jsonBool2Php( $jsonArray[self::SCHEDULEFORCESEND] ));
        }
        if( isset( $jsonArray[self::SCHEDULESEQUENCE] )) {
            $dto->setScheduleSequence((int) $jsonArray[self::SCHEDULESEQUENCE] );
        }
        if( isset( $jsonArray[self::SCHEDULESTATUS] )) {
            foreach( $jsonArray[self::SCHEDULESTATUS] as $status ) {
                $dto->addScheduleStatus( $status );
            }
        }
        if( isset( $jsonArray[self::SCHEDULEUPDATED] )) {
            $dto->setScheduleUpdated( $jsonArray[self::SCHEDULEUPDATED] );
        }
        if( isset( $jsonArray[self::SENTBY] )) {
            $dto->setSentBy( $jsonArray[self::SENTBY] );
        }
        if( isset( $jsonArray[self::INVITEDBY] )) {
            $dto->setInvitedBy( $jsonArray[self::INVITEDBY] );
        }
        if( isset( $jsonArray[self::DELEGATEDTO] )) {
            foreach( $jsonArray[self::DELEGATEDTO] as $delegatdTo => $bool) {
                $dto->addDelegatedTo( $delegatdTo, ( 'true' === $bool ));
            }
        }
        if( isset( $jsonArray[self::DELEGATEDFROM] )) {
            foreach( $jsonArray[self::DELEGATEDFROM] as $delegatdFrom => $bool ) {
                $dto->addDelegatedFrom( $delegatdFrom, ( 'true' === $bool ));
            }
        }
        if( isset( $jsonArray[self::MEMBEROF] )) {
            foreach( $jsonArray[self::MEMBEROF] as $memberOf => $bool ) {
                $dto->addMemberOf( $memberOf, ( 'true' === $bool ));
            }
        }
        if( isset( $jsonArray[self::LINKS] )) {
            foreach( $jsonArray[self::LINKS] as $lid => $link ) {
                $dto->addLink( $lid, Link::parse( $lid, $link ));
            }
        }
        if( isset( $jsonArray[self::PROGRESS] )) {
            $dto->setProgress( $jsonArray[self::PROGRESS] );
        }
        if( isset( $jsonArray[self::PROGRESSUPDATED] )) {
            $dto->setProgressUpdated( $jsonArray[self::PROGRESSUPDATED] );
        }
        if( isset( $jsonArray[self::PERCENTCOMPLETE] )) {
            $dto->setPercentComplete((int) $jsonArray[self::PERCENTCOMPLETE] );
        }
        return $dto;
    }

    /**
     * Write Participant Dto properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return array
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];
        if( $dto->isNameSet()) {
            $jsonArray[self::NAME] = $dto->getName();
        }
        if( $dto->isEmailSet()) {
            $jsonArray[self::EMAIL] = $dto->getEmail();
        }
        if( $dto->isDescriptionSet()) {
            $jsonArray[self::DESCRIPTION] = $dto->getDescription();
        }
        // array of "String[String]"
        if( ! empty( $dto->getSendToCount())) {
            foreach( $dto->getSendTo() as $method => $uri ) {
                $jsonArray[self::SENDTO][$method] = $uri;
            }
        }
        if( $dto->isKindSet()) {
            $jsonArray[self::KIND] = $dto->getKind();
        }
        // array of "String[Boolean]"
        if( ! empty( $dto->getRolesCount())) {
            foreach( $dto->getRoles() as $role => $bool ) {
                $jsonArray[self::ROLES][$role] = $bool;
            }
        }
        if( $dto->isLocationIdSet()) {
            $jsonArray[self::LOCATIONID] = $dto->getLocationId();
        }
        if( $dto->isLanguageSet()) {
            $jsonArray[self::LANGUAGE] = $dto->getLanguage();
        }
        if( $dto->isParticipationStatusSet()) {
            $jsonArray[self::PARTICIPATIONSTATUS] = $dto->getParticipationStatus();
        }
        if( $dto->isParticipationCommentSet()) {
            $jsonArray[self::PARTICIPATIONCOMMENT] = $dto->getParticipationComment();
        }
        if( $dto->isExpectReplySet() && $dto->getExpectReply()) { // skip default false
            $jsonArray[self::EXPECTREPLY] = self::phpBool2Json( true );
        }
        if( $dto->isScheduleAgentSet()) {
            $jsonArray[self::SCHEDULEAGENT] = $dto->getScheduleAgent();
        }
        if( $dto->isScheduleForceSendSet() && $dto->getScheduleForceSend()) { // skip default false
            $jsonArray[self::SCHEDULEFORCESEND] = self::phpBool2Json( $dto->getScheduleForceSend());
        }
        if( $dto->isScheduleSequenceSet()) {
            $jsonArray[self::SCHEDULESEQUENCE] = $dto->getScheduleSequence();
        }
        // aray of "String[]"
        if( ! empty( $dto->getScheduleStatusCount())) {
            foreach( $dto->getScheduleStatus() as $x => $value ) {
                $jsonArray[self::SCHEDULESTATUS][$x] = $value;
            }
        }
        if( $dto->isScheduleUpdatedSet()) {
            $jsonArray[self::SCHEDULEUPDATED] = $dto->getScheduleUpdated();
        }
        if( $dto->isSentBySet()) {
            $jsonArray[self::SENTBY] = $dto->getSentBy();
        }
        if( $dto->isInvitedBySet()) {
            $jsonArray[self::INVITEDBY] = $dto->getInvitedBy();
        }
        // array of "Id[Boolean]"
        if( ! empty( $dto->getDelegatedToCount())) {
            foreach( $dto->getDelegatedTo() as $x => $bool ) {
                $jsonArray[self::DELEGATEDTO][$x] = self::phpBool2Json( $bool );
            }
        }
        // array of "Id[Boolean]"
        if( ! empty( $dto->getDelegatedFromCount())) {
            foreach( $dto->getDelegatedFrom() as $x => $bool ) {
                $jsonArray[self::DELEGATEDFROM][$x] = self::phpBool2Json( $bool );
            }
        }
        // array of "Id[Boolean]"
        if( ! empty( $dto->getMemberOfCount())) {
            foreach( $dto->getMemberOf() as $x => $bool ) {
                $jsonArray[self::MEMBEROF][$x] = self::phpBool2Json( $bool );
            }
        }
        // array of "Id[Link]"
        if( ! empty( $dto->getLinksCount())) {
            $jsonArray[self::LINKS] = new stdClass();
            foreach( $dto->getLinks() as $lid => $link ) {
                $jsonArray[self::LINKS]->{$lid} = Link::write( $lid, $link );
            }
        }
        if( $dto->isProgressSet()) {
            $jsonArray[self::PROGRESS] = $dto->getProgress();
        }
        if( $dto->isProgressUpdatedSet()) {
            $jsonArray[self::PROGRESSUPDATED] = $dto->getProgressUpdated();
        }
        if( $dto->isPercentCompleteSet()) {
            $jsonArray[self::PERCENTCOMPLETE] = $dto->getPercentComplete();
        }
        return $jsonArray;
    }
}
