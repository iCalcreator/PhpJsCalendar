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
use Kigkonsult\PhpJsCalendar\Dto\AbsoluteTrigger as AbsoluteTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\OffsetTrigger   as OffsetTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\UnknownTrigger  as UnknownTriggerDto;
use Kigkonsult\PhpJsCalendar\Dto\Alert as Dto;
use stdClass;

class Alert extends BaseJson
{
    /**
     * Parse json array to populate new Alert
     *
     * @param string[]|string[][] $jsonArray
     * @return Dto
     * @throws Exception
     */
    public static function parse( array $jsonArray ) : Dto
    {
        $dto = new Dto();
        if( isset( $jsonArray[self::ACTION] )) {
            $dto->setAction( $jsonArray[self::ACTION] );
        }
        if( isset( $jsonArray[self::ACKNOWLEDGED] )) {
            $dto->setAcknowledged( $jsonArray[self::ACKNOWLEDGED] );
        }
        if( isset( $jsonArray[self::RELATEDTO] )) {
            foreach( $jsonArray[self::RELATEDTO] as $uid => $relatedTo ) {
                $dto->addRelatedTo( $uid, Relation::parse( $relatedTo ));
            }
        }
        if( isset( $jsonArray[self::TRIGGER] )) {
            switch( true ) {
                case self::hasObjectType( $jsonArray[self::TRIGGER], self::ABSOLUTETRIGGER ) :
                    $dto->setTrigger( AbsoluteTrigger::parse( $jsonArray[self::TRIGGER] ));
                    break;
                case self::hasObjectType( $jsonArray[self::TRIGGER], self::OFFSETTRIGGER ) :
                    $dto->setTrigger( OffsetTrigger::parse( $jsonArray[self::TRIGGER] ));
                    break;
                default :
                    $dto->setTrigger( UnknownTrigger::parse( $jsonArray[self::TRIGGER] ));
                    break;
            }
        }
        return $dto;
    }

    /**
     * Write Alert Dto properties to json array
     *
     * Ordered as in rfc8984
     *
     * @param Dto $dto
     * @return mixed[]
     */
    public static function write( Dto $dto ) : array
    {
        $jsonArray = [ self::OBJECTTYPE => $dto->getType() ];
        if( $dto->isActionSet()) {
            $jsonArray[self::ACTION] = $dto->getAction( false );
        }
        $trigger   = $dto->getTrigger();
        switch( true ) {
            case ( null === $trigger ) :
                break;
            case ( $trigger instanceof AbsoluteTriggerDto ) :
                $jsonArray[self::TRIGGER] = AbsoluteTrigger::write( $trigger );
                break;
            case ( $trigger instanceof OffsetTriggerDto ):
                $jsonArray[self::TRIGGER] = OffsetTrigger::write( $trigger );
                break;
            case ( $trigger instanceof UnknownTriggerDto ) :
                $jsonArray[self::TRIGGER] = UnknownTrigger::write( $trigger );
                break;
        } // end switch
        if( $dto->isAcknowledgedSet()) {
            $jsonArray[self::ACKNOWLEDGED] = $dto->getAcknowledged();
        }
        // array of "String[Relation]"
        if( ! empty( $dto->getRelatedToCount())) {
            $jsonArray[self::RELATEDTO] = new stdClass();
            foreach( $dto->getRelatedTo() as $uid => $relation ) {
                $jsonArray[self::RELATEDTO]->{$uid} = (object) Relation::write( $relation );
            }
        }
        return $jsonArray;
    }
}
