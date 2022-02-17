<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;
use wcf\data\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\system\api\outadoc\MCSkinPreviewAPI\SkinRenderer;
use wcf\system\exception\SystemException;
use wcf\system\io\HttpFactory;
use wcf\system\SingletonFactory;
use wcf\util\JSON;

class MinecraftProfileHandler extends SingletonFactory
{

    /** @var int */
    protected $minecraftID;

    /** @var Minecraft */
    protected $minecraft;

    /**
     * Baut die Klasse auf
     */
    public function init()
    {
        if (MINECRAFT_PROFILE_IDENTITY) {
            $this->minecraftID = MINECRAFT_PROFILE_IDENTITY;
        }

        if (empty($this->minecraftID)) {
            return;
        }

        $this->minecraft = new Minecraft($this->minecraftID);
    }

    public function getOfflineSkinURL($name) {
        $args = [
            'type' => 'skin',
            'content' => [
                'name' => $name
            ]
        ];
        $jsonSting = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonSting);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if ($result['Response'] != 0) {
            return ['error' => true, 'message' => 'Response not for commands.'];
        }
        $response = [];
        try {
            $response = JSON::decode($result['CMD']);
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if (empty($response)) {
            return ['error' => true, 'message' => 'Resonse empty'];
        }
        if ($response['error']) {
            return $response;
        }
        return $response['message'];
    }

    /**
     * Downloads offline Skindata and saves modified picture.
     * @param MinecraftUser $minecraftUser
     * @return string|false path to picture
     */
    public function loadOfflineMinecraftSkin($minecraftUser) {
        $skindata64 = $this->getOfflineSkinURL($minecraftUser->minecraftName);
        if (array_key_exists('error', $skindata64)) {
            return false;
        }
        $skindata = \base64_decode($skindata64['value']);
        $jsonSkindata = JSON::decode($skindata);
        $client = HttpFactory::getDefaultClient();
        $response2 = $client->request('GET', $jsonSkindata['textures']['SKIN']['url']);

        return $this->saveModifyMinecraftSkin($response2->getBody()->getContents(), $minecraftUser->minecraftUUID);
    }

    /**
     * Downloads online Skindata and saves modified picture.
     * @param MinecraftUser $minecraftUser
     * @return string path to picture
     */
    public function loadOnlineMinecraftSkin($minecraftUser) {
        $client = HttpFactory::getDefaultClient();
        $response1 = $client->request('GET', 'https://sessionserver.mojang.com/session/minecraft/profile/' . \str_replace('-', '', strtolower($minecraftUser->minecraftUUID)));
        $jsonResponse1 = JSON::decode($response1->getBody()->getContents());
        $skindata = \base64_decode($jsonResponse1['properties'][0]['value']);
        $jsonSkindata = JSON::decode($skindata);
        $response2 = $client->request('GET', $jsonSkindata['textures']['SKIN']['url']);

        return $this->saveModifyMinecraftSkin($response2->getBody()->getContents(), $minecraftUser->minecraftUUID);
    }

    /**
     * Modifies and saves Skindata from given url.
     * @param string|resource $resource
     * @param string $uuid
     * @return string path to picture
     */
    public function saveModifyMinecraftSkin($resource, $uuid) {
        $renderer = new SkinRenderer(85, WCF_DIR . 'lib/system/api/outadoc/MCSkinPreviewAPI/char.png');
        $path = 'images/skins/' . $uuid . '.png';
        \imagepng($renderer->renderSkinFromResource(imagecreatefromstring($resource)), WCF_DIR . $path);
        return $path;
    }
}
