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
    protected bool $ignoreName = false;

    /**
     * Weather user is online
     */
    public bool $online = false;

    /**
     * Url for action
     */
    public $url;

    /**
     * @var \wcf\data\minecraft\MinecraftProfile
     */
    public $minecraftProfile;

    /**
     * @inheritDoc
     */
    public function readParameters(): ?JsonResponse
    {
        $result = parent::readParameters();

        if ($result !== null) {
            return $result;
        }

        // check online
        if (!array_key_exists('online', $this->getJSON())) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'online\'.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }

        if ($this->getData('online') === 1) {
            $this->online = true;
        } else if ($this->getData('online') === 0) {
            $this->online = false;
        } else {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'online\' is not 1 or 0.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }

        // skip image part if offline
        if (!$this->online) {
            return $result;
        }

        // check image url
        if (!array_key_exists('url', $this->getJSON())) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'url\'.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }

        $this->url = $this->getData('url');

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ?JsonResponse
    {
        parent::execute();

        // Set MinecraftProfile
        $minecraftProfileList = new MinecraftProfileList();
        $minecraftProfileList->getConditionBuilder()->add('minecraftID = ? AND minecraftUUID = ?', [$this->minecraft->minecraftID, $this->uuid]);
        $minecraftProfileList->readObjects();

        try {
            $this->minecraftProfile = $minecraftProfileList->getSingleObject();
        } catch (BadMethodCallException $e) {
            // do nothing
        }
        if (!isset($this->minecraftProfile)) {
            $this->minecraftProfile = MinecraftProfileEditor::create([
                'minecraftID' => $this->minecraft->minecraftID,
                'minecraftUUID' => $this->uuid,
                'minecraftName' => $this->name
            ]);
        }

        // Online part
        $minecraftProfileEditor = new MinecraftProfileEditor($this->minecraftProfile);
        $minecraftProfileEditor->update([
            'online' => $this->online ? 1 : 0
        ]);

        // skip if user is not online
        if (!$this->online) {
            return $this->send();
        }
        // skip if url is the same
        if (isset($this->url) && $this->minecraftProfile->url == $this->url && !$this->minecraftProfile->imageGenerated) {
            return $this->send();
        }

        $minecraftProfileEditor->update([
            'url' => $this->url
        ]);

        // Image part
        $client = HttpFactory::makeClient();

        $request = new Request('GET', $this->url);

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
            $rawImage = $response->getBody()->getContents();
        } catch (RuntimeException $e) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not read mojang texture server response: ' . $e->getMessage() . '.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }

        // TODO Check if steve or alex
        $skinType = 'steve';

        // Render face
        $rendererFace = new SkinRendererHandler();
        $renderedFace = $rendererFace->renderSkinFromResource($rawImage, $skinType, 'face');
        if (!$renderedFace) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not generate Image.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }
        $rendererFace->writeImage($renderedFace, "images/skins/" . $this->uuid . "-FACE.png");

        // Render front
        $rendererFront = new SkinRendererHandler();
        $renderedFront = $rendererFront->renderSkinFromResource($rawImage, $skinType, 'front');
        if (!$renderedFront) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not generate Image.', 400);
            } else {
                return $this->send('Bad request.', 400);
            }
        }
        $rendererFront->writeImage($renderedFront, "images/skins/" . $this->uuid . "-FRONT.png");

        $minecraftProfileEditor->update([
            'imageGenerated' => true
        ]);

        return $this->send();
    }
}
