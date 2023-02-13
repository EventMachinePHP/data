<?php

declare(strict_types=1);

namespace EventMachinePHP\Guard\Guards;

use EventMachinePHP\Guard\Attributes\Alias;
use EventMachinePHP\Guard\Exceptions\InvalidArgumentException;

/**
 * This trait contains methods for validating equality values.
 *
 * @method static mixed eq(mixed $value, mixed $expect, ?string $message = null) @see Guard::isEqualTo()
 * @method static mixed equalTo(mixed $value, mixed $expect, ?string $message = null) @see Guard::isEqualTo()
 * @method static mixed neq(mixed $value, mixed $expect, ?string $message = null) @see Guard::IsNotEqualTo()
 * @method static mixed notEq(mixed $value, mixed $expect, ?string $message = null) @see Guard::IsNotEqualTo()
 * @method static mixed notEqualTo(mixed $value, mixed $expect, ?string $message = null) @see Guard::IsNotEqualTo()
 */
trait EqualityGuards
{
    /**
     * Validates if the given value is equal to the expected value
     * and returns it.
     *
     * If the value is not equal to the expected value, an exception
     * is thrown. The exception message can be custom or a default
     * message is used, which includes the expected value and the
     * received value.
     *
     * @param  mixed  $value The value to check.
     * @param  mixed  $expect The expected value.
     * @param  string|null  $message A custom message to use in the exception.
     *
     * @return mixed The value if it is equal to the expected value.
     *
     * @throws InvalidArgumentException If the value is not equal to the expected value.
     *
     * @see Guard::eq()
     * @see Guard::equalTo()
     */
    #[Alias(['eq', 'equalTo'])]
    public static function isEqualTo(mixed $value, mixed $expect, ?string $message = null): mixed
    {
        return $value != $expect
            ? throw InvalidArgumentException::create(
                customMessage: $message,
                defaultMessage: 'Expected a value equal to: %s (%s). Got: %s (%s)',
                values: [self::valueToString($value), self::valueToType($value), self::valueToString($expect), self::valueToType($expect)],
            )
            : $value;
    }

    /**
     * Validates if the given value is not equal to the expected value and returns it.
     *
     * Throws an {@see InvalidArgumentException} if the value is equal to the expected
     * value. The exception message can be customized through the `$message` parameter
     * or a default message will be used if not provided.
     *
     * @param  mixed  $value     The value to be validated.
     * @param  mixed  $expect    The expected value.
     * @param  string|null  $message  Optional custom error message.
     *
     * @return mixed The original value if validation passes.
     *
     * @throws InvalidArgumentException If the value is equal to the expected value.
     *
     * @see Guard::neq()
     * @see Guard::notEq()
     * @see Guard::notEqualTo()
     */
    #[Alias(['neq', 'notEq', 'notEqualTo'])]
    public static function IsNotEqualTo(mixed $value, mixed $expect, ?string $message = null): mixed
    {
        return $value == $expect
            ? throw InvalidArgumentException::create(
                customMessage: $message,
                defaultMessage: 'Expected a value different from: %s (%s). Got: %s (%s)',
                values: [self::valueToString($value), self::valueToType($value), self::valueToString($expect), self::valueToType($expect)],
            )
            : $value;
    }
}