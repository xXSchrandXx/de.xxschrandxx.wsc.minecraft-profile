<?php

namespace wcf\system\menu\user\profile\content;

use wcf\data\minecraft\MinecraftList;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

class MinecraftProfileUserContent extends SingletonFactory implements IUserProfileMenuContent
{
    /**
     * @inheritDoc
     */
    public function getContent($userID)
    {
        $minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_PROFILE_IDENTITY));
        $minecraftList = new MinecraftList();
        $minecraftList->setObjectIDs($minecraftIDs);
        $minecraftList->readObjects();
        /** @var \wcf\data\minecraft\Minecraft[] */
        $minecrafts = $minecraftList->getObjects();

        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$userID]);
        $userToMinecraftUserList->readObjectIDs();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

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

        $minecraftProfileList = new MinecraftProfileList();
        $minecraftProfileList->getConditionBuilder()->add('minecraftID IN (?) AND minecraftUUID IN (?)', [$minecraftIDs, $minecraftUUIDs]);
        $minecraftProfileList->readObjects();
        /** @var \wcf\data\minecraft\MinecraftProfile[] */
        $minecraftProfiles = $minecraftProfileList->getObjects();

        $minecraftProfilesByMinecraft = [];
        foreach ($minecraftProfiles as $minecraftProfile) {
            $minecraftProfilesByMinecraft[$minecraftProfile->getMinecraftID()][$minecraftProfile->getObjectID()] = $minecraftProfile;
        }

        WCF::getTPL()->assign([
            'minecrafts' => $minecrafts,
            'minecraftProfilesByMinecraft' => $minecraftProfilesByMinecraft
        ]);

        return WCF::getTPL()->fetch('userProfileMinecraftProfiles');
    }

    /**
     * @inheritDoc
     */
    public function isVisible($userID)
    {
        return true;
    }
}
