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
 * MinecraftMaxPlayers PropertyCondition class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftMaxPlayersCondition extends AbstractSingleFieldCondition implements IObjectListCondition
{
    /**
     * property field name
     */
    protected $fieldName = 'maxPlayers';

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.acp.box.controller.maxPlayers';

    /**
     * @inheritDoc
     */
    protected $description = 'wcf.acp.box.controller.maxPlayers.description';

    /**
     * property value has to be greater than the given value
     * @var int
     */
    protected $minMaxPlayers = 0;

    /**
     * property value
     * @var int
     */
    protected $maxPlayers = 100;

    /**
     * @inheritDoc
     */
    public function getData()
    {
        if ($this->maxPlayers === null) {
            return null;
        }
        $data = [];
        $data['maxPlayers'] = $this->maxPlayers;
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
            $this->maxPlayers = \intval($_POST[$this->fieldName]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getFieldElement()
    {
        return <<<HTML
<input type="number" name="{$this->fieldName}" value="{$this->maxPlayers}" min="{$this->minMaxPlayers}" class="medium">
HTML;
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->maxPlayers = 32;
    }

    /**
     * @inheritDoc
     */
    public function setData(Condition $condition)
    {
        $this->maxPlayers = $condition->maxPlayers;
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        if ($this->minMaxPlayers > $this->getDecoratedObject()->maxPlayers) {
            throw new UserInputException($this->fieldName, 'minValue');
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
