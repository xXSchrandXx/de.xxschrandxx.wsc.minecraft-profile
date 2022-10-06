<?php

namespace wcf\system\box;

use wcf\data\minecraft\MinecraftProfileList;
use wcf\system\WCF;

/**
 * MinecraftOnline Box class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Box
 */
class MinecraftOnlineBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    protected $conditionDefinition = 'de.xxschrandxx.wsc.minecraft-profile.MinecraftOnlineBox.condition';

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        $minecraftProfileList = new MinecraftProfileList();
        $minecraftProfileList->getConditionBuilder()->add('online = 1');
        return $minecraftProfileList;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        return WCF::getTPL()->fetch('boxMinecraftOnlineList', 'wcf', [
            'boxMinecraftProfileList' => $this->objectList->getObjects()
        ], true);
    }
}