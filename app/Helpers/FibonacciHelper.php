<?php

namespace App\Helpers;

class FibonacciHelper
{
    /**
     * Menghitung bilangan Fibonacci ke-n menggunakan rekursi (O(2^n) - untuk testing performa)
     * @param int $n
     * @return int
     */
    public static function recursiveFibonacci(int $n): int
    {
        if ($n <= 1) {
            return $n;
        }
        return self::recursiveFibonacci($n - 1) + self::recursiveFibonacci($n - 2);
    }

    /**
     * Menghitung bilangan Fibonacci ke-n menggunakan iterasi (O(n) - lebih optimal)
     * @param int $n
     * @return int
     */
    public static function iterativeFibonacci(int $n): int
    {
        if ($n <= 1) {
            return $n;
        }
        $a = 0;
        $b = 1;
        for ($i = 2; $i <= $n; $i++) {
            $c = $a + $b;
            $a = $b;
            $b = $c;
        }
        return $b;
    }

    /**
     * Test untuk memeriksa konflik saat update versi dependencies
     * Menggunakan Fibonacci untuk testing performa
     * @param int $n
     * @return array
     */
    public static function testFibonacciPerformance(int $n = 10): array
    {
        $start = microtime(true);
        $recursive = self::recursiveFibonacci($n);
        $recursiveTime = microtime(true) - $start;

        $start = microtime(true);
        $iterative = self::iterativeFibonacci($n);
        $iterativeTime = microtime(true) - $start;

        return [
            'n' => $n,
            'recursive_result' => $recursive,
            'recursive_time' => $recursiveTime,
            'iterative_result' => $iterative,
            'iterative_time' => $iterativeTime,
            'big_o_recursive' => 'O(2^n)',
            'big_o_iterative' => 'O(n)',
        ];
    }
}