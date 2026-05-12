<?php

namespace Tests\Unit;

use App\Helpers\SearchHelper;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class SearchHelperTest extends TestCase
{
    public function test_sequential_search_finds_exact_match(): void
    {
        $items = new Collection([
            (object) ['nama' => 'Brownies', 'kode' => 'BRN-001'],
            (object) ['nama' => 'Combro', 'kode' => 'COM-002'],
        ]);

        $result = SearchHelper::sequentialSearch($items, 'Combro', ['nama']);

        $this->assertCount(1, $result);
        $this->assertSame('Combro', $result->first()->nama);
    }

    public function test_boyer_moore_contains_detects_partial_pattern(): void
    {
        $this->assertTrue(SearchHelper::boyerMooreContains('Kue Brownies Coklat', 'rownies'));
        $this->assertFalse(SearchHelper::boyerMooreContains('Kue Brownies Coklat', 'durian'));
    }

    public function test_ranked_search_prioritizes_exact_then_partial(): void
    {
        $items = new Collection([
            (object) ['nama_produk' => 'Brownies'],
            (object) ['nama_produk' => 'Brownies Coklat'],
            (object) ['nama_produk' => 'Combro'],
        ]);

        $result = SearchHelper::rankedSearch($items, 'Brownies', ['nama_produk']);

        $this->assertCount(2, $result);
        $this->assertSame('Brownies', $result->first()->nama_produk);
        $this->assertSame('Brownies Coklat', $result->last()->nama_produk);
    }
}