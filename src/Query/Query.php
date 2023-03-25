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

final class Query
{
    public function __construct(
        readonly private QueryTokenStream $tokenStream,
    ) {
    }

    public function getStream(): QueryTokenStream
    {
        return $this->tokenStream;
    }
}
