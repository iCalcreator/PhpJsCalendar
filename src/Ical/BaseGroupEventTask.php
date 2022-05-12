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
use Kigkonsult\Icalcreator\Vevent;
use Kigkonsult\Icalcreator\Vtodo;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\PhpJsCalendar\Dto\Group as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Task  as TaskDto;

abstract class BaseGroupEventTask extends BaseIcal
{
    /**
     * Ical Group|Event|Task common properties to Vcalendar|Vevent|Vtodo
     *
     * @param GroupDto|EventDto|TaskDto $dto
     * @param Vcalendar|Vevent|Vtodo $iCal
     * @throws Exception
     */
    protected static function groupEventTaskProcessTo(
        GroupDto|EventDto|TaskDto $dto,
        Vcalendar|Vevent|Vtodo $iCal
    ) : void
    {
        $isVeventVtodo = ! $iCal instanceof Vcalendar;
        $value = $dto->getUid();
        if( ! empty( $value )) {
            $iCal->setUid( $value);
        }

        if( $dto->isCreatedSet()) {
            if( $isVeventVtodo ) {
                $iCal->setCreated( $dto->getCreated());
            }
            else {
                $iCal->setXprop( self::setXPrefix( self::CREATED ), $dto->getCreated( true ));
            }
        }

        if( $dto->isUpdatedSet()) {
            $iCal->setLastModified( $dto->getUpdated());
        }

        $params = [];
        if( $dto->isLocaleSet()) {
            $params[Vcalendar::LANGUAGE] = $dto->getLocale();
        }
        if( $dto->isTitleSet()) {
            $value = $dto->getTitle();
            if( $isVeventVtodo ) {
                $iCal->setSummary( $value, $params );
            }
            else {
                $iCal->setName( $value, $params );
            }
        }

        if( $dto->isColorSet()) {
            $iCal->setColor( $dto->getColor());
        }

        // array of "String[Boolean]"
        if(  ! empty( $dto->getCategoriesCount())) {
            foreach( array_keys( $dto->getCategories()) as $category ) {
                $iCal->setCategories( $category, $params );
            }
        }

        // array of "Id[Link]"   to iCal IMAGE/STRUCTURED_DATA
        if( ! empty( $dto->getLinksCount())) {
            Link::processLinksTo( $dto->getLinks(), $iCal );
        }

        // array of "String[Boolean]"
        if( ! empty( $dto->getKeywordsCount())) {
            $iCal->setXprop(
                self::setXPrefix( self::KEYWORDS ),
                implode( self::$itemSeparator, array_keys( $dto->getKeywords()))
            );
        }
    }

    /**
     * Ical Vcalendar|Vevent|Vtodo properties to Group|Event|Task
     *
     * @param Vcalendar|Vevent|Vtodo $iCal
     * @param GroupDto|EventDto|TaskDto $dto
     * @return void
     * @throws Exception
     */
    protected static function groupEventTaskProcessFrom(
        Vcalendar|Vevent|Vtodo $iCal,
        GroupDto|EventDto|TaskDto $dto
    ) : void
    {
        $isVcalendar = $iCal instanceof Vcalendar;
        $dto->setUid( $iCal->getUid());

        $key = self::setXPrefix( self::CREATED );
        if( $isVcalendar && $iCal->isXpropSet( $key )) {
            $dto->setCreated( $iCal->getXprop( $key )[1] );
        }
        elseif( ! $isVcalendar && $iCal->isCreatedSet()) {
            $dto->setCreated( $iCal->getCreated());
        }
        if( $iCal->isLastmodifiedSet()) {
            $dto->setUpdated( $iCal->getLastmodified());
        }

        $locale = null;
        if( ! $isVcalendar && $iCal->isSummarySet()) {
            $value = $iCal->getSummary( true );
            $dto->setTitle( $value->value );
            if( $value->hasParamKey( Vcalendar::LANGUAGE )) {
                $locale= $value->getParams( Vcalendar::LANGUAGE );
            }
        }
        elseif( $isVcalendar && $iCal->isNameSet())  {
            $value = $iCal->getName( null, true ); // accept first only
            $dto->setTitle( $value->value );
            if( $value->hasParamKey( Vcalendar::LANGUAGE )) {
                $locale = $value->getParams( Vcalendar::LANGUAGE );
            }
        }

        if( ! empty( $locale )) {
            $dto->setLocale( $locale );
        }

        while( false !== ( $value = $iCal->getCategories())) {
            $dto->addCategory( $value, true );
        }

        if( $iCal->isColorSet()) {
            $dto->setColor( $iCal->getColor());
        }

        if( ! $isVcalendar && $iCal->isCreatedSet()) {
            $dto->setCreated( $iCal->getCreated());
        }

        // iCal IMAGE + STRUCTURED_DATA to links
        Link::processLinksFrom( $iCal, $dto );

        $keywordKey = self::setXPrefix( self::KEYWORDS );
        if( $iCal->isXpropSet( $keywordKey )) {
            $dto->setKeywords( explode( self::$itemSeparator, $iCal->getXprop( $keywordKey )[1] ) );
        }
    }
}
