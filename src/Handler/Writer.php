<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Flexic-Systems
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 *
 * @version 1.0.0
 */

namespace Flexic\ObjectQueryLanguage\Handler;

use Flexic\ObjectQueryLanguage\Query;

final class Writer extends Handler
{
    public function write(
        object $object,
        Query\Query $query,
        mixed $value,
    ): void {
        $stream = $this->query(
            $object,
            $query->getStream(),
        );

        if (null === $stream) {
            return;
        }

        foreach ($stream->getProperties() as $property) {
            $property->setValue(
                $value,
            );
        }
    }
}
