<?php

namespace wcf\system\event\listener;

use wcf\data\minecraft\MinecraftProfile;
use wcf\data\user\avatar\MinecraftProfileAvatar;
use wcf\system\WCF;

class MinecraftProfileGetAvatarListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param \wcf\data\user\UserProfile $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (empty(MINECRAFT_PROFILE_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('user.profile.avatar.canUseMinecraftProfile')) {
            return;
        }
        if (!isset($eventObj->minecraftProfileAvatarID) && !$eventObj->minecraftProfileAvatarID) {
            return;
        }

        $minecraftProfile = new MinecraftProfile($eventObj->minecraftProfileAvatarID);

        if (!$minecraftProfile->profileID) {
            return;
        }

        $parameters['avatar'] = new MinecraftProfileAvatar($minecraftProfile->getMinecraftUUID());
    }
}
