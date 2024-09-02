<?php

namespace wcf\system\condition\minecraft;

use wcf\data\DatabaseObjectList;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\system\condition\AbstractSelectCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;
use wcf\util\StringUtil;

/**
 * MinecraftImageType PropertyCondition class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftImageTypeCondition extends AbstractSelectCondition implements IObjectListCondition
{
    /**
     * @inheritDoc
     */
    protected $fieldName = 'imageType';

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.acp.box.controller.imageType';

    /**
     * @inheritDoc
     */
    protected $fieldValue = 'FACE';

    /**
     * @inheritDoc
     */
    protected function getOptions()
    {
        return [
            'FACE' => 'wcf.acp.box.controller.imageType.FACE',
            'FRONT' => 'wcf.acp.box.controller.imageType.FRONT',
        ];
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        if (isset($_POST[$this->fieldName])) {
            $this->fieldValue = StringUtil::trim($_POST[$this->fieldName]);
        }
    }

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof MinecraftProfileList)) {
            throw new InvalidObjectArgument($objectList, MinecraftProfileList::class, 'Object list');
        }
    }
}
