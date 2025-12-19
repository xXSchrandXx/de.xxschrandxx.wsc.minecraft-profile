<?php

namespace wcf\data\minecraft;

use wcf\data\DatabaseObject;

/**
 * MinecraftProfile Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\Minecraft
 *
 * @property-read int $profileID
 * @property-read int $minecraftID
 * @property-read string $minecraftUUID
 * @property-read string $minecraftName
 * @property-read string $url
 * @property-read boolean $imageGenerated
 * @property-read boolean $online
 */
class MinecraftProfile extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'minecraft_profile';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'profileID';

    /**
     * Returns Minecraft ID
     * @return ?int
     */
    public function getMinecraftID()
    {
        return $this->minecraftID;
    }

    /**
     * Returns Minecraft-UUID
     * @return ?string
     */
    public function getMinecraftUUID()
    {
        return $this->minecraftUUID;
    }

    /**
     * Returns Minecraft-Name
     * @return ?string
     */
    public function getMinecraftName()
    {
        return $this->minecraftName;
    }

    /**
     * Returns url
     * @return ?string
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * Weather image is generated
     * @return ?bool
     */
    public function hasGeneratedImage()
    {
        return $this->imageGenerated;
    }

    /**
     * Weather user is online
     * @return ?bool
     */
    public function isOnline()
    {
        return $this->online;
    }

    /**
     * Weather user is offline
     * @return ?bool
     */
    public function isOffline()
    {
        return !$this->online;
    }
}
