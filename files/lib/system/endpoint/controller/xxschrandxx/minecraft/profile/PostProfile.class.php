<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\profile;

use BadMethodCallException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use wcf\data\minecraft\MinecraftProfileEditor;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\http\Helper;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\linker\AbstractMinecraftLinker;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\linker\MinecraftLinkerParameters;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\io\HttpFactory;
use wcf\system\MCSkinPreviewAPI\SkinRendererHandler;

#[PostRequest('/xxschrandxx/minecraft/{uuid:[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}}/profile')]
class PostProfile extends AbstractMinecraftLinker
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_PROFILE_ENABLED'];

    /**
     * @inheritDoc
     */
    public bool $ignoreName = false;

    public function validateParameters($parameters)
    {
        // check online
        if ($parameters['online'] !== 1 && $parameters['online'] !== 0) {
            if (ENABLE_DEBUG_MODE) {
                throw new UserInputException('online', '\'online\' is not 1 or 0.');
            } else {
                throw new UserInputException('online');
            }
            return;
        }

        // skip image part if offline
        if ($parameters['online'] === 0) {
            return;
        }

        // check skin type
        if ($parameters['type'] !== 'CLASSIC' && $parameters['type'] !== 'SLIM') {
            if (ENABLE_DEBUG_MODE) {
                throw new UserInputException('type', 'not \'CLASSIC\' or \'SLIM\'.');
            } else {
                throw new UserInputException('type');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $parameters = Helper::mapApiParameters($this->request, PostProfileParameters::class);

        $this->validateParameters($parameters);

        if ($parameters['online'] === 1) {
            $online = true;
        } else if ($parameters['online'] === 0) {
            $online = false;
        }

        // Set MinecraftProfile
        $minecraftProfileList = new MinecraftProfileList();
        $minecraftProfileList->getConditionBuilder()->add('minecraftID = ? AND minecraftUUID = ?', [$this->minecraft->getObjectID(), $this->uuid]);
        $minecraftProfileList->readObjects();

        try {
            $minecraftProfile = $minecraftProfileList->getSingleObject();
        } catch (BadMethodCallException $e) {
            // handeled by isset
        }
        if (!isset($minecraftProfile)) {
            $minecraftProfile = MinecraftProfileEditor::create([
                'minecraftID' => $this->minecraft->getObjectID(),
                'minecraftUUID' => $this->uuid,
                'minecraftName' => $this->name
            ]);
        }

        // Online part
        $minecraftProfileEditor = new MinecraftProfileEditor($minecraftProfile);
        $minecraftProfileEditor->update([
            'online' => $online ? 1 : 0
        ]);

        // skip if user is not online
        if (!$online) {
            return new EmptyResponse(200);
        }
        // skip if url is the same
        if (isset($parameters['url']) && $minecraftProfile->url == $parameters['url'] && $minecraftProfile->imageGenerated) {
            return new EmptyResponse(200);
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
                throw new SystemException('Bad Request.', 400, 'Could not connect to texture server: ' . $e->getMessage() . '.');
            } else {
                throw new SystemException('Bad request.', 400);
            }
        }
        $rawImage = null;
        try {
            $rawImage = $response->getBody();
        } catch (RuntimeException $e) {
            if (ENABLE_DEBUG_MODE) {
                throw new SystemException('Bad Request.', 400, 'Could not connect to texture server: ' . $e->getMessage() . '.');
            } else {
                throw new SystemException('Bad request.', 400);
            }
        }

        /*
         * (Remove black line in front and back)
         */
        $skinType = $parameters['type'] == 'CLASSIC' ? 'steve' : 'alex';

        // Render face
        $rendererFace = new SkinRendererHandler();
        $renderedFace = $rendererFace->renderSkinFromResource((string) $rawImage, $skinType, 'face');
        if (!$renderedFace) {
            if (ENABLE_DEBUG_MODE) {
                throw new SystemException('Bad Request.', 400, 'Could not generate Image.');
            } else {
                throw new SystemException('Bad request.', 400);
            }
        }
        $rendererFace->writeImage($renderedFace, WCF_DIR . "images/skins/" . $parameters['uuid'] . "-FACE.png");

        // Render front
        $rendererFront = new SkinRendererHandler();
        $renderedFront = $rendererFront->renderSkinFromResource((string) $rawImage, $skinType, 'front');
        if (!$renderedFront) {
            if (ENABLE_DEBUG_MODE) {
                throw new SystemException('Bad Request.', 400, 'Could not generate Image.');
            } else {
                throw new SystemException('Bad request.', 400);
            }
        }
        $rendererFront->writeImage($renderedFront, WCF_DIR . "images/skins/" . $parameters['uuid'] . "-FRONT.png");

        $minecraftProfileEditor->update([
            'imageGenerated' => true
        ]);

        $this->response = new EmptyResponse(200);
    }
}

/** @internal */
class PostProfileParameters extends MinecraftLinkerParameters
{
    public function __construct(
        /** @var string */
        public readonly string $name,
        /** @var non-empty-bool */
        public readonly bool $online,
        /** @var non-empty-string */
        public readonly string $url,
        /** @var non-empty-string CLASSIC or SLIM */
        public readonly string $type,
    ) {
    }
}
