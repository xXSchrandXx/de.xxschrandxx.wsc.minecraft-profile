<?php

namespace wcf\data\user\avatar;

use wcf\data\minecraft\MinecraftProfile;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * MinecraftProfile Avatar class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\Minecraft
 */
class MinecraftProfileAvatar implements IUserAvatar, ISafeFormatAvatar
{
    /**
     * image size
     * @var int
     */
    public $size = UserAvatar::AVATAR_SIZE;

    /**
     * @var string
     */
    protected $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @inheritDoc
     */
	public function getURL($size = null)
	{
        return WCF::getPath() . "images/skins/" . $this->uuid . "-FACE.png";
	}

    /**
     * @inheritDoc
     */
	public function getImageTag($size = null)
	{
        return '<img src="' . StringUtil::encodeHTML($this->getURL($size)) . '" width="' . $size . '" height="' . $size . '" alt="" class="userAvatarImage">';
	}

    /**
     * @inheritDoc
     */
	public function getWidth()
	{
		return $this->size;
	}

    /**
     * @inheritDoc
     */
	public function getHeight()
	{
		return $this->size;
	}

    /**
     * @inheritDoc
     */
    public function getSafeURL(?int $size = null): string
    {
        return WCF::getPath() . "images/skins/default-FACE.png";
    }

    /**
     * @inheritDoc
     */
    public function getSafeImageTag(?int $size = null): string
    {
        return '<img src="' . StringUtil::encodeHTML($this->getSafeURL($size)) . '" width="' . $size . '" height="' . $size . '" alt="" class="userAvatarImage">';
    }
}
