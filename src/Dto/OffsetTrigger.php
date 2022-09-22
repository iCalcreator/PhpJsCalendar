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

use DateInterval;
use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Traits\DateInterval2StringTrait;
use Kigkonsult\PhpJsCalendar\Dto\Traits\RelativeToTrait;

final class OffsetTrigger extends BaseDto
{
    /**
     * The offset at which to trigger the alert relative to the time property defined in the "relativeTo" property
     *
     * @var null|DateInterval SignedDuration
     */
    private ? DateInterval $offset = null;

    /**
     * Specifies the time property that the alert offset is relative to
     *
     * "start":  triggers the alert relative to the start of the calendar object
     *  "end":   triggers the alert relative to the end/due time of the calendar object
     * Optional, default: "start"
     */
    use RelativeToTrait;

    /**
     * @var string
     */
    public static string $relativeToDefault = self::START;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::OFFSETTRIGGER;
    }

    /**
     * Class factory method
     *
     * @param DateInterval $offset
     * @return static
     * @throws Exception
     */
    public static function factoryOffset( DateInterval $offset ) : OffsetTrigger
    {
        return ( new self())->setOffset( $offset );
    }
    
    /**
     * @param null|bool $asString  default true
     * @return null|string|DateInterval
     */
    public function getOffset( ? bool $asString = true ) : null | string | DateInterval
    {
        return (( $this->offset instanceof DateInterval ) && $asString )
            ? self::dateInterval2String( $this->offset, true )
            : $this->offset;
    }

    /**
     * Return bool true if offset is not null
     *
     * @return bool
     */
    public function isOffsetSet() : bool
    {
        return ( null !== $this->offset );
    }

    /**
     * @param string|DateInterval $offset
     * @return static
     * @throws Exception
     */
    public function setOffset( string|DateInterval $offset ) : OffsetTrigger
    {
        $this->offset = $offset instanceof DateInterval
            ? $offset
            : new DateInterval( $offset );
        return $this;
    }

    use DateInterval2StringTrait;
}
