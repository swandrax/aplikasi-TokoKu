<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Helper to build the bad character table for Boyer-Moore matching.
     */
    private function badCharHeuristic(string $pattern, int $size): array
    {
        $badchar = array_fill(0, 256, -1);
        for ($i = 0; $i < $size; $i++) {
            $char = ord($pattern[$i]);
            if ($char < 256) {
                $badchar[$char] = $i;
            }
        }
        return $badchar;
    }

    /**
     * Boyer-Moore Bad Character Heuristic Algorithm.
     * Case-insensitive, returns an array of matched text index positions.
     */
    public function boyerMoore(string $text, string $pattern): array
    {
        $text = strtolower(trim($text));
        $pattern = strtolower(trim($pattern));
        
        $m = strlen($pattern);
        $n = strlen($text);

        if ($m === 0 || $n === 0 || $m > $n) {
            return [];
        }

        $badchar = $this->badCharHeuristic($pattern, $m);
        $s = 0; // Shift of the pattern with respect to text
        $matches = [];

        while ($s <= ($n - $m)) {
            $j = $m - 1;

            // Keep reducing index j while characters of pattern and text are matching
            while ($j >= 0 && $pattern[$j] === $text[$s + $j]) {
                $j--;
            }

            // If the pattern is present at current shift, then index j will become -1
            if ($j < 0) {
                $matches[] = $s;
                
                // Shift the pattern so that the next character in text aligns with its last occurrence in pattern.
                if ($s + $m < $n) {
                    $nextChar = ord($text[$s + $m]);
                    $badcharVal = ($nextChar < 256) ? $badchar[$nextChar] : -1;
                    $s += $m - $badcharVal;
                } else {
                    $s += 1;
                }
            } else {
                // Shift the pattern so that the bad character in text aligns with its last occurrence in pattern.
                $badChar = ord($text[$s + $j]);
                $badcharVal = ($badChar < 256) ? $badchar[$badChar] : -1;
                $s += max(1, $j - $badcharVal);
            }
        }

        return $matches;
    }

    /**
     * Sequential Search (Linear Search filter).
     * Iterates over a dataset and filters items based on a callback predicate.
     */
    public function sequentialSearch(array $items, callable $predicate): array
    {
        $results = [];
        foreach ($items as $item) {
            if ($predicate($item)) {
                $results[] = $item;
            }
        }
        return $results;
    }

    /**
     * Hybrid Search:
     * 1. Uses Boyer-Moore to scan and match names.
     * 2. Uses Sequential Search to filter by Category ID and Price range.
     * 3. Highlighting: Injects HTML bold marks around matches.
     */
    public function hybridProductSearch(
        string $keyword,
        ?int $categoryId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): Collection {
        // Fetch all active products
        $allProducts = Product::query()->where('is_active', true)->with('category')->get()->all();

        // 1. Boyer-Moore Text Filter on product name
        $bmFiltered = [];
        if (!empty($keyword)) {
            foreach ($allProducts as $product) {
                $matches = $this->boyerMoore($product->name, $keyword);
                if (!empty($matches)) {
                    // Highlight matching parts
                    $highlightedName = $this->highlightMatch($product->name, $matches, strlen($keyword));
                    $product->highlighted_name = $highlightedName;
                    $bmFiltered[] = $product;
                }
            }
        } else {
            foreach ($allProducts as $product) {
                $product->highlighted_name = $product->name;
            }
            $bmFiltered = $allProducts;
        }

        // 2. Sequential Filter by Category & Price bounds
        $finalFiltered = $this->sequentialSearch($bmFiltered, function ($product) use ($categoryId, $minPrice, $maxPrice) {
            if ($categoryId && $product->category_id != $categoryId) {
                return false;
            }
            if ($minPrice !== null && $product->price_sell < $minPrice) {
                return false;
            }
            if ($maxPrice !== null && $product->price_sell > $maxPrice) {
                return false;
            }
            return true;
        });

        return collect($finalFiltered);
    }

    /**
     * Injects <mark> HTML tags inside a matched string to highlight search hits.
     */
    private function highlightMatch(string $original, array $indices, int $length): string
    {
        $result = '';
        $lastEnd = 0;
        
        // Sort indices ascending to build the string from left to right
        sort($indices);
        
        foreach ($indices as $index) {
            if ($index < $lastEnd) {
                continue; // Prevent overlapping matches
            }
            
            // Safe escaped text before the match
            $prefix = substr($original, $lastEnd, $index - $lastEnd);
            $result .= e($prefix);
            
            // Safe highlighted match
            $matchedPart = substr($original, $index, $length);
            $result .= "<mark class='bg-yellow-200 text-yellow-900 font-semibold px-0.5 rounded'>" . e($matchedPart) . "</mark>";
            
            $lastEnd = $index + $length;
        }
        
        // Add any remaining escaped text
        if ($lastEnd < strlen($original)) {
            $result .= e(substr($original, $lastEnd));
        }
        
        return $result;
    }
}
