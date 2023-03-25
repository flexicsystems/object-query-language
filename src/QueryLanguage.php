<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\ObjectQueryLanguage;

final class QueryLanguage
{
    private Handler\Reader $reader;

    private Handler\Writer $writer;

    public function __construct()
    {
        $this->reader = new Handler\Reader();
        $this->writer = new Handler\Writer();
    }

    public function get(object $object, string $query): mixed
    {
        return $this->reader->read(
            $object,
            QueryParser::parse($query),
        );
    }

    public function set(object $object, string $query, mixed $value): void
    {
        $this->writer->write(
            $object,
            QueryParser::parse($query),
            $value,
        );
    }
}
