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

use Flexic\ObjectQueryLanguage\Object as QueryObject;
use Flexic\ObjectQueryLanguage\Query;

final class Reader extends Handler
{
    public function read(
        object $object,
        Query\Query $query,
    ): mixed {
        $stream = $this->query(
            $object,
            $query->getStream(),
        );

        if (null === $stream) {
            return null;
        }

        $entries = $stream->getProperties();

        if ($stream->isArray()) {
            return \array_map(static function (QueryObject\QueryObject $property) {
                return $property->getValue();
            }, $entries);
        }

        if (\count($entries) === 0) {
            return null;
        }

        return $stream->getProperties()[\array_key_first($entries)]->getValue();
    }
}
