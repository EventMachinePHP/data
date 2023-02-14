<?php

declare(strict_types=1);

use EventMachinePHP\Guard\Guard;

test('Guard::isAOf(passing)')
    ->with('isAOf(passing)')
    ->expect(fn ($value, $class) => Guard::isAOf(value: $value, class: $class))
    ->toHaveValueThat(assertionName: 'toBe', callable: fn ($value, $class) => $value)
    ->notToThrowInvalidArgumentException();

test('Guard::isAOf(failing)')
    ->expectInvalidArgumentException(fn ($value, $class, $message) => $message)
    ->with('isAOf(failing)')
    ->expect(fn ($value, $class, $message) => Guard::isAOf(value: $value, class: $class));

test('Guard::isAOf() Aliases')
    ->expect('isAOf')
    ->validateAliases();

dataset('isAOf(passing)', [
    "('stdClass', 'stdClass')"           => ['stdClass', 'stdClass'],
    '(stdClass::class, stdClass::class)' => [stdClass::class, stdClass::class],
    "('ValueError', 'Error')"            => ['ValueError', 'Error'],
    '(ValueError::class, Error::class)'  => [ValueError::class, Error::class],
]);
dataset('isAOf(failing)', [
    "('stdClass', 123)"                          => ['stdClass', 123, 'Expected a string. Got: 123 (int)'],
    '(stdClass::class, 123)'                     => [stdClass::class, 123, 'Expected a string. Got: 123 (int)'],
    '(RuntimeException::class, stdClass::class)' => [RuntimeException::class, stdClass::class, 'Expected an instance of this class or to this class among its parents "stdClass". Got: "RuntimeException" (string)'],
    "('RuntimeException', 'stdClass')"           => ['RuntimeException', 'stdClass', 'Expected an instance of this class or to this class among its parents "stdClass". Got: "RuntimeException" (string)'],
    "('NonExistentClass', stdClass::class)"      => ['NonExistentClass', stdClass::class, 'Expected an instance of this class or to this class among its parents "stdClass". Got: "NonExistentClass" (string)'],
    "('string', 'stdClass')"                     => ['string', 'stdClass', 'Expected an instance of this class or to this class among its parents "stdClass". Got: "string" (string)'],
    "('Iterator', 'ArrayIterator')"              => ['Iterator', 'ArrayIterator', 'Expected an instance of this class or to this class among its parents "ArrayIterator". Got: "Iterator" (string)'],
    '(Iterator::class, ArrayIterator::class)'    => [Iterator::class, ArrayIterator::class, 'Expected an instance of this class or to this class among its parents "ArrayIterator". Got: "Iterator" (string)'],
    "(123, 'Iterator')"                          => [123, 'Iterator', 'Expected an existing class name. Got: "Iterator" (string)'],
    '(123, Iterator::class)'                     => [123, Iterator::class, 'Expected an existing class name. Got: "Iterator" (string)'],
    "([], 'Iterator')"                           => [[], 'Iterator', 'Expected an existing class name. Got: "Iterator" (string)'],
    '([], Iterator::class)'                      => [[], Iterator::class, 'Expected an existing class name. Got: "Iterator" (string)'],
]);

// TODO: dataset's has duplicate test cases?