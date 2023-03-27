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
            if (!$this->initialize()) {
                return null;
            }
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
        $type = $this->reflection->getProperty($this->propertyName)->getType();

        if (
            $type instanceof \ReflectionNamedType &&
            $type->isBuiltin()
        ) {
            $value = $this->convert(
                $type->getName(),
                $value
            );
        }

        $this->reflection->getProperty($this->propertyName)->setValue(
            $this->object,
            $value,
        );
    }

    private function initialize(): bool
    {
        $type = $this->reflection->getProperty($this->propertyName)->getType();

        if ((!$type instanceof \ReflectionNamedType) || $type->isBuiltin()) {
            return false;
        }

        if (!\class_exists($type->getName())) {
            return false;
        }

        $classReflection = new \ReflectionClass($type->getName());

        if ($classReflection->getConstructor() !== null && $classReflection->getConstructor()->getNumberOfRequiredParameters() > 0) {
            return false;
        }

        $this->reflection->getProperty($this->propertyName)->setValue(
            $this->object,
            $classReflection->newInstance(),
        );

        return true;
    }

    private function convert(string $type, mixed $input): mixed
    {
        if ($type === 'int') {
            return (int) $input;
        }

        if ($type === 'float') {
            return (float) $input;
        }

        if ($type === 'bool') {
            if ('false' === $input || 0 === $input || '0' === $input) {
                return false;
            }

            if ('true' === $input || 1 === $input || '1' === $input) {
                return true;
            }

            return (bool) $input;
        }

        if ($type === 'string') {
            return (string) $input;
        }

        if ($type === 'array') {
            return (array) $input;
        }

        if ($type === 'object') {
            return (object) $input;
        }

        return $input;
    }
}
