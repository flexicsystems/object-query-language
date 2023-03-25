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

use Flexic\ObjectQueryLanguage\Object\Output;
use Flexic\ObjectQueryLanguage\Object\QueryObject;
use Flexic\ObjectQueryLanguage\Query\QueryToken;
use Flexic\ObjectQueryLanguage\Query\QueryTokenStream;

abstract class Handler
{
    protected function query(
        object $object,
        QueryTokenStream $tokenStream,
    ): Output {
        $queryObjects = $this->generateQueryObject(
            $object,
            $tokenStream->current(),
        );
        $isArrayStream = false;

        while (null !== $tokenStream->current()) {
            $token = $tokenStream->current();

            if ($token->isArray()) {
                $isArrayStream = true;
            }

            $results = [];

            foreach ($queryObjects as $queryObject) {
                $results[] = $queryObject->query();
            }

            if ($tokenStream->isLast()) {
                break;
            }

            $queryObjects = [];

            foreach ($results as $result) {
                $nextQueryObject = $this->generateQueryObject(
                    $result,
                    $tokenStream->next(),
                );

                if (null === $nextQueryObject) {
                    continue;
                }

                foreach ($nextQueryObject as $item) {
                    $queryObjects[] = $item;
                }
            }

            $tokenStream->iterate();
        }

        return new Output(
            $queryObjects,
            $isArrayStream,
        );
    }

    private function generateQueryObject(
        mixed $object,
        QueryToken $token,
    ): ?array {
        if (\is_array($object)) {
            $objectsToQuery = [];

            foreach ($object as $item) {
                if (!\is_object($item)) {
                    continue;
                }

                $objectsToQuery[] = new QueryObject(
                    new \ReflectionObject($item),
                    $token->getTarget(),
                    $item,
                );
            }

            return $objectsToQuery;
        }

        if (\is_object($object)) {
            return [
                new QueryObject(
                    new \ReflectionObject($object),
                    $token->getTarget(),
                    $object,
                ),
            ];
        }

        return null;
    }
}
