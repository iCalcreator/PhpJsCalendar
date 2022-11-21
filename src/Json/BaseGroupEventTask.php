<?php

declare( strict_types = 1 );
namespace Kigkonsult\PhpJsCalendar\Json;

use Exception;
use Kigkonsult\PhpJsCalendar\Dto\Group as GroupDto;
use Kigkonsult\PhpJsCalendar\Dto\Event as EventDto;
use Kigkonsult\PhpJsCalendar\Dto\Task  as TaskDto;
use stdClass;

abstract class BaseGroupEventTask extends BaseJson
{
    /**
     * Parse json array for common properties to update Group|Event|Task
     *
     * @param string[]|string[][] $jsonArray
     * @param GroupDto|EventDto|TaskDto $dto
     * @throws Exception
     */
    protected static function groupEventTaskParse( array $jsonArray, GroupDto|EventDto|TaskDto $dto ) : void
    {
        if( isset( $jsonArray[self::UID] )) {
            $dto->setUid( $jsonArray[self::UID] );
        }
        if( isset( $jsonArray[self::TITLE] )) {
            $dto->setTitle( $jsonArray[self::TITLE] );
        }
        if( isset( $jsonArray[self::CATEGORIES] )) {
            foreach( $jsonArray[self::CATEGORIES] as $category => $bool ) {
                $dto->addCategory( $category, self::jsonBool2Php( $bool ));
            }
        }
        if( isset( $jsonArray[self::COLOR] )) {
            $dto->setColor( $jsonArray[self::COLOR] );
        }
        if( isset( $jsonArray[self::CREATED] )) {
            $dto->setCreated( $jsonArray[self::CREATED] );
        }
        if( isset( $jsonArray[self::LINKS] )) {
            foreach( $jsonArray[self::LINKS] as $lid => $link ) {
                $dto->addLink( $lid, Link::parse( $lid, $link ));
            }
        }
        if( isset( $jsonArray[self::KEYWORDS] )) {
            foreach( $jsonArray[self::KEYWORDS] as $keyWord => $bool ) {
                $dto->addKeyword( $keyWord, self::jsonBool2Php( $bool ));
            }
        }
        if( isset( $jsonArray[self::LOCALE] )) {
            $dto->setLocale( $jsonArray[self::LOCALE] );
        }
        if( isset( $jsonArray[self::UPDATED] )) {
            $dto->setUpdated( $jsonArray[self::UPDATED] );
        }
    }

    /**
     * Write Group|Event|Task common Dto properties to json array
     *
     * @param GroupDto|EventDto|TaskDto $dto
     * @param string[]|string[][] $jsonArray
     */
    protected static function groupEventTaskWrite( GroupDto|EventDto|TaskDto $dto, array & $jsonArray ) : void
    {
        $jsonArray[self::PRODID] = $dto->getProdId();
        $jsonArray[self::UID] = $dto->getUid();
        if($dto->isCreatedSet()) {
            $jsonArray[self::CREATED] = $dto->getCreated();
        }
        if( $dto->isUpdatedSet()) {
            $jsonArray[self::UPDATED] = $dto->getUpdated();
        }
        if( $dto->isTitleSet()) {
            $jsonArray[self::TITLE] = $dto->getTitle();
        }
        if( $dto->isLocaleSet()) {
            $jsonArray[self::LOCALE] = $dto->getLocale();
        }
        if( $dto->isColorSet()) {
            $jsonArray[self::COLOR] = $dto->getColor();
        }
        // array of "String[Boolean]"
        if( ! empty( $dto->getCategoriesCount())) {
            foreach( $dto->getCategories() as $category => $bool ) {
                $jsonArray[self::CATEGORIES][$category] = $bool;
            }
        }
        // array of "Id[Link]"
        if( ! empty( $dto->getLinksCount())) {
            $jsonArray[self::LINKS] = new stdClass();
            foreach( $dto->getLinks() as $lid => $link ) {
                $jsonArray[self::LINKS]->{$lid} = (object) Link::write( $lid, $link );
            }
        }
        // array of "String[Boolean]"
        if( ! empty( $dto->getKeywordsCount())) {
            foreach( $dto->getKeywords() as $keyWord => $bool ) {
                $jsonArray[self::KEYWORDS][$keyWord] = $bool;
            }
        }
    }
}
