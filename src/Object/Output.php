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

final class Output
{
    public function __construct(
        readonly private array $properties,
        readonly private bool $isArray = false,
    ) {
    }

    /**
     * @return QueryObject[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }
}
