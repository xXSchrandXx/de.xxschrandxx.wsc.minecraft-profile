<?php

namespace wcf\system\condition\minecraft;

use wcf\data\DatabaseObjectList;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\system\condition\AbstractIntegerCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;

/**
 * ImageType PropertyCondition class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftImageWidthCondition extends AbstractIntegerCondition implements IObjectListCondition
{
    /**
     * @inheritDoc
     */
    protected $fieldName = 'imageWidth';

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.acp.box.controller.imageWidth';

    /**
     * @inheritDoc
     */
    protected $minValue = 0;

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof MinecraftProfileList)) {
            throw new InvalidObjectArgument($objectList, ViewableThreadList::class, 'Object list');
        }
    }
}
