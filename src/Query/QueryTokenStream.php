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

final class QueryTokenStream
{
    private int $index;

    public function __construct(
        readonly private array $tokens,
    ) {
        $this->index = 0;
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function current(): ?QueryToken
    {
        if (!\array_key_exists($this->index, $this->tokens)) {
            return null;
        }

        return $this->tokens[$this->index];
    }

    public function next(): ?QueryToken
    {
        if (!\array_key_exists($this->index + 1, $this->tokens)) {
            return null;
        }

        return $this->tokens[$this->index + 1];
    }

    public function isLast(): bool
    {
        return !\array_key_exists($this->index + 1, $this->tokens);
    }

    public function iterate(): void
    {
        ++$this->index;
    }
}
