<?php

namespace wcf\system\menu\user\profile\content;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\system\io\HttpFactory;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\Url;

class MinecraftUserProfileMenuContent extends SingletonFactory implements IUserProfileMenuContent
{
    public function getContent($userID)
    {
        $minecrafts = [];

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('userID = ?', [$userID]);
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();

        $client = HttpFactory::getDefaultClient();
        foreach ($minecraftUsers as $i => $minecraftUser) {
            // TODO switch to ingame name
            $minecrafts[$i]['title'] = $minecraftUser->title;
            $minecrafts[$i]['uuid'] = $minecraftUser->minecraftUUID;
            // TODO add url and size change
            // TODO Ingameskin?
            $minecrafts[$i]['img'] = 'https://minotar.net/armor/bust/' . \str_replace('-', '', strtolower($minecraftUser->minecraftUUID) . "/100.png");
            // TODO add stats
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
