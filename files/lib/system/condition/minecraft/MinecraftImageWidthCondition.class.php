<?php

namespace wcf\system\condition\minecraft;

use wcf\data\condition\Condition;
use wcf\data\DatabaseObjectList;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\system\condition\AbstractSingleFieldCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\exception\UserInputException;

/**
 * ImageType PropertyCondition class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftImageWidthCondition extends AbstractSingleFieldCondition implements IObjectListCondition
{
    /**
     * property field name
     */
    protected $fieldName = 'imageWidth';

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.acp.box.controller.imageWidth';

    /**
     * property value has to be greater than the given value
     * @var int
     */
    protected $minWidth = 0;

    /**
     * property value
     * @var int
     */
    protected $imageWidth = 32;

    /**
     * @inheritDoc
     */
    public function getData()
    {
        if ($this->imageWidth === null) {
            return null;
        }
        $data = [];
        $data['imageWidth'] = $this->imageWidth;
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        if (
            isset($_POST[$this->fieldName])
            && \strlen($_POST[$this->fieldName])
        ) {
            $this->imageWidth = \intval($_POST[$this->fieldName]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getFieldElement()
    {
        return <<<HTML
<input type="number" name="{$this->fieldName}" value="{$this->imageWidth}" min="{$this->minWidth}" class="medium">
HTML;
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->imageWidth = 32;
    }

    /**
     * @inheritDoc
     */
    public function setData(Condition $condition)
    {
        $this->imageWidth = $condition->imageWidth;
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        if ($this->minWidth > $this->getDecoratedObject()->imageWidth) {
            throw new UserInputException($this->fieldName, 'minValue');
        }
    }

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
