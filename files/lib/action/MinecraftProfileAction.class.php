<?php

namespace wcf\action;

use BadMethodCallException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Laminas\Diactoros\Response\JsonResponse;
use RuntimeException;
use wcf\data\minecraft\MinecraftProfileEditor;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\system\io\HttpFactory;
use wcf\system\MCSkinPreviewAPI\SkinRendererHandler;

/**
 * MinecraftProfile action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class MinecraftProfileAction extends AbstractMinecraftLinkerAction
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_PROFILE_ENABLED'];

    /**
     * @inheritDoc
     */
    public bool $ignoreName = false;

    /**
     * @inheritDoc
     */
    public function validateParameters($parameters, &$response): void
    {
        parent::validateParameters($parameters, $response);
        if ($response instanceof JsonResponse) {
            return;
        }

        // check online
        if (!array_key_exists('online', $parameters)) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. Missing \'online\'.', 400);
            } else {
                $response = $this->send('Bad request.', 400);
            }
            return;
        }
        if ($parameters['online'] !== 1 && $parameters['online'] !== 1) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'online\' is not 1 or 0.', 400);
            } else {
                $response = $this->send('Bad request.', 400);
            }
            return;
        }

        // skip image part if offline
        if ($parameters['online'] === 0) {
            return;
        }

        // check image url
        if (!array_key_exists('url', $parameters)) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. Missing \'url\'.', 400);
            } else {
                $response = $this->send('Bad request.', 400);
            }
            return;
        }

    }

    /**
     * @inheritDoc
     */
    public function execute($parameters): JsonResponse
    {
        if ($parameters['online'] === 1) {
            $online = true;
        } else if ($parameters['online'] === 0) {
            $online = false;
        }

        // Set MinecraftProfile
        $minecraftProfileList = new MinecraftProfileList();
        $minecraftProfileList->getConditionBuilder()->add('minecraftID = ? AND minecraftUUID = ?', [$parameters['minecraftID'], $parameters['uuid']]);
        $minecraftProfileList->readObjects();

        try {
            $minecraftProfile = $minecraftProfileList->getSingleObject();
        } catch (BadMethodCallException $e) {
            // do nothing
        }
        if (!isset($minecraftProfile)) {
            $minecraftProfile = MinecraftProfileEditor::create([
                'minecraftID' => $parameters['minecraftID'],
                'minecraftUUID' => $parameters['uuid'],
                'minecraftName' => $parameters['name']
            ]);
        }

        // Online part
        $minecraftProfileEditor = new MinecraftProfileEditor($minecraftProfile);
        $minecraftProfileEditor->update([
            'online' => $online ? 1 : 0
        ]);

        // skip if user is not online
        if (!$online) {
            return $this->send();
        }
        // skip if url is the same
        if (isset($parameters['url']) && $minecraftProfile->url == $parameters['url'] && $minecraftProfile->imageGenerated) {
            return $this->send();
        }

        $minecraftProfileEditor->update([
            'url' => $parameters['url']
        ]);

        // Image part
        $client = HttpFactory::makeClient();

        $request = new Request('GET', $parameters['url']);

        $response = null;
        try {
            $response = $client->send($request);
        } catch (GuzzleException $e) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not connect to mojang texture server: ' . $e->getMessage() . '.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }
        $rawImage = null;
        try {
            $rawImage = $response->getBody();
        } catch (RuntimeException $e) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not read mojang texture server response: ' . $e->getMessage() . '.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }

        /*
         * TODO Check if steve or alex
         * (Remove black line in front and back)
         */
        $skinType = 'steve';

        // Render face
        $rendererFace = new SkinRendererHandler();
        $renderedFace = $rendererFace->renderSkinFromResource((string) $rawImage, $skinType, 'face');
        if (!$renderedFace) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not generate Image.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }
        $rendererFace->writeImage($renderedFace, WCF_DIR . "images/skins/" . $parameters['uuid'] . "-FACE.png");

        // Render front
        $rendererFront = new SkinRendererHandler();
        $renderedFront = $rendererFront->renderSkinFromResource((string) $rawImage, $skinType, 'front');
        if (!$renderedFront) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not generate Image.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }
        $rendererFront->writeImage($renderedFront, WCF_DIR . "images/skins/" . $parameters['uuid'] . "-FRONT.png");

        $minecraftProfileEditor->update([
            'imageGenerated' => true
        ]);

        return $this->send();
    }
}
