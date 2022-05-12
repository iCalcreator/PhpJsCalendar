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
namespace Kigkonsult\PhpJsCalendar\Dto;

use DateTime;
use DateTimeInterface;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Traits\RelatedToTrait;

final class Alert extends BaseDto
{
    /**
     * Describes how to alert the user, optional, default: "display"
     *
     * "display":  The alert should be displayed as appropriate for the current device and user context.
     * "email":    The alert should trigger an email sent out to the user, notifying them of the alert.
     *             This action is typically only appropriate for server implementations.
     * Or a value registered in the IANA "JSCalendar Enum Values" registry,
     * or a vendor-specific value
     *
     * @var string|null
     */
    private ? string $action = null;

    /**
     * @var string
     */
    public static string $actionDefault = 'display';

    /**
     * Records when an alert was last acknowledged, optional
     *
     * This is set when the user has dismissed the alert; other clients that sync
     * this property SHOULD automatically dismiss or suppress duplicate
     * alerts (alerts with the same alert id that triggered on or before this date-time).
     *
     * @var DateTimeInterface|null  UTCDateTime
     */
    private ? DateTimeInterface $acknowledged = null;

    /**
     * .. relates this alert to other alerts in the same JSCalendar object
     */
    use RelatedToTrait;

    /**
     * Defines when to trigger the alert, mandatory
     *
     * @var null|OffsetTrigger|AbsoluteTrigger|UnknownTrigger
     */
    private null|OffsetTrigger|AbsoluteTrigger|UnknownTrigger $trigger = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::ALERT;
    }

    /**
     * @param null|bool $defaultIfNotSet
     * @return string|null
     */
    public function getAction( ? bool $defaultIfNotSet = false ) : ? string
    {
        return ( empty( $this->action ) && $defaultIfNotSet) ? self::$actionDefault : $this->action;
    }

    /**
     * Return bool true if action is not null
     *
     * @return bool
     */
    public function isActionSet() : bool
    {
        return ( null !== $this->action );
    }

    /**
     * @param null|string $action
     * @return static
     */
    public function setAction( ? string $action ) : static
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param null|bool $asString  default true
     * @return null|string|DateTimeInterface   DateTime in UTC, string with suffix 'Z'
     */
    public function getAcknowledged( ? bool $asString = true ) : null | string | DateTimeInterface
    {
        return (( $this->acknowledged instanceof DateTimeInterface ) && $asString )
            ? $this->acknowledged->format( self::$UTCDateTimeFMT )
            : $this->acknowledged;
    }

    /**
     * Return bool true if acknowledged is not null
     *
     * @return bool
     */
    public function isAcknowledgedSet() : bool
    {
        return ( null !== $this->acknowledged );
    }

    /**
     * Set acknowledged
     *
     * If empty, UTC date-time now
     * If DateTime, any timezone allowed, converted to UTC DateTime
     * If string (date[time] without timezone!), saved as DateTime with input:date[time] with UTC timezone
     *
     * @param null|string|DateTimeInterface $acknowledged   UTCDateTime
     * @return static
     * @throws Exception
     */
    public function setAcknowledged( null | string | DateTimeInterface $acknowledged = null ) : static
    {
        $this->acknowledged = self::toUtcDateTime( $acknowledged ?? new DateTime(), false );
        return $this;
    }

    /**
     * @return null|AbsoluteTrigger|OffsetTrigger|UnknownTrigger
     */
    public function getTrigger() : null | AbsoluteTrigger | OffsetTrigger | UnknownTrigger
    {
        return $this->trigger;
    }

    /**
     * Return bool true if trigger is not null
     *
     * @return bool
     */
    public function isTriggerSet() : bool
    {
        return ( null !== $this->trigger );
    }

    /**
     * @param AbsoluteTrigger|OffsetTrigger|UnknownTrigger $trigger
     * @return static
     */
    public function setTrigger( AbsoluteTrigger | OffsetTrigger | UnknownTrigger $trigger ) : static
    {
        $this->trigger = $trigger;
        return $this;
    }
}
