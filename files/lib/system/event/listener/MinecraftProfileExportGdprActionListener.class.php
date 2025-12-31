<?php

namespace wcf\system\event\listener;

use Laminas\Diactoros\Response;
use wcf\acp\action\UserExportGdprAction;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use ZipStream\ZipStream;

class MinecraftProfileExportGdprActionListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param UserExportGdprAction $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $user = $eventObj->user;

        $minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_PROFILE_IDENTITY));

        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$user->userID]);
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

        $minecraftProfileData = [];
        foreach ($minecraftProfiles as $minecraftProfile) {
            $minecraftProfileData[] = [
                'minecraftID' => $minecraftProfile->getMinecraftID(),
                'uuid' => $minecraftProfile->getMinecraftUUID(),
                'name' => $minecraftProfile->getMinecraftName(),
                'url' => $minecraftProfile->getURL(),
                'imageGenerated' => $minecraftProfile->hasGeneratedImage(),
                'online' => $minecraftProfile->isOnline()
            ];
        }

        $eventObj->data['de.xxschrandxx.wsc.minecraft-profile'] = $minecraftProfileData;
    }
}
