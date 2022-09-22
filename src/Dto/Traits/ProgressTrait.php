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

trait ProgressTrait
{
    /**
     * The progress of (the participant for) a task
     *
     * It MUST NOT be set if the "participationStatus" of this participant is any value other than "accepted"
     * The default progress value for a Task (in order of evaluation):
     *    "completed":     if the "progress" property value of all participants is "completed"
     *    "failed":        if at least one "progress" property value of a participant is "failed"
     *    "in-process":    if at least one "progress" property value of a participant is "in-process"
     *    "needs-action":  if none of the other criteria match
     * or
     *   "cancelled":  indicates the task was cancelled
     *    another value registered in the IANA "JSCalendar Enum Values" registry
     *    vendor-specific value
     *
     * Optional; only allowed for (participants of) a Task
     *
     * @var string|null
     */
    private ? string $progress = null;

    /**
     * @return string|null
     */
    public function getProgress() : ?string
    {
        return $this->progress;
    }

    /**
     * Return bool true if progress is not null
     *
     * @return bool
     */
    public function isProgressSet() : bool
    {
        return ( null !== $this->progress );
    }

    /**
     * @param string $progress
     * @return static
     */
    public function setProgress( string $progress ) : static
    {
        $this->progress = $progress ? strtolower( $progress ) : null;
        return $this;
    }
}
