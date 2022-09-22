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
use Kigkonsult\Icalcreator\CalendarComponent     as IcalComponent;
use Kigkonsult\Icalcreator\Valarm                as IcalValarm;
use Kigkonsult\PhpJsCalendar\Dto\AbsoluteTrigger as AbsoluteTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\OffsetTrigger   as OffsetTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\UnknownTrigger  as UnknownTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\Alert           as AlertDto;

class Alert extends BaseIcal
{
    /**
     * Alert to iCal Valarm
     *
     * Ordered as in rfc8984
     *
     * @param string $id
     * @param AlertDto $alertDto
     * @param IcalValarm $iCalAlarm
     * @return void
     * @throws Exception
     */
    public static function processToIcal(
        string $id,
        AlertDto $alertDto,
        IcalValarm $iCalAlarm
    ) : void
    {
        $iCalAlarm->setUid( $id );

        if( $alertDto->isActionSet()) {
            $iCalAlarm->setAction( $alertDto->getAction( false ));
        }

        if( $alertDto->isTriggerSet()) {
            $trigger = $alertDto->getTrigger();
            switch( true ) {
                case $trigger instanceof AbsoluteTriggerDto : // No Ical/AbsoluteTrigger
                    $iCalAlarm->setTrigger( $trigger->getWhen( false ) );
                    break;
                case $trigger instanceof OffsetTriggerDto : // No Ical/OffsetTrigger
                    $relativeTo = $trigger->isRelativeToSet()
                        ? $trigger->getRelativeTo()
                        : OffsetTriggerDto::$relativeToDefault;
                    $iCalAlarm->setTrigger(
                        $trigger->getOffset(),
                        [ IcalValarm::RELATED => $relativeTo ]
                    );
                    break;
                case $trigger instanceof UnknownTriggerDto : // NO Ical/UnknownTriggerDto
                    foreach( $trigger->getProperties() as $key => $value ) {
                        $iCalAlarm->setXprop( self::setXPrefix( $key ), $value );
                    }
                    break;
            } // end switch
        } // end if

        if( $alertDto->isAcknowledgedSet()) {
            $iCalAlarm->setAcknowledged( $alertDto->getAcknowledged());
        }

        // array of "String[Relation]"
        if( ! empty( $alertDto->getRelatedToCount())) {
            foreach( $alertDto->getRelatedTo() as $uid => $relation ) {
                $iCalAlarm->setRelatedto( $uid, Relation::processToIcalXparams( $relation ));
            }
        } // end if
    }

    /**
     * iCal Valarm to Alert
     *
     * @param IcalComponent|IcalValarm $icalValarm
     * @return array  [ id, Alert ]
     * @throws Exception
     */
    public static function processFromIcal( IcalComponent|IcalValarm $icalValarm ) : array
    {
        $alertDto = new AlertDto();
        $id       = $icalValarm->getUid();
        $trigger  = new UnknownTriggerDto();
        if( ! $icalValarm->isTriggerSet()) {
            foreach( $icalValarm->getAllXprop() as $xProp ) {
                $trigger->addProperty( self::unsetXPrefix( $xProp[0] ), $xProp[1] );
            }
        } // end if isTriggerSet
        else {
            $triggerContent = $icalValarm->getTrigger( true );
            if( $triggerContent->getValue() instanceof DateTime ) {
                $trigger = AbsoluteTriggerDto::factoryWhen( $triggerContent->getValue() );
            }
            elseif( $triggerContent->getValue() instanceof DateInterval ) {
                $trigger = OffsetTriggerDto::factoryOffset( $triggerContent->getValue());
                if( $triggerContent->hasParamKey( IcalValarm::RELATED )) {
                    $relativeTo = strtolower( $triggerContent->getParams( IcalValarm::RELATED ));
                    if( OffsetTriggerDto::$relativeToDefault !== $relativeTo ) { // skip default
                        $trigger->setRelativeTo( $relativeTo );
                    }
                } // end if
            } // end else
        } // end else
        $alertDto->setTrigger( $trigger );

        if( $icalValarm->isActionSet()) {
            $alertDto->setAction( strtolower( $icalValarm->getAction()));
        }
        if( $icalValarm->isAcknowledgedSet()) {
            $alertDto->setAcknowledged( $icalValarm->getAcknowledged());
        }
        foreach( $icalValarm->getAllRelatedto( true ) as $relatedTo ) {
            [ $uid, $relation ] = Relation::processFromIcalRelatedTo( $relatedTo );
            $alertDto->addRelatedTo( $uid, $relation );
        } // end foreach
        return [ $id, $alertDto ];
    }
}
