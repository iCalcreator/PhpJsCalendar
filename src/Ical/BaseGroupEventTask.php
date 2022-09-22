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
use Kigkonsult\Icalcreator\Vevent      as IcalVevent;
use Kigkonsult\Icalcreator\Vtodo       as IcalVtodo;
use Kigkonsult\Icalcreator\Vcalendar   as IcalVcalendar;
use Kigkonsult\PhpJsCalendar\Dto\Group as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Task  as TaskDto;

abstract class BaseGroupEventTask extends BaseIcal
{
    /**
     * Group|Event|Task common properties to Vcalendar|Vevent|Vtodo
     *
     * @param GroupDto|EventDto|TaskDto $dto
     * @param IcalVcalendar|IcalVevent|IcalVtodo $iCalComp
     * @throws Exception
     */
    protected static function groupEventTaskProcessToIcal(
        GroupDto|EventDto|TaskDto $dto,
        IcalVcalendar|IcalVevent|IcalVtodo $iCalComp
    ) : void
    {
        $isVeventVtodo = ! $iCalComp instanceof IcalVcalendar;
        $value = $dto->getUid();
        if( ! empty( $value )) {
            $iCalComp->setUid( $value);
        }

        if( $dto->isCreatedSet()) {
            if( $isVeventVtodo ) {
                $iCalComp->setCreated( $dto->getCreated());
            }
            else {
                $iCalComp->setXprop( self::setXPrefix( self::CREATED ), $dto->getCreated( true ));
            }
        }

        if( $dto->isUpdatedSet()) {
            $iCalComp->setLastModified( $dto->getUpdated());
        }

        $params = [];
        if( $dto->isLocaleSet()) {
            $params[iCalVcalendar::LANGUAGE] = $dto->getLocale();
        }
        if( $dto->isTitleSet()) {
            $value = $dto->getTitle();
            if( $isVeventVtodo ) {
                $iCalComp->setSummary( $value, $params );
            }
            else {
                $iCalComp->setName( $value, $params );
            }
        }

        if( $dto->isColorSet()) {
            $iCalComp->setColor( $dto->getColor());
        }

        // array of "String[Boolean]"
        if(  ! empty( $dto->getCategoriesCount())) {
            foreach( array_keys( $dto->getCategories()) as $category ) {
                $iCalComp->setCategories( $category, $params );
            }
        }

        // array of "Id[Link]"   to iCal IMAGE/STRUCTURED_DATA
        if( ! empty( $dto->getLinksCount())) {
            Link::processLinksToIcal( $dto->getLinks(), $iCalComp );
        }

        // array of "String[Boolean]"
        if( ! empty( $dto->getKeywordsCount())) {
            $iCalComp->setXprop(
                self::setXPrefix( self::KEYWORDS ),
                implode( self::$itemSeparator, array_keys( $dto->getKeywords()))
            );
        }
    }

    /**
     * Ical Vcalendar|Vevent|Vtodo properties to Group|Event|Task
     *
     * @param IcalVcalendar|IcalVevent|IcalVtodo $iCalComp
     * @param GroupDto|EventDto|TaskDto $dto
     * @return void
     * @throws Exception
     */
    protected static function groupEventTaskProcessFromIcal(
        IcalVcalendar|IcalVevent|IcalVtodo $iCalComp,
        GroupDto|EventDto|TaskDto $dto
    ) : void
    {
        $isVcalendar = $iCalComp instanceof IcalVcalendar;
        $dto->setUid( $iCalComp->getUid());

        $key = self::setXPrefix( self::CREATED );
        if( $isVcalendar && $iCalComp->isXpropSet( $key )) {
            $dto->setCreated( $iCalComp->getXprop( $key )[1] );
        }
        elseif( ! $isVcalendar && $iCalComp->isCreatedSet()) {
            $dto->setCreated( $iCalComp->getCreated());
        }
        if( $iCalComp->isLastmodifiedSet()) {
            $dto->setUpdated( $iCalComp->getLastmodified());
        }

        $locale = null;
        if( ! $isVcalendar && $iCalComp->isSummarySet()) {
            $summary = $iCalComp->getSummary( true );
            $dto->setTitle( $summary->getValue());
            if( $summary->hasParamKey( iCalVcalendar::LANGUAGE )) {
                $locale= $summary->getParams( iCalVcalendar::LANGUAGE );
            }
        }
        elseif( $isVcalendar && $iCalComp->isNameSet())  {
            $name = $iCalComp->getName( null, true ); // accept first only
            $dto->setTitle( $name->getValue());
            if( $name->hasParamKey( iCalVcalendar::LANGUAGE )) {
                $locale = $name->getParams( iCalVcalendar::LANGUAGE );
            }
        }

        if( ! empty( $locale )) {
            $dto->setLocale( $locale );
        }

        foreach( $iCalComp->getAllCategories() as $value ) {
            $dto->addCategory( $value, true );
        }

        if( $iCalComp->isColorSet()) {
            $dto->setColor( $iCalComp->getColor());
        }

        if( ! $isVcalendar && $iCalComp->isCreatedSet()) {
            $dto->setCreated( $iCalComp->getCreated());
        }

        // iCal IMAGE + STRUCTURED_DATA to links
        Link::processLinksFromIcal( $iCalComp, $dto );

        $keywordKey = self::setXPrefix( self::KEYWORDS );
        if( $iCalComp->isXpropSet( $keywordKey )) {
            $dto->setKeywords(
                explode( self::$itemSeparator, $iCalComp->getXprop( $keywordKey )[1] )
            );
        }
    }
}
