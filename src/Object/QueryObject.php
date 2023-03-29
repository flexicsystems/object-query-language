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
            $type instanceof \ReflectionNamedType
            && $type->isBuiltin()
        ) {
            $value = $this->convert(
                $type->getName(),
                $value,
            );
        }

        $property = $this->reflection->getProperty($this->propertyName);
        $propertyName = $property->getName();
        $methods = \get_class_methods($this->object);

        if (\in_array(\sprintf('set%s', \ucfirst($propertyName)), $methods, true)) {
            $this->object->{'set' . \ucfirst($propertyName)}($value);
        } else {
            $this->reflection->getProperty($this->propertyName)->setValue(
                $this->object,
                $value,
            );
        }
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

        $property = $this->reflection->getProperty($this->propertyName);
        $propertyName = $property->getName();
        $methods = \get_class_methods($this->object);

        if (\in_array(\sprintf('set%s', \ucfirst($propertyName)), $methods, true)) {
            $this->object->{'set' . \ucfirst($propertyName)}(
                $classReflection->newInstance()
            );
        } else {
            $this->reflection->getProperty($this->propertyName)->setValue(
                $this->object,
                $classReflection->newInstance(),
            );
        }

        return true;
    }

    private function convert(string $type, mixed $input): mixed
    {
        if ('int' === $type) {
            return (int) $input;
        }

        if ('float' === $type) {
            return (float) $input;
        }

        if ('bool' === $type) {
            if ('false' === $input || 0 === $input || '0' === $input) {
                return false;
            }

            if ('true' === $input || 1 === $input || '1' === $input) {
                return true;
            }

            return (bool) $input;
        }

        if ('string' === $type) {
            return (string) $input;
        }

        if ('array' === $type) {
            return (array) $input;
        }

        if ('object' === $type) {
            return (object) $input;
        }

        return $input;
    }
}
