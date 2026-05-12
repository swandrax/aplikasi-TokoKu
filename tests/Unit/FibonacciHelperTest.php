<?php

namespace Tests\Unit;

use App\Helpers\FibonacciHelper;
use PHPUnit\Framework\TestCase;

class FibonacciHelperTest extends TestCase
{
    public function test_recursive_fibonacci_calculates_correctly(): void
    {
        $this->assertEquals(0, FibonacciHelper::recursiveFibonacci(0));
        $this->assertEquals(1, FibonacciHelper::recursiveFibonacci(1));
        $this->assertEquals(1, FibonacciHelper::recursiveFibonacci(2));
        $this->assertEquals(2, FibonacciHelper::recursiveFibonacci(3));
        $this->assertEquals(3, FibonacciHelper::recursiveFibonacci(4));
        $this->assertEquals(5, FibonacciHelper::recursiveFibonacci(5));
    }

    public function test_iterative_fibonacci_calculates_correctly(): void
    {
        $this->assertEquals(0, FibonacciHelper::iterativeFibonacci(0));
        $this->assertEquals(1, FibonacciHelper::iterativeFibonacci(1));
        $this->assertEquals(1, FibonacciHelper::iterativeFibonacci(2));
        $this->assertEquals(2, FibonacciHelper::iterativeFibonacci(3));
        $this->assertEquals(3, FibonacciHelper::iterativeFibonacci(4));
        $this->assertEquals(5, FibonacciHelper::iterativeFibonacci(5));
    }

    public function test_fibonacci_performance_test_returns_correct_structure(): void
    {
        $result = FibonacciHelper::testFibonacciPerformance(5);

        $this->assertArrayHasKey('n', $result);
        $this->assertArrayHasKey('recursive_result', $result);
        $this->assertArrayHasKey('recursive_time', $result);
        $this->assertArrayHasKey('iterative_result', $result);
        $this->assertArrayHasKey('iterative_time', $result);
        $this->assertArrayHasKey('big_o_recursive', $result);
        $this->assertArrayHasKey('big_o_iterative', $result);

        $this->assertEquals(5, $result['recursive_result']);
        $this->assertEquals(5, $result['iterative_result']);
        $this->assertEquals('O(2^n)', $result['big_o_recursive']);
        $this->assertEquals('O(n)', $result['big_o_iterative']);
    }

    /**
     * Test untuk memeriksa konflik saat update versi dependencies
     * Menggunakan Fibonacci untuk testing performa
     */
    public function test_conflict_testing_with_fibonacci_on_version_update(): void
    {
        // Simulasi testing performa saat update versi
        $n = 10; // Nilai yang cukup untuk melihat perbedaan performa
        $result = FibonacciHelper::testFibonacciPerformance($n);

        // Pastikan hasil benar
        $this->assertEquals(55, $result['recursive_result']); // Fibonacci(10) = 55
        $this->assertEquals(55, $result['iterative_result']);

        // Pastikan waktu iteratif lebih cepat dari rekursif untuk n besar
        $this->assertLessThan($result['recursive_time'], $result['iterative_time'] * 0.1); // Iteratif harus jauh lebih cepat

        // Log untuk monitoring konflik versi
        // Jika waktu berubah drastis, mungkin ada konflik dalam dependencies
        echo "Fibonacci Test Results:\n";
        echo "Recursive Time: " . $result['recursive_time'] . " seconds\n";
        echo "Iterative Time: " . $result['iterative_time'] . " seconds\n";
        echo "Big O Recursive: " . $result['big_o_recursive'] . "\n";
        echo "Big O Iterative: " . $result['big_o_iterative'] . "\n";
    }
}