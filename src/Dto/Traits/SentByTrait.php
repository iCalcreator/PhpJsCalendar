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
namespace Kigkonsult\PhpJsCalendar\Dto\Traits;

trait SentByTrait
{
    /**
     * Event/Task : The email address in the "From" header of the email in which this calendar object was received
     *              or only if if the calendar object is received via iMIP or as an attachment to a message
     *
     * Participant : The email address in the "From" header of the email that last updated this participant via iMIP
     *               SHOULD only be set if the email address is different to that in the mailto URI of
     *               this participant's "imip" method in the "sendTo" property
     *
     * Optional
     *
     * @var string|null
     */
    protected ? string $sentBy = null;

    /**
     * @return string|null
     */
    public function getSentBy() : ?string
    {
        return $this->sentBy;
    }

    /**
     * Return bool true if sentBy is not null
     *
     * @return bool
     */
    public function isSentBySet() : bool
    {
        return ( null !== $this->sentBy );
    }

    /**
     * The SentBy value MUST be a valid "addr-spec" value as defined in Section 3.4.1 of [RFC5322].
     *
     * @param null|string $sentBy
     * @return static
     */
    public function setSentBy( ? string $sentBy ) : static
    {
        $this->sentBy = $sentBy;
        return $this;
    }
}
