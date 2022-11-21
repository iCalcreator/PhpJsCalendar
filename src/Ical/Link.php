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

use Exception;
use Kigkonsult\Icalcreator\Participant       as IcalParticipant;
use Kigkonsult\Icalcreator\Pc;
use Kigkonsult\Icalcreator\Vcalendar         as IcalVcalendar;
use Kigkonsult\Icalcreator\Vevent            as IcalVevent;
use Kigkonsult\Icalcreator\Vlocation         as IcalVlocation;
use Kigkonsult\Icalcreator\Vtodo             as IcalVtodo;
use Kigkonsult\PhpJsCalendar\Dto\Event       as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Group       as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Link        as LinkDto;
use Kigkonsult\PhpJsCalendar\Dto\Location    as LocationDto;
use Kigkonsult\PhpJsCalendar\Dto\Participant as ParticipantDto;
use Kigkonsult\PhpJsCalendar\Dto\Task        as TaskDto;

class Link extends BaseIcal
{
    /**
     * Links property to iCal IMAGE/STRUCTURED_DATA/URL/X-prop property
     *
     * @param LinkDto[] $links
     * @param IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp
     */
    public static function processLinksToIcal(
        array $links,
        IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp
    ) : void
    {
        static $setImage = 'setImage';
        static $setStructureddata = 'setStructureddata';
        static $setUrl    = 'seturl';
        static $S         = '/';
        static $ctKey, $linkKey = null;
        if( empty( $ctKey )) {
            $ctKey      = self::setXPrefix( self::CONTENTTYPE );
            $linkKey    = self::setXPrefix( self::LINK );
        }
        $hasPropImage     = method_exists( $iCalComp, $setImage );
        $hasPropStrucData = method_exists( $iCalComp, $setStructureddata );
        $hasPropUrl       = method_exists( $iCalComp, $setUrl );
        $isVlocaton = $iCalComp instanceof IcalVlocation;
        $urlCnt     = 0;
        foreach( $links as $id => $link ) {
            [ $value, $params ] = self::processTo( $id, $link );
            $params[$iCalComp::VALUE] = $iCalComp::URI;
            $hasCtKeyParam      = isset( $params[$ctKey] );
            switch( true ) {
                case ( $hasPropImage &&
                    $hasCtKeyParam &&
                    ( 0 === stripos( $params[$ctKey], $iCalComp::IMAGE . $S ))) :
                    $params[$iCalComp::FMTTYPE] = $params[$ctKey];
                    unset( $params[$ctKey] );
                    $iCalComp->{$setImage}( $value, $params ); // 0-X times
                    break;
                case ( ! $isVlocaton && $hasPropUrl && empty( $urlCnt )) : // 0-1 times
                    unset( $params[$iCalComp::VALUE] );
                    $iCalComp->{$setUrl}( $value, $params );
                    ++$urlCnt;
                    break;
                case  $hasPropStrucData :
                    $iCalComp->setStructureddata( $value, $params ); // 0-X times
                    break;
                case $hasCtKeyParam :
                    $params[$iCalComp::FMTTYPE] = $params[$ctKey];
                    unset( $params[$ctKey] );
                    // fall through
                default :
                    $iCalComp->setXprop( $linkKey . ++$urlCnt, $value, $params ); // 0-X times
            } // end switch
        } // end foreach
    }

    /**
     * Link properties to iCal URL/STRUCTURED_DATA property value and (X-)params, return array
     *
     * @param string $id
     * @param LinkDto $linkDto
     * @return array   [ hrefValue, (x-)params ]
     */
    public static function processTo( string $id, LinkDto $linkDto ) : array
    {
        $href   = $linkDto->getHref();
        $params = [ self::CID => $id ]; // Link:cid (content-id) default set as uuid
        if( $linkDto->isContentTypeSet()) {
            $params[self::CONTENTTYPE] = $linkDto->getContentType();
        }
        if( $linkDto->isSizeSet()) {
            $params[self::SIZE] = $linkDto->getSize();
        }
        if( $linkDto->isRelSet()) {
            $params[self::REL] = $linkDto->getRel();
        }
        if( $linkDto->isDisplaySet()) {
            $params[self::DISPLAY] = $linkDto->getDisplay();
        }
        if( $linkDto->isTitleSet()) {
            $params[self::TITLE] = $linkDto->getTitle();
        }
        return [ $href, self::xPrefixKeys( $params ) ];
    }

