<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\EventResult;
use Fi1a\Unit\UserSettings\TestCase\TabsAndFieldsTestCase;
use Fi1a\UserSettings\Exceptions\OptionGetValueException;
use Fi1a\UserSettings\Option;

/**
 * Тестирование класса реализующего работу со значениями пользовательских настроек
 */
class OptionTest extends TabsAndFieldsTestCase
{
    /**
     * Тестирование метода  getAll
     */
    public function testGetAll(): void
    {
        $values = Option::getInstance()->getAll();
        $this->assertIsArray($values);
        $this->assertCount(2, $values);
    }

    /**
     * Тестирование методов set и get
     */
    public function testSetGet(): void
    {
        $option = Option::getInstance();
        $this->assertFalse($option->get('UF_FUS_TEST_FIELD1'));
        $option->clearCache();
        $this->assertTrue($option->set('UF_FUS_TEST_FIELD1', 'value')->isSuccess());
        $this->assertEquals('value', $option->get('UF_FUS_TEST_FIELD1'));
    }

    /**
     * Тестирование исключения при возврате ошибки обработчиком события OnOptionGet
     */
    public function testGetEventTypeError(): void
    {
        $option = Option::getInstance();
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnOptionGet',
            function (Event $event) {
                $result = new EventResult();
                $result->addError(new EntityError('UF_FUS_TEST_BEFORE_GET'));

                return $result;
            }
        );
        $this->expectException(OptionGetValueException::class);
        try {
            $option->get('UF_FUS_TEST_FIELD1');
        } catch (OptionGetValueException $exception) {
            EventManager::getInstance()->removeEventHandler(
                self::MODULE_ID,
                'OnOptionGet',
                $eventHandlerKey
            );

            throw $exception;
        }
    }

    /**
     * Изменение значения из обработчика события OnOptionGet
     */
    public function testGetEventModify(): void
    {
        $option = Option::getInstance();
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnOptionGet',
            function (Event $event) {
                $result = new EventResult();
                $result->modifyFields(['value' => 'modify']);

                return $result;
            }
        );
        $this->assertEquals('modify', $option->get('UF_FUS_TEST_FIELD1'));
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnOptionGet',
            $eventHandlerKey
        );
    }

    /**
     * Ошибка при попытке установить не существующую пользовательскую настройку
     */
    public function testSetUnknownOption(): void
    {
        $option = Option::getInstance();
        $this->assertFalse($option->set('UNKNOWN', 123)->isSuccess());
    }

    /**
     * Ошибка при попытке установить пустое значение для обязательного поля
     */
    public function testSetValidationError(): void
    {
        $option = Option::getInstance();
        $this->assertFalse($option->set('UF_FUS_TEST_FIELD1', null)->isSuccess());
    }

    /**
     * Ошибка получения значения при исключении \Throwable
     */
    public function testThrowableExceptionOnSet(): void
    {
        $option = Option::getInstance();
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeOptionSet',
            function (Event $event) {
                throw new \ErrorException();
            }
        );
        $this->assertFalse($option->set('UF_FUS_TEST_FIELD1', 'value')->isSuccess());
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeOptionSet',
            $eventHandlerKey
        );
    }

    /**
     * Изменение значения из обработчика события OnBeforeOptionSet
     */
    public function testSetEventModify(): void
    {
        $option = Option::getInstance();
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeOptionSet',
            function (Event $event) {
                $result = new EventResult();
                $result->modifyFields(['UF_FUS_TEST_FIELD1' => 'modify']);

                return $result;
            }
        );
        $option->set('UF_FUS_TEST_FIELD1', 'value');
        $this->assertEquals('modify', $option->get('UF_FUS_TEST_FIELD1'));
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeOptionSet',
            $eventHandlerKey
        );
    }

    /**
     * Тестирование исключения при возврате ошибки обработчиком события OnOptionGet
     */
    public function testSetEventTypeError(): void
    {
        $option = Option::getInstance();
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeOptionSet',
            function (Event $event) {
                $result = new EventResult();
                $result->addError(new EntityError('UF_FUS_TEST_BEFORE_SET'));

                return $result;
            }
        );
        $this->assertFalse($option->set('UF_FUS_TEST_FIELD1', 'value')->isSuccess());
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeOptionSet',
            $eventHandlerKey
        );
    }
}
