<?php

namespace wcf\system\box;

use wcf\data\box\Box;
use wcf\data\minecraft\MinecraftProfileList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\data\user\User;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * MinecraftOnline Box class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\System\Box
 */
class MinecraftOnlineBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    protected $conditionDefinition = 'de.xxschrandxx.wsc.minecraft-profile.MinecraftOnlineBox.condition';

    /**
     * @inheritDoc
     */
    public $defaultLimit = 50;

    /**
     * Width of images
     * @var int
     */
    public $imageWidth = 32;

    /**
     * Type of images
     * @var string FACE / FRONT
     */
    public $imageType = 'FACE';

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    /**
     * @inheritDoc
     */
    public function addPipGuiFormFields(IFormDocument $form, $objectType)
    {
        parent::addPipGuiFormFields($form, $objectType);

        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('dataTabData');

        /** @var SingleSelectionFormField $objectTypeField */
        $objectTypeField = $dataContainer->getNodeById('objectType');

        $prefix = \str_replace('.', '_', $objectType) . '_';

        $dataContainer->appendChildren([
            SingleSelectionFormField::create($prefix . 'imageType')
                ->objectProperty('imageType')
                ->label('wcf.acp.box.controller.imageType')
                ->description('wcf.acp.box.controller.imageType.description')
                ->options([
                    'FACE',
                    'FRONT'
                ])
                ->addDependency(
                    ValueFormFieldDependency::create('boxType')
                        ->field($objectTypeField)
                        ->values([$objectType])
                ),
            IntegerFormField::create($prefix . 'imageWidth')
                ->objectProperty('imageWidth')
                ->label('wcf.acp.box.controller.imageWidth')
                ->description('wcf.acp.box.controller.imageWidth.description')
                ->addDependency(
                    ValueFormFieldDependency::create('boxType')
                        ->field($objectTypeField)
                        ->values([$objectType])
                )
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getPipGuiElementData(\DOMElement $element, $saveData = false)
    {
        $data = parent::getPipGuiElementData($element, $saveData);
        foreach (['imageType', 'imageWidth'] as $optionalElementName) {
            $optionalElement = $element->getElementsByTagName($optionalElementName)->item(0);
            if ($optionalElement !== null) {
                $data[$optionalElementName] = $optionalElement->nodeValue;
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function getAdditionalData()
    {
        $additionalData = parent::getAdditionalData();
        $additionalData['imageWidth'] = $this->imageWidth;
        $additionalData['imageType'] = $this->imageType;
        return $additionalData;
    }

    /**
     * @inheritDoc
     */
    public function getConditionsTemplate()
    {
        return WCF::getTPL()->fetch('boxConditions', 'wcf', [
            'boxController' => $this,
            'conditionObjectTypes' => $this->conditionObjectTypes,
            'defaultLimit' => $this->defaultLimit,
            'limit' => $this->limit,
            'maximumLimit' => $this->maximumLimit,
            'minimumLimit' => $this->minimumLimit,
            'sortField' => $this->sortField,
            'sortFieldLanguageItemPrefix' => $this->sortFieldLanguageItemPrefix,
            'sortOrder' => $this->sortOrder,
            'validSortFields' => $this->validSortFields,
            'imageType' => $this->imageType,
            'imageWidth' => $this->imageWidth
        ], true);
    }

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        return new MinecraftProfileList();
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        // Always has content.
        return true;
    }

    /**
     * @inheritDoc
     */
    public function readConditions()
    {
        if (isset($_POST['imageType'])) {
            $this->imageType = StringUtil::trim($_POST['imageType']);
        }
        if (isset($_POST['imageWidth'])) {
            $this->imageWidth = \intval($_POST['imageWidth']);
        }

        parent::readConditions();
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $minecraftUUIDToUserIDs = [];
        if ($userToMinecraftUserList->countObjects() > 0) {
            $userToMinecraftUserList->readObjects();
            $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();
            /** @var \wcf\data\user\minecraft\UserToMinecraftUser[] */
            $userToMinecraftUsers = $userToMinecraftUserList->getObjects();
            $minecraftUserList = new MinecraftUserList();
            $minecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
            if ($minecraftUserList->countObjects() > 0) {
                $minecraftUserList->readObjects();
                /** @var \wcf\data\user\minecraft\MinecraftUser[] */
                $minecraftUsers = $minecraftUserList->getObjects();
                foreach ($minecraftUsers as $minecraftUser) {
                    $minecraftUUIDToUserIDs[$minecraftUser->getMinecraftUUID()] = new User($userToMinecraftUsers[$minecraftUser->getObjectID()]->getUserID());
                }
            }
        }

        /** @var \wcf\data\minecraft\MinecraftProfile[] */
        $minecafProfiles = $this->objectList->getObjects();

        $onlineList = [];
        foreach ($minecafProfiles as $minecraftProfileID => $minecraftProfile) {
            if (!array_key_exists($minecraftProfile->getMinecraftUUID(), $onlineList)) {
                $user = null;
                if (array_key_exists($minecraftProfile->getMinecraftUUID(), $minecraftUUIDToUserIDs)) {
                    $user = $minecraftUUIDToUserIDs[$minecraftUser->getMinecraftUUID()];
                }
                $onlineList[$minecraftProfile->getMinecraftUUID()] = [
                    'user' => $user,
                    'minecraftName' => $minecraftProfile->getMinecraftName(),
                    'hasGeneratedImage' => $minecraftProfile->hasGeneratedImage()
                ];
                continue;
            }
            // Update hasImage if false
            if (!$onlineList[$minecraftProfile->getMinecraftUUID()]['hasGeneratedImage']) {
                $onlineList[$minecraftProfile->getMinecraftUUID()]['hasGeneratedImage'] = $minecraftProfile->hasGeneratedImage();
            }
            // Update user if not set
            if (!isset($onlineList[$minecraftProfile->getMinecraftUUID()]['user'])) {
                $user = null;
                if (array_key_exists($minecraftProfile->getMinecraftUUID(), $minecraftUUIDToUserIDs)) {
                    $user = $minecraftUUIDToUserIDs[$minecraftUser->getMinecraftUUID()];
                }
                $onlineList[$minecraftProfile->getMinecraftUUID()]['user'] = $user;
            }
        }

        return WCF::getTPL()->fetch('boxMinecraftOnlineList', 'wcf', [
            'boxMinecraftOnlineList' => $onlineList,
            'boxMinecraftOnlineImageType' => $this->imageType,
            'boxMinecraftOnlineImageWidth' => $this->imageWidth
        ], true);
    }

    /**
     * @inheritDoc
     */
    public function setBox(Box $box, $setConditionData = true)
    {
        parent::setBox($box, $setConditionData);

        if ($this->box->imageType) {
            $this->imageType = $this->box->imageType;
        }
        if ($this->box->imageWidth) {
            $this->imageWidth = \intval($this->box->imageWidth);
        }

        if ($setConditionData) {
            if ($this->box->imageType) {
                $this->imageType = $this->box->imageType;
            }
            if ($this->box->imageWidth) {
                $this->imageWidth = $this->box->imageWidth;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateConditions()
    {
        if ($this->imageType !== 'FACE' && $this->imageType !== 'FRONT') {
            throw new UserInputException('imageType', 'invalidImageType');
        }
        if ($this->imageWidth < 1) {
            throw new UserInputException('imageWidth', 'greaterThan');
        }

        parent::validateConditions();
    }

    /**
     * @inheritDoc
     */
    public function writePipGuiEntry(\DOMElement $element, IFormDocument $form)
    {
        parent::writePipGuiEntry($element, $form);

        $data = $form->getData()['data'];

        $content = $element->getElementsByTagName('content')->item(0);

        foreach (['imageType' => 'FACE', 'imageWidth' => 32] as $field => $defaultValue) {
            if (isset($data[$field]) && $data[$field] !== $defaultValue) {
                $newElement = $element->ownerDocument->createElement($field, (string)$data[$field]);

                if ($content !== null) {
                    $element->insertBefore($newElement, $content);
                } else {
                    $element->appendChild($newElement);
                }
            }
        }
    }
}
