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

use DateInterval;
use DateTime;
use Exception;
use Kigkonsult\Icalcreator\Valarm;
use Kigkonsult\PhpJsCalendar\Dto\AbsoluteTrigger as AbsoluteTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\OffsetTrigger   as OffsetTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\UnknownTrigger  as UnknownTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\Alert           as AlertDto;

class Alert extends BaseIcal
{
    /**
     * Ical Alert properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param string $id
     * @param AlertDto $alertDto
     * @param Valarm $alarm
     * @return void
     * @throws Exception
     */
    public static function processTo( string $id, AlertDto $alertDto, Valarm $alarm  ) : void
    {
        $alarm->setUid( $id );

        if( $alertDto->isActionSet()) {
            $alarm->setAction( $alertDto->getAction( false ));
        }

        if( $alertDto->isTriggerSet()) {
            $value = $alertDto->getTrigger();
            switch( true ) {
                case $value instanceof AbsoluteTriggerDto : // No Ical/AbsoluteTrigger
                    $alarm->setTrigger( $value->getWhen( false ) );
                    break;
                case $value instanceof OffsetTriggerDto : // No Ical/OffsetTrigger
                    $alarm->setTrigger(
                        $value->getOffset(),
                        $value->isRelativeToSet() ? [ Valarm::RELATED => $value->getRelativeTo() ] : []
                    );
                    break;
                case $value instanceof UnknownTriggerDto : // NO Ical/UnknownTriggerDto
                    foreach( $value->getProperties() as $key => $value ) {
                        $alarm->setXprop( self::setXPrefix( $key ), $value );
                    }
                    break;
            } // end switch
        }

        if( $alertDto->isAcknowledgedSet()) {
            $alarm->setAcknowledged( $alertDto->getAcknowledged());
        }

        // array of "String[Relation]"
        if( ! empty( $alertDto->getRelatedToCount())) {
            foreach( $alertDto->getRelatedTo() as $uid => $relation ) {
                $alarm->setRelatedto( $uid, Relation::processTo( $relation ));
            }
        }
    }

    /**
     * Ical iCal Valarm to Alert
     *
     * @param Valarm $icalValarm
     * @return mixed[]  [ id, Alert ]
     * @throws Exception
     */
    public static function processFrom( Valarm $icalValarm ) : array
    {
        $alertDto = new AlertDto();
        $id       = $icalValarm->getUid();
        if( $icalValarm->isTriggerSet()) {
            $triggerValue = $icalValarm->getTrigger( true );
            if( $triggerValue->value instanceof DateTime ) {
                $trigger = AbsoluteTriggerDto::factoryWhen( $triggerValue->value );
            }
            elseif( $triggerValue->value instanceof DateInterval ) {
                $trigger = OffsetTriggerDto::factoryOffset( $triggerValue->value );
                if( $triggerValue->hasParamKey( Valarm::RELATED )) {
                    $trigger->setRelativeTo( strtolower( $triggerValue->getParams( Valarm::RELATED )));
                }
            }
        } // end if isTriggerSet
        else {
            $trigger = new UnknownTriggerDto();
            while( false !== ( $xProp = $icalValarm->getXprop())) {
                $trigger->addProperty( self::unsetXPrefix( $xProp[0] ), $xProp[1] );
            }
        } // end if
        $alertDto->setTrigger( $trigger );

        if( $icalValarm->isActionSet()) {
            $alertDto->setAction( strtolower( $icalValarm->getAction()));
        }
        if( $icalValarm->isAcknowledgedSet()) {
            $alertDto->setAcknowledged( $icalValarm->getAcknowledged());
        }
        while( false !== ( $relatedTo = $icalValarm->getRelatedto( null, true ))) {
            [ $uid, $relation ] = Relation::processFrom( $relatedTo );
            $alertDto->addRelatedTo( $uid, $relation );
        } // end while
        return [ $id, $alertDto ];
    }
}
