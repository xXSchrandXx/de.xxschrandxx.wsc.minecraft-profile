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

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    public function delete()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->objects as $object) {
            if (!$object->hasGeneratedImage()) {
                continue;
            }

            // Delete Face
            @unlink(WCF_DIR . "images/skins/" . $object->getMinecraftUUID() . "-FACE.png");
            // Delete Front
            @unlink(WCF_DIR . "images/skins/" . $object->getMinecraftUUID() . "-FRONT.png");
        }

        return parent::delete();
    }
}
