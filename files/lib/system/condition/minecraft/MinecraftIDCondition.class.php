<?php
namespace wcf\system\condition\minecraft;

use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\minecraft\MinecraftList;
use wcf\data\minecraft\MinecraftProfile;
use wcf\system\condition\AbstractSelectCondition;
use wcf\system\condition\IObjectCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\WCF;

/**
 * MinecraftID PropertyCondition class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftIDCondition extends AbstractSelectCondition implements
    IObjectCondition,
    IObjectListCondition
{
    /**
     * @inheritDoc
     */
    protected $fieldName = 'minecraftID';

    /**
     * name of the relevant database object class
     * @var string
     */
    protected $className = MinecraftProfile::class;

    /**
     * name of the relevant object property
     * @var string
     */
    protected $propertyName = 'minecraftID';

    /**
     * @inheritDoc
     */
    public function getHTML()
    {
        if (empty($this->getOptions())) {
            return '<p class="info">' . WCF::getLanguage()->get('wcf.global.noItems') . '</p>';
        }

        return parent::getHTML();
    }

    /**
     * @inheritDoc
     */
    protected function getOptions()
    {
        $minecraftList = new MinecraftList();
        $minecraftList->sqlOrderBy = 'title ASC';
        $minecraftList->readObjects();
        /** @var \wcf\data\minecraft\Minecraft */
        $minecrafts = $minecraftList->getObjects();

        $options = [];
        foreach ($minecrafts as $minecraftID => $minecraft) {
            $options[$minecraftID] = $minecraft->getTitle();
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        $className = $this->getListClassName();
        if (!($objectList instanceof $className)) {
            throw new InvalidObjectArgument($objectList, $className, 'Object list');
        }

        $objectList->getConditionBuilder()->add(
            $objectList->getDatabaseTableAlias() . '.' . $this->getPropertyName() . ' = ?',
            [$conditionData[$this->fieldName]]
        );
    }

    /**
     * @inheritDoc
     */
    public function checkObject(DatabaseObject $object, array $conditionData)
    {
        $className = $this->getClassName();
        if (!($object instanceof $className)) {
            throw new InvalidObjectArgument($object, $className);
        }

        return \in_array($object->{$this->getPropertyName()}, $conditionData[$this->fieldName]);
    }

    /**
     * Returns the name of the relevant database object class.
     *
     * @return  string
     */
    protected function getClassName()
    {
        return $this->className;
    }

    /**
     * Returns the name of the relevant database object list class.
     *
     * @return  string
     */
    protected function getListClassName()
    {
        return $this->className . 'List';
    }

    /**
     * Returns the name of the relevant object property.
     *
     * @return  string
     */
    protected function getPropertyName()
    {
        return $this->propertyName;
    }
}
