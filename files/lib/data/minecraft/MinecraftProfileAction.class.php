<?php

namespace wcf\data\minecraft;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * MinecraftProfile Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\Minecraft
 */
class MinecraftProfileAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = MinecraftProfileEditor::class;
}
