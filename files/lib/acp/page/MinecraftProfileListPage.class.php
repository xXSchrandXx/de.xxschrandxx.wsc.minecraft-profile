<?php

namespace wcf\acp\page;

use wcf\data\minecraft\Minecraft;
use wcf\data\minecraft\MinecraftList;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\page\SortablePage;
use wcf\system\WCF;

/**
 * MinecraftProfileList Page class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Page
 */
class MinecraftProfileListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $objectListClassName = MinecraftProfileList::class;

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftProfileList';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    public $validSortFields = [
        'profileID',
        'minecraftID',
        'minecraftUUID',
        'minecraftName',
        'imageGenerated',
        'online'
    ];

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'profileID';

    /**
     * Filtered minecraftID
     * @param int
     */
    public $minecraftID = 0;

    /**
     * Filtered minecraftTitle
     */
    public $minecraftTitle = '';

    /**
     * Filterable Minecrafts
     * @param MinecraftList
     */
    public $minecraftList;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // read minecraftID parameter
        if (isset($_REQUEST['minecraftID'])) {
            $minecraft = new Minecraft(intval($_REQUEST['minecraftID']));
            if ($minecraft->minecraftID) {
                $this->minecraftID = $minecraft->getObjectID();
                $this->minecraftTitle = $minecraft->getTitle();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        $this->minecraftList = new MinecraftList();
        $this->minecraftList->readObjects();
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if (isset($this->minecraftID) && $this->minecraftID) {
            $this->objectList->getConditionBuilder()->add('minecraftID = ?', [$this->minecraftID]);
        }

    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'minecraftList' => $this->minecraftList,
            'minecraftID' => $this->minecraftID,
            'minecraftTitle' => $this->minecraftTitle
        ]);
    }
}
