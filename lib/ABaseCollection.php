<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Fi1a\Collection\AbstractInstanceCollection;

/**
 * Абстрактный класс коллекций вкладок и полей
 */
abstract class ABaseCollection extends AbstractInstanceCollection implements IBaseCollection
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this as $object) {
            $array[] = $object->getArrayCopy();
        }

        return $array;
    }
}
