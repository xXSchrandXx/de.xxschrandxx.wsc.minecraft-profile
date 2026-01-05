<?php

namespace wcf\system\event\listener;

use wcf\data\minecraft\MinecraftProfile;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

class MinecraftProfileAvatarEditFormListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param \wcf\form\AvatarEditForm $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (empty(MINECRAFT_PROFILE_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('user.profile.avatar.canUseMinecraftProfile')) {
            return;
        }
        $this->{$eventName}($eventObj);
    }

    /**
     * @param \wcf\form\AvatarEditForm $eventObj
     */
    public function validate($eventObj)
    {
        if (str_starts_with($eventObj->avatarType, 'minecraftProfile')) {
            $minecraftProfileID = intval(str_replace('minecraftProfile', '', $eventObj->avatarType));
            $minecraftProfile = new MinecraftProfile($minecraftProfileID);
            if (!$minecraftProfile->profileID) {
                throw new UserInputException($eventObj->avatarType);
            }

            $eventObj->additionalFields['minecraftProfileAvatarID'] = $minecraftProfile->profileID;
        } else {
            $eventObj->additionalFields['minecraftProfileAvatarID'] = null;
        }
    }

    /**
     * @param \wcf\form\AvatarEditForm $eventObj
     */
    public function saved($eventObj)
    {
        if (!isset($eventObj->additionalFields['minecraftProfileAvatarID'])) {
            return;
        }
        // validation always sets avatarType to none if it's not custom.
        // This is to re set the avatar for saved page
        $eventObj->avatarType = 'minecraftProfile' . $eventObj->additionalFields['minecraftProfileAvatarID'];
    }

    /**
     * @param \wcf\form\AvatarEditForm $eventObj
     */
    public function readData($eventObj)
    {
        if (!empty($_POST)) {
            return;
        }
        $minecraftProfileID = WCF::getUser()->minecraftProfileAvatarID;
        if (isset($minecraftProfileID) && $minecraftProfileID) {
            $eventObj->avatarType = 'minecraftProfile' . $minecraftProfileID;
        }
    }

    /**
     * @param \wcf\form\AvatarEditForm $eventObj
     */
    public function assignVariables($eventObj)
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->getObjectID()]);
        $userToMinecraftUserList->readObjectIDs();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();
        if (empty($userToMinecraftUserIDs)) {
            return;
        }

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->setObjectIDs($userToMinecraftUserIDs);
        $minecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\MinecraftUser[] */
        $minecraftUsers = $minecraftUserList->getObjects();
        $minecraftUUIDs = [];
        foreach ($minecraftUsers as $minecraftUser) {
            if (in_array($minecraftUser->getMinecraftUUID(), $minecraftUUIDs)) {
                continue;
            }
            $minecraftUUIDs[] = $minecraftUser->getMinecraftUUID();
        }
        if (empty($minecraftUUIDs)) {
            return;
        }

        $minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_PROFILE_IDENTITY));
        $minecraftProfileList = new MinecraftProfileList();
        $minecraftProfileList->getConditionBuilder()->add('minecraftUUID IN (?) AND minecraftID IN (?)', [$minecraftUUIDs, $minecraftIDs]);
        $minecraftProfileList->readObjects();
        /** @var \wcf\data\minecraft\MinecraftProfile[] */
        $minecraftProfiles = $minecraftProfileList->getObjects();

        WCF::getTPL()->assign('minecraftProfiles', $minecraftProfiles);
    }
}
