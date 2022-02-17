<?php

namespace wcf\system\menu\user\profile\content;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\system\minecraft\MinecraftProfileHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

class MinecraftUserProfileMenuContent extends SingletonFactory implements IUserProfileMenuContent
{

    public function getContent($userID)
    {
        $mph = MinecraftProfileHandler::getInstance();

        $minecrafts = [];

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('userID = ?', [$userID]);
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();

        foreach ($minecraftUsers as $i => $minecraftUser) {
            $minecrafts[$i]['title'] = $minecraftUser->title;
            $minecrafts[$i]['uuid'] = $minecraftUser->minecraftUUID;
            $minecrafts[$i]['name'] = $minecraftUser->minecraftName;
            if (MINECRAFT_PROFILE_ONLINEMODE) {
                $minecrafts[$i]['img'] = $mph->loadOnlineMinecraftSkin($minecraftUser);
            } else {
                $minecrafts[$i]['img'] = $mph->loadOfflineMinecraftSkin($minecraftUser);
            }
            if ($minecrafts[$i]['img'] === false) {
                $minecrafts[$i]['img'] = 'images/skins/default.png';
            }
        }

        return WCF::getTPL()->fetch('userProfileMinecraft', 'wcf', [
            'userID' => $userID,
            'minecrafts' => $minecrafts
        ]);
    }

    public function isVisible($userID)
    {
        if (MINECRAFT_PROFILE_ENABLED && MINECRAFT_LINKER_ENABLED) {
            $user = new User($userID);
            if ($user == null) {
                return false;
            } else if (WCF::getUser()->hasAdministrativeAccess()) {
                return true;
            } else {
                return $user->getUserOption('minecraft_profile_enabled');
            }
        } else {
            return false;
        }
    }
}
