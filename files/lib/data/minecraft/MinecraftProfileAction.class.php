<?php

namespace wcf\data\minecraft;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;

/**
 * MinecraftProfile Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\Minecraft
 */
class MinecraftProfileAction extends AbstractDatabaseObjectAction implements IToggleAction
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

    /**
     * @inheritDoc
     */
    public function validateToggle()
    {
        parent::validateUpdate();
    }

    /**
     * @inheritDoc
     */
    public function toggle()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $object) {
            if ($object->online) {
                $object->update(['online' => 0]);
            } else {
                $object->update(['online' => 1]);
            }
        }
    }
}
