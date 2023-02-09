<?php

declare(strict_types=1);

use EventMachinePHP\Guard\Guard;
use EventMachinePHP\Guard\Exceptions\InvalidArgumentException;

test('Guard::scalar ✅', function ($value): void {
    expect(Guard::scalar(value: $value))
        ->toBe($value)
        ->toBeScalar()
        ->not()->toThrow(InvalidArgumentException::class);
})->with([
    "('1')"  => ['1'],
    '(123)'  => [123],
    '(true)' => [true],
]);

test('Guard::scalar ❌', function ($value, $message): void {
    expect(fn () => Guard::scalar(value: $value))
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    '(null)'           => [null, 'Expected a scalar value. Got: null (null)'],
    '([])'             => [[], 'Expected a scalar value. Got: array (array)'],
    '(new stdClass())' => [new stdClass(), 'Expected a scalar value. Got: stdClass (stdClass)'],
]);