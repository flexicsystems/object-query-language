<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\ObjectQueryLanguage\Query;

final class QueryToken
{
    /**
     * @param array<QueryIterationIndex> $index
     */
    public function __construct(
        readonly private string $target,
        readonly private bool $isArray = false,
        readonly private array $index = [],
    ) {
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }

    public function getIndex(): array
    {
        return $this->index;
    }

    public function hasIndex(): bool
    {
        return \count($this->index) > 0;
    }
}
