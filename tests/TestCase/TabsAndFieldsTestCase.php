<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings\TestCase;

use Fi1a\UserSettings\Field;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\Tab;
use Fi1a\UserSettings\TabMapper;

/**
 * Тест-кейс с созданными табами и полями
 */
class TabsAndFieldsTestCase extends ModuleTestCase
{
    /**
     * @var int[]
     */
    protected static $tabIds = [];

    /**
     * @var int[]
     */
    protected static $fieldIds = [];

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $tab = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB1',
            'LOCALIZATION' => null,
        ]);
        $result = $tab->add();
        if (!$result->isSuccess()) {
            throw new \ErrorException();
        }
        self::$tabIds['FUS_TEST_TAB1'] = $result->getId();
        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_FIELD1',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $result = $field->save();
        if (!$result->isSuccess()) {
            throw new \ErrorException();
        }
        self::$fieldIds['UF_FUS_TEST_FIELD1'] = $result->getId();
        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_FIELD2',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $result = $field->save();
        if (!$result->isSuccess()) {
            throw new \ErrorException();
        }
        self::$fieldIds['UF_FUS_TEST_FIELD2'] = $result->getId();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        foreach (self::$fieldIds as $fieldId) {
            FieldMapper::getById($fieldId)->delete();
        }
        foreach (self::$tabIds as $tabId) {
            TabMapper::getById($tabId)->delete();
        }
        parent::tearDownAfterClass();
    }
}
