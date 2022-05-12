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

trait PercentCompleteTrait
{
    /**
     * Represents the percent completion of the participant for the Task or overall
     *
     * The property value MUST be a positive integer between 0 and 100
     * Optional; participants of a Task or the Task as a whole
     *
     * @var int|null  UnsignedInt
     */
    private ? int $percentComplete = null;

    /**
     * @return int|null
     */
    public function getPercentComplete() : ? int
    {
        return $this->percentComplete;
    }


    /**
     * Return bool true if percentComplete is not null
     *
     * @return bool
     */
    public function isPercentCompleteSet() : bool
    {
        return ( null !== $this->percentComplete );
    }

    /**
     * @param null|int $percentComplete
     * @return static
     */
    public function setPercentComplete( ? int $percentComplete ) : static
    {
        $this->percentComplete = $percentComplete;
        return $this;
    }
}
