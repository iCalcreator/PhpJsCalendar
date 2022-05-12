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
/**
 * autoload.php
 *
 * PhpJsCalendar package autoloader
 */

spl_autoload_register(
    function( $class ) {
        static $BS      = '\\';
        static $PHP     = '.php';
        static $PREFIX  = 'Kigkonsult\\PhpJsCalendar\\';
        static $SRC     = 'src';
        static $SRCDIR  = null;
        if( is_null( $SRCDIR )) {
            $SRCDIR  = __DIR__ . DIRECTORY_SEPARATOR . $SRC . DIRECTORY_SEPARATOR;
        }
        if( 0 !== strncmp( $PREFIX, $class, 25 )) {
            return false;
        }
        $class = substr( $class, 25 );
        if( false !== strpos( $class, $BS )) {
            $class = str_replace( $BS, DIRECTORY_SEPARATOR, $class );
        }
        $file = $SRCDIR . $class . $PHP;
        if( file_exists( $file )) {
            include $file;
        }
    }
);
