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
use DateTimeZone;
use Exception;

abstract class BaseDto implements DtoInterface
{
    /**
     * @var string UTCDateTime string format without fractions
     */
    public static string $UTCDateTimeFMT   = 'Y-m-d\TH:i:s\Z';

    /**
     * @var string LocalDateTime string format without timezone and fractions
     */
    public static string $LocalDateTimeFMT = 'Y-m-d\TH:i:s';

    /**
     * @var string   This specifies the type that this object represents
     */
    protected string $type;

    /**
     * Class constructor, as for now empty
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return static
     */
    public function setType( string $type ) : static
    {
        $this->type = $type;
        return $this;
    }

    /** class static methods */

    /**
     * Return an unique id as GUID v4 string
     *
     * @return string
     * @throws Exception
     * @see https://www.php.net/manual/en/function.com-create-guid.php#117893
     */
    public static function getNewUid() : string
    {
        static $FMT = '%s%s-%s-%s-%s-%s%s%s';
        $bytes      = random_bytes( 16 );
        $bytes[6]   = chr( ord( $bytes[6] ) & 0x0f | 0x40 ); // set version to 0100
        $bytes[8]   = chr( ord( $bytes[8] ) & 0x3f | 0x80 ); // set bits 6-7 to 10
        return vsprintf( $FMT, str_split( bin2hex( $bytes ), 4 ));
    }

    /**
     * Return (new) UTC DateTime from date string or DateTime
     *
     * @param string|DateTimeInterface $input
     * @param null|bool $asLocalDateTime   true: set UTC timezone, false: is in UTC
     * @return DateTime|null
     * @throws Exception
     */
    protected static function toUtcDateTime(
        string | DateTimeInterface $input,
        ? bool $asLocalDateTime = true
    ) : ? DateTime
    {
        static $UTC = 'UTC';
        return match ( true ) {
            ! $input instanceof DateTimeInterface
                            => new DateTime( $input, new DateTimeZone( $UTC )),
            empty( $input->getOffset()) // is in UTC
                            => clone $input,
            $asLocalDateTime            // set UTC
                            => new DateTime(
                                $input->format( self::$LocalDateTimeFMT ),
                                new DateTimeZone( $UTC )
                            ),
            default         => ( clone $input )->setTimezone( new DateTimeZone( $UTC )),
        }; // end switch
    }
}
