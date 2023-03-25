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

use Flexic\ObjectQueryLanguage\Exception\QueryParserException;

final class QueryParser
{
    public function __construct(
        readonly private string $query,
    ) {
    }

    public function doParse(): Query\Query
    {
        $parts = $this->getQueryParts();

        return new Query\Query(
            new Query\QueryTokenStream(
                \array_map(function (string $part): Query\QueryToken {
                    return $this->tokenize(
                        $part,
                    );
                }, $parts),
            ),
        );
    }

    public static function parse(string $query): Query\Query
    {
        return (new self($query))->doParse();
    }

    private function getQueryParts(): array
    {
        return \explode('.', $this->query);
    }

    private function tokenize(string $part): Query\QueryToken
    {
        \preg_match('/((.*)(\[([0-9\,\- ]+)?]))/m', $part, $partInformation);

        $isArray = \count($partInformation) > 0;
        $hasIndex = \array_key_exists(4, $partInformation);

        return new Query\QueryToken(
            $isArray ? $partInformation[2] : $part,
            $isArray,
            $hasIndex ? $this->getIndex($partInformation[4]) : [new Query\QueryIterationIndex(0, null)],
        );
    }

    /**
     * @return array<int, Query\QueryIterationIndex>
     */
    private function getIndex(string $index): array
    {
        return \array_map(static function (string $index): Query\QueryIterationIndex {
            if (!\str_contains($index, '-')) {
                return new Query\QueryIterationIndex(
                    (int) $index,
                    (int) $index,
                );
            }

            [$start, $end] = \explode('-', $index);

            $start = '' === $start ? null : (int) $start;
            $end = '' === $end ? null : (int) $end;

            if (\is_int($start) && \is_int($end) && $start > $end) {
                throw new QueryParserException('Start index must be smaller than end index');
            }

            return new Query\QueryIterationIndex(
                $start,
                $end,
            );
        }, \explode(',', \str_replace(' ', '', $index)));
    }
}
