<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class SearchHelper
{
    public static function sequentialSearch(Collection $items, string $keyword, array $fields): Collection
    {
        $needle = strtolower(trim($keyword));

        if ($needle === '') {
            return $items;
        }

        return $items->filter(function ($item) use ($needle, $fields) {
            foreach ($fields as $field) {
                $value = strtolower((string) data_get($item, $field, ''));
                if ($value === $needle) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    public static function boyerMooreContains(string $text, string $pattern): bool
    {
        $text = strtolower($text);
        $pattern = strtolower($pattern);

        $n = strlen($text);
        $m = strlen($pattern);

        if ($m === 0) {
            return true;
        }

        if ($m > $n) {
            return false;
        }

        $badChar = [];
        for ($i = 0; $i < $m; $i++) {
            $badChar[$pattern[$i]] = $i;
        }

        $shift = 0;

        while ($shift <= ($n - $m)) {
            $j = $m - 1;

            while ($j >= 0 && $pattern[$j] === $text[$shift + $j]) {
                $j--;
            }

            if ($j < 0) {
                return true;
            }

            $badCharIndex = $badChar[$text[$shift + $j]] ?? -1;
            $shift += max(1, $j - $badCharIndex);
        }

        return false;
    }

    public static function boyerMooreSearch(Collection $items, string $keyword, array $fields): Collection
    {
        $needle = trim($keyword);

        if ($needle === '') {
            return $items;
        }

        return $items->filter(function ($item) use ($needle, $fields) {
            foreach ($fields as $field) {
                $value = (string) data_get($item, $field, '');
                if ($value !== '' && self::boyerMooreContains($value, $needle)) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    public static function rankedSearch(Collection $items, string $keyword, array $fields): Collection
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return $items;
        }

        $exactMatches = self::sequentialSearch($items, $keyword, $fields);
        $partialMatches = self::boyerMooreSearch($items, $keyword, $fields)
            ->reject(fn ($item) => $exactMatches->containsStrict($item));

        return $exactMatches->concat($partialMatches)->values();
    }
}