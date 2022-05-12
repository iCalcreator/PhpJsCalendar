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
use Kigkonsult\Icalcreator\Participant;
use Kigkonsult\Icalcreator\Pc;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;
use Kigkonsult\Icalcreator\Vlocation;
use Kigkonsult\Icalcreator\Vtodo;
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
     * @param Participant|Vcalendar|Vevent|Vlocation|Vtodo $iCal
     */
    public static function processLinksTo(
        array $links,
        Participant|Vcalendar|Vevent|Vlocation|Vtodo $iCal
    ) : void
    {
        static $setImage = 'setImage';
        static $setStructureddata = 'setStructureddata';
        static $seturl   = 'seturl';
        static $S        = '/';
        $hasImage   = method_exists( $iCal, $setImage );
        $hasSdata   = method_exists( $iCal, $setStructureddata );
        $hasUrl     = method_exists( $iCal, $seturl );
        $isVlocaton = $iCal instanceof Vlocation;
        $urlCnt     = 0;
        $ctKey      = self::setXPrefix( self::CONTENTTYPE );
        $linkKey    = self::setXPrefix( self::LINK );
        foreach( $links as $id => $link ) {
            [ $value, $params ] = self::processTo( $id, $link );
            $hasCtKeyParam = isset( $params[$ctKey] );
            if( $hasImage &&
                $hasCtKeyParam &&
                ( 0 === stripos( $params[$ctKey], $iCal::IMAGE . $S ))) {
                $params[$iCal::VALUE]   = $iCal::URI;
                $params[$iCal::FMTTYPE] = $params[$ctKey];
                unset( $params[$ctKey] );
                $iCal->{$setImage}( $value, $params );
            }
            elseif( ! $isVlocaton && $hasUrl && empty( $urlCnt )) {
                $iCal->{$seturl}( $value, $params );
                ++$urlCnt;
            }
            elseif( $hasSdata ) {
                $params[$iCal::VALUE] = $iCal::URI;
                $iCal->{$setStructureddata}( $value, $params );
            }
            else {
                $params[$iCal::VALUE] = $iCal::URI;
                if( $hasCtKeyParam ) {
                    $params[$iCal::FMTTYPE] = $params[$ctKey];
                    unset( $params[$ctKey] );
                }
                $iCal->setXprop( $linkKey . ++$urlCnt, $value, $params );
            }
        } // end foreach
    }

    /**
     * Link properties to iCal URL/STRUCTURED_DATA property value and (X-)params, return array
     *
     * @param string $id
     * @param LinkDto $linkDto
     * @return mixed[]   [ hrefValue, (x-)params ]
     */
    public static function processTo( string $id, LinkDto $linkDto ) : array
    {
        $href   = $linkDto->getHref();
        $params = [ self::CID => $id ]; // Link:cid default set as uuid

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
     * iCal IMAGE/STRUCTURED_DATA/URL/X-prop properties to Links property
     *
     * @param Participant|Vcalendar|Vevent|Vlocation|Vtodo $iCal
     * @param GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
     * @throws Exception
     */
    public static function processLinksFrom(
        Participant|Vcalendar|Vevent|Vlocation|Vtodo $iCal,
        GroupDto|EventDto|LocationDto|ParticipantDto|TaskDto $dto
    ) : void
    {
        static $getImage = 'getImage';
        static $hasImage = 'isImageSet';
        static $getStructureddata = 'getStructureddata';
        static $hasStructureddata = 'isStructureddataSet';
        static $getUrl   = 'getUrl';
        static $hasUrl   = 'isUrlSet';
        if( method_exists( $iCal, $hasImage ) && $iCal->{$hasImage}()) {
            while( false !== ( $value = $iCal->{$getImage}( null, true ))) {
                if( ! $value->hasParamkey( $iCal::VALUE ) ||
                    ( $iCal::URI !== $value->getParams( $iCal::VALUE ))) {
                    continue;
                }
                if( ! $value->hasParamKey( $iCal::FMTTYPE ) ||
                    ( 0 !== stripos( $value->getParams( $iCal::FMTTYPE ), $iCal::IMAGE ))) {
                    continue;
                }
                [ $lid, $link ] = self::processFrom( $value );
                $dto->addLink(
                    $lid,
                    $link->setContentType( $value->params[$iCal::FMTTYPE] )
                );
            } // end while
        } // end if 'getImage'
        if( ! $iCal instanceof Vlocation &&
            method_exists( $iCal, $hasUrl ) && $iCal->{$hasUrl}()) {
            [ $lid, $link ] = self::processFrom( $iCal->{$getUrl}( true ));
            $dto->addLink( $lid, $link );
        } // end if
        if( method_exists( $iCal, $hasStructureddata ) && $iCal->{$hasStructureddata}()) {
            while( false !== ( $value = $iCal->{$getStructureddata}( null, true ))) {
                if( $value->hasParamKey( $iCal::VALUE ) &&
                    ( $iCal::URI === $value->getParams( $iCal::VALUE ))) {
                    [ $lid, $link ] = self::processFrom( $value );
                    $dto->addLink( $lid, $link );
                }
            } // end while
        } // end if 'getStructureddata'
        $linkKey = self::setXPrefix( self::LINK );
        $ctKey   = self::setXPrefix( self::CONTENTTYPE );
        while( false !== ( $xProp = $iCal->getXprop( null, null, true ))) {
            if( str_starts_with( $xProp[0], $linkKey )) {
                if( $xProp[1]->hasParamKey( $iCal::FMTTYPE )) {
                    $xProp[1]->addParam( $ctKey, $xProp[1]->getParams( $iCal::FMTTYPE ));
                }
                [ $lid, $link ] = self::processFrom( $xProp[1] );
                $dto->addLink( $lid, $link );
            }
        } // end while
    }

    /**
     * Ical Vevent|Vtodo IMAGE/STRUCTURED_DATA/URL/X-prop properties value to Link
     *
     * @param Pc $value   iCal property value and parameters
     * @return mixed[]    [ id, LINK ]
     * @throws Exception
     */
    public static function processFrom( Pc $value ) : array
    {
        // id/cid is default set as uuid
        $linkDto = new LinkDto();
        $id      = $linkDto->getCid();
        $linkDto->setHref( $value->value );

        $params  = self::unXPrefixKeys( $value->params );
        if( isset( $params[self::CID] )) {
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
