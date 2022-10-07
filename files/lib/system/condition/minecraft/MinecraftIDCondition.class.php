<?php

namespace wcf\system\condition\minecraft;

use wcf\data\DatabaseObjectList;
use wcf\data\minecraft\MinecraftList;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\system\condition\AbstractMultiSelectCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\WCF;

/**
 * MinecraftID PropertyCondition class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftIDCondition extends AbstractMultiSelectCondition implements IObjectListCondition
{
    /**
     * @inheritDoc
     */
    protected $description = 'wcf.global.multiSelect';

    /**
     * @inheritDoc
     */
    protected $fieldName = 'minecraftID';

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.minecraft.MinecraftOnlineBox.condition.minecraftID';

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof MinecraftProfileList)) {
            throw new InvalidObjectArgument($objectList, MinecraftProfileList::class, 'Object list');
        }

        $objectList->sqlSelects = 'DISTINCT minecraftUUID';

        $objectList->getConditionBuilder()->add(
            $this->fieldName . ' IN (?)',
            [$conditionData[$this->fieldName]]
        );
        $objectList->getConditionBuilder()->add(
            'online = 1'
        );
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        $minecraftList = new MinecraftList();
        $minecraftList->readObjects();
        /** @var \wcf\data\minecraft\Minecraft[] */
        $minecrafts = $minecraftList->getObjects();

        $options = [];
        foreach ($minecrafts as $minecraft) {
            $options[$minecraft->getObjectID()] = $minecraft->getTitle();
        }

        return $options;
    }
}
