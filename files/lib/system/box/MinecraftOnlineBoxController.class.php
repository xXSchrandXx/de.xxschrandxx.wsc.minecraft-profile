<?php

namespace wcf\system\box;

use wcf\data\box\Box;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\data\user\User;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * MinecraftOnline Box class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Box
 */
class MinecraftOnlineBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    protected $conditionDefinition = 'de.xxschrandxx.wsc.minecraft-profile.MinecraftOnlineBox.condition';

    /**
     * @inheritDoc
     */
    public $defaultLimit = 50;

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        return new MinecraftProfileList();
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        // Always has content.
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $minecraftUUIDToUserIDs = [];
        if ($userToMinecraftUserList->countObjects() > 0) {
            $userToMinecraftUserList->readObjects();
            $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();
            /** @var \wcf\data\user\minecraft\UserToMinecraftUser[] */
            $userToMinecraftUsers = $userToMinecraftUserList->getObjects();
            $minecraftUserList = new MinecraftUserList();
            $minecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
            if ($minecraftUserList->countObjects() > 0) {
                $minecraftUserList->readObjects();
                /** @var \wcf\data\user\minecraft\MinecraftUser[] */
                $minecraftUsers = $minecraftUserList->getObjects();
                foreach ($minecraftUsers as $minecraftUser) {
                    $minecraftUUIDToUserIDs[$minecraftUser->getMinecraftUUID()] = new User($userToMinecraftUsers[$minecraftUser->getObjectID()]->getUserID());
                }
            }
        }

        /** @var \wcf\data\minecraft\MinecraftProfile[] */
        $minecafProfiles = $this->objectList->getObjects();

        $onlineList = [];
        foreach ($minecafProfiles as $minecraftProfileID => $minecraftProfile) {
            if (!array_key_exists($minecraftProfile->getMinecraftUUID(), $onlineList)) {
                $user = null;
                if (array_key_exists($minecraftProfile->getMinecraftUUID(), $minecraftUUIDToUserIDs)) {
                    $user = $minecraftUUIDToUserIDs[$minecraftUser->getMinecraftUUID()];
                }
                $onlineList[$minecraftProfile->getMinecraftUUID()] = [
                    'user' => $user,
                    'minecraftName' => $minecraftProfile->getMinecraftName(),
                    'hasGeneratedImage' => $minecraftProfile->hasGeneratedImage()
                ];
                continue;
            }
            // Update hasImage if false
            if (!$onlineList[$minecraftProfile->getMinecraftUUID()]['hasGeneratedImage']) {
                $onlineList[$minecraftProfile->getMinecraftUUID()]['hasGeneratedImage'] = $minecraftProfile->hasGeneratedImage();
            }
            // Update user if not set
            if (!isset($onlineList[$minecraftProfile->getMinecraftUUID()]['user'])) {
                $user = null;
                if (array_key_exists($minecraftProfile->getMinecraftUUID(), $minecraftUUIDToUserIDs)) {
                    $user = $minecraftUUIDToUserIDs[$minecraftUser->getMinecraftUUID()];
                }
                $onlineList[$minecraftProfile->getMinecraftUUID()]['user'] = $user;
            }
        }

        // default values
        $imageType = 'FACE';
        $imageWidth = 32;

        foreach ($this->box->getControllerConditions() as $condition) {
            if (
                $condition->getObjectType()->objectType === 'de.xxschrandxx.wsc.minecraft-profile.imageType'
            ) {
                $imageType = $condition->imageType;
            } else if (
                $condition->getObjectType()->objectType === 'de.xxschrandxx.wsc.minecraft-profile.imageWidth'
            ) {
                $imageWidth = $condition->imageWidth;
            }
        }

        return WCF::getTPL()->fetch('boxMinecraftOnlineList', 'wcf', [
            'boxMinecraftOnlineList' => $onlineList,
            'boxMinecraftOnlineImageType' => $imageType,
            'boxMinecraftOnlineImageWidth' => $imageWidth
        ], true);
    }
}
