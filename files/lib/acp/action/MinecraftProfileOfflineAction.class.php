<?php

namespace wcf\acp\action;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\acp\page\MinecraftProfileListPage;
use wcf\action\AbstractAction;
use wcf\data\minecraft\MinecraftProfileAction;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\system\request\LinkHandler;

class MinecraftProfileOfflineAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.minecraft.canManageConnection'];

    /**
     * Filter for Minecraft
     * @param int
     */
    public $minecraftID;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // read minecraftID parameter
        if (isset($_REQUEST['minecraftID'])) {
            $this->minecraftID = \intval($_REQUEST['minecraftID']);
        }
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        parent::execute();

        $minecraftProfileList = new MinecraftProfileList();
        if (isset($this->minecraftID) && $this->minecraftID) {
            $minecraftProfileList->getConditionBuilder()->add('minecraftID = ?', [$this->minecraftID]);
        }
        $minecraftProfileList->readObjects();
        $minecraftProfiles = $minecraftProfileList->getObjects();
        $minecraftProfileAction = new MinecraftProfileAction($minecraftProfiles, 'update', [
            'data' => [
                'online' => 0
            ]
        ]);
        $minecraftProfileAction->executeAction();

        return new RedirectResponse(
            LinkHandler::getInstance()->getControllerLink(MinecraftProfileListPage::class)
        );
    }
}
