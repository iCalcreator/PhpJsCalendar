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

use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Traits\TitleTrait;

final class Link extends BaseDto
{
    /**
     * A URI [RFC3986] from which the resource may be fetched, mandatory
     *
     * @var string
     */
    private string $href;

    /**
     * MUST be a valid "content-id" value according to the definition of Section 2 of [RFC2392], optional
     *
     * The value MUST be unique within this Link object but has no meaning beyond that.
     * It MAY be different from the link id for this Link object.
     *
     * Here, AUTO set to uuid
     *
     * @var string
     */
    private string $cid;

    /**
     * The media type [RFC6838] of the resource, if known, optional
     *
     * @var string|null
     */
    private ? string $contentType = null;

    /**
     * The size, in octets, of the resource when fully decoded, optional
     *
     * I.e., the number of octets in the file the user would download), if known.
     * Note that this is an informational estimate, and implementations must be prepared to handle
     * the actual size being quite different when the resource is fetched.
     *
     * @var int|null UnsignedInt
     */
    private ? int $size = null;

    /**
     * Identifies the relation of the linked resource to the object, optional
     *
     * If set, the value MUST be a relation type from the IANA "Link Relations" registry [LINKRELS], as established in [RFC8288].
     *
     * Links with a rel of "enclosure" MUST be considered by the client to be attachments for download.
     * Links with a rel of "describedby" MUST be considered by the client to be alternative representations of the description.
     * Links with a rel of "icon" MUST be considered by the client to be images that it may use when presenting
     *   the calendar data to a user. The "display" property may be set to indicate the purpose of this image.
     *
     * @var string|null
     * @see https://www.iana.org/assignments/link-relations
     */
    private ? string $rel = null;

    /**
     * Describes the intended purpose of a link to an image,optional
     *
     * If set, the "rel" property MUST be set to "icon".
     * Value
     *    "badge":  an image meant to be displayed alongside the title of the object
     *    "graphic":  a full image replacement for the object itself
     *    "fullsize":  an image that is used to enhance the object
     *    "thumbnail":  a smaller variant of "fullsize" to be used when space for the image is constrained
     * OR another value registered in the IANA "JSCalendar Enum Values" registry
     * OR or a vendor-specific value
     *
     * @var string|null
     */
    private ? string $display = null;

    use TitleTrait;

    /**
     * Class constructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::LINK;
        $this->setCid( self::getNewUid());
    }

    /**
     * Class factory method
     *
     * @param string $href
     * @return static
     */
    public static function factoryHref( string $href ) : Link
    {
        return ( new self())->setHref( $href );
    }

    /**
     * @return string
     */
    public function getHref() : string
    {
        return $this->href;
    }

    /**
     * Return bool true if href is not null
     *
     * @return bool
     */
    public function isHrefSet() : bool
    {
        return ( null !== $this->href );
    }

    /**
     * @param string $href
     * @return static
     */
    public function setHref( string $href ) : Link
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return string
     */
    public function getCid() : string
    {
        return $this->cid;
    }

    /**
     * @param string $cid
     * @return static
     */
    public function setCid( string $cid ) : Link
    {
        $this->cid = $cid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentType() : ?string
    {
        return $this->contentType;
    }

    /**
     * Return bool true if contentType is not null
     *
     * @return bool
     */
    public function isContentTypeSet() : bool
    {
        return ( null !== $this->contentType );
    }

    /**
     * @param string $contentType
     * @return static
     */
    public function setContentType( string $contentType ) : Link
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize() : ? int
    {
        return $this->size;
    }

    /**
     * Return bool true if size is not null
     *
     * @return bool
     */
    public function isSizeSet() : bool
    {
        return ( null !== $this->size );
    }

    /**
     * @param int $size
     * @return static
     */
    public function setSize( int $size ) : Link
    {
        self::assertUnsignedInt( $size, self::SIZE );
        $this->size = $size;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRel() : ?string
    {
        return $this->rel;
    }

    /**
     * Return bool true if rel is not null
     *
     * @return bool
     */
    public function isRelSet() : bool
    {
        return ( null !== $this->rel );
    }

    /**
     * @param string $rel
     * @return static
     */
    public function setRel( string $rel ) : Link
    {
        $this->rel = $rel;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisplay() : ?string
    {
        return $this->display;
    }

    /**
     * Return bool true if display is not null
     *
     * @return bool
     */
    public function isDisplaySet() : bool
    {
        return ( null !== $this->display );
    }

    /**
     * @param string $display
     * @return static
     */
    public function setDisplay( string $display ) : Link
    {
        $this->display = $display;
        return $this;
    }
}
