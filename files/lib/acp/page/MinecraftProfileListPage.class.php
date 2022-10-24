<?php

namespace wcf\acp\page;

use wcf\data\minecraft\MinecraftProfile;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\page\MultipleLinkPage;

/**
 * MinecraftProfileList Page class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Page
 */
class MinecraftProfileListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $objectListClassName = MinecraftProfileList::class;

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftProfileList';

    /**
     * @inheritDoc
     */
    public function __run()
    {
        $this->sortField = MinecraftProfile::getDatabaseTableIndexName();
        parent::__run();
    }
}
