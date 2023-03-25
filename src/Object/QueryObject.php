<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\ObjectQueryLanguage\Object;

final class QueryObject
{
    public function __construct(
        readonly private \ReflectionObject $reflection,
        readonly private string $propertyName,
        readonly private object $object,
    ) {
    }

    public function query(): mixed
    {
        if (!$this->reflection->hasProperty($this->propertyName)) {
            return null;
        }

        if (!$this->isInitialized()) {
            return null;
        }

        return $this->reflection->getProperty($this->propertyName)->getValue(
            $this->object,
        );
    }

    public function getValue(): mixed
    {
        if (!$this->reflection->hasProperty($this->propertyName)) {
            return null;
        }

        if (!$this->isInitialized()) {
            return null;
        }

        return $this->reflection->getProperty($this->propertyName)->getValue(
            $this->object,
        );
    }

    public function isInitialized(): bool
    {
        return $this->reflection->getProperty($this->propertyName)->isInitialized($this->object);
    }

    public function setValue(mixed $value): void
    {
        $this->reflection->getProperty($this->propertyName)->setValue(
            $this->object,
            $value,
        );
    }
}