    /**
     * iCal IMAGE/STRUCTURED_DATA/URL/X-prop properties to Link properties
     *
     * @param IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp
     * @param GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
     * @throws Exception
     */
    public static function processLinksFromIcal(
        IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp,
        GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
    ) : void
    {
        static $isImageSet = 'isImageSet';
        static $isUrlSet   = 'isUrlSet';
        static $isStructureddataSet = 'isStructureddataSet';
        if( self::existsAndIsset( $iCalComp, $isImageSet )) {
            self::extractIcalImage( $iCalComp, $dto ); // IMAGE (fmttype=image/.....) to Link
        }
        if(( ! $iCalComp instanceof IcalVlocation ) &&
            self::existsAndIsset( $iCalComp, $isUrlSet )) {
            [ $lid, $link ] = self::processFrom( $iCalComp->getUrl( true ));
            $dto->addLink( $lid, $link ); // URL to Link
        } // end if
        if( self::existsAndIsset( $iCalComp, $isStructureddataSet )) {
            self::extractIcalStructureddata( $iCalComp, $dto ); // STRUCTURED_DATA (value=uri) to Link
        }
        self::extractIcalXlinks( $iCalComp, $dto ); // X-LINK to Link
    }

    /**
     * @param IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp
     * @param GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractIcalImage(
        IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp,
        GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
    ) : void
    {
        foreach( $iCalComp->getAllImage(true ) as $imagePc ) {
            if( ! $imagePc->hasParamKey( IcalVcalendar::VALUE, $iCalComp::URI )) {
                continue;
            }
            if( ! $imagePc->hasParamKey( $iCalComp::FMTTYPE ) ||
                ( 0 !== stripos( $imagePc->getParams( $iCalComp::FMTTYPE ), $iCalComp::IMAGE ))) {
                continue;
            }
            [ $lid, $link ] = self::processFrom( $imagePc );
            $dto->addLink(
                $lid,
                $link->setContentType( $imagePc->getParams( $iCalComp::FMTTYPE ))
            );
        } // end foreach
    }

    /**
     * @param IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp
     * @param GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractIcalStructureddata(
        IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp,
        GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
    ) : void
    {
        foreach( $iCalComp->getAllStructureddata( true ) as $strucDataPc ) {
            // temp iCal STRUCTURED_DATA bug (pre 2.1.66) : VALUE URI if no VALUE found
            if( ! $strucDataPc->hasParamKey( $iCalComp::VALUE )) {
                $strucDataPc->addParamValue( $iCalComp::VALUE, $iCalComp::URI );
            }
            if( $strucDataPc->hasParamKey( $iCalComp::VALUE, $iCalComp::URI )) {
                [ $lid, $link ] = self::processFrom( $strucDataPc );
                $dto->addLink( $lid, $link );
            }
        } // end foreach
    }

    /**
     * @param IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp
     * @param GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
     * @throws Exception
     */
    private static function extractIcalXlinks(
        IcalParticipant|IcalVcalendar|IcalVevent|IcalVlocation|IcalVtodo $iCalComp,
        GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
    ) : void
    {
        $linkKey = self::setXPrefix( self::LINK );
        $ctKey   = self::setXPrefix( self::CONTENTTYPE );
        foreach( $iCalComp->getAllXprop( true ) as $xProp ) {
            if( ! str_starts_with( $xProp[0], $linkKey )) {
                continue;
            }
            if( $xProp[1]->hasParamKey( $iCalComp::FMTTYPE )) {
                $xProp[1]->addParam( $ctKey, $xProp[1]->getParams( $iCalComp::FMTTYPE ));
            }
            [ $lid, $link ] = self::processFrom( $xProp[1] );
            $dto->addLink( $lid, $link );
        } // end foreach
    }

    /**
     * Ical Vevent|Vtodo IMAGE/STRUCTURED_DATA/URL/X-prop property value to Link
     *
     * @param Pc $value   iCal property value and parameters
     * @return array    [ id, LINK ]
     * @throws Exception
     */
    public static function processFrom( Pc $value ) : array
    {
        // id/cid is default set as uuid
        $linkDto = new LinkDto();
        $id      = $linkDto->getCid();
        $linkDto->setHref( $value->getValue());
        $params  = self::unXPrefixKeys( $value->getParams());
        if( isset( $params[self::CID] )) { // content-id
            $linkDto->setCid( $params[self::CID] );
            $id  = $params[self::CID]; // replace
        }
        $key = strtolower( self::CONTENTTYPE );
        if( isset( $params[$key] )) {
            $linkDto->setContentType( $params[$key] );
        }
        if( isset( $params[self::SIZE] )) {
            $linkDto->setSize((int) $params[self::SIZE] );
        }
        if( isset( $params[self::REL] )) {
            $linkDto->setRel( $params[self::REL] );
        }
        if( isset( $params[self::DISPLAY] )) {
            $linkDto->setDisplay( $params[self::DISPLAY] );
        }
        if( isset( $params[self::TITLE] )) {
            $linkDto->setTitle( $params[self::TITLE] );
        }
        return [ $id, $linkDto ];
    }
}
