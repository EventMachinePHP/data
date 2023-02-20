<?php

declare(strict_types=1);

use Pest\Datasets;
use function Ozzie\Nest\test;
use EventMachinePHP\Guard\Guard;
use function Ozzie\Nest\describe;
use EventMachinePHP\Guard\Exceptions\InvalidGuardArgumentException;

test('no duplicate guard alias exists', function (): void {
    // TODO: Do this thorough traits

    // Create an array to store the method aliases
    $methodAliases = [];

    // Create a ReflectionClass instance for the Guard class
    $class = new ReflectionClass(Guard::class);

    // Iterate over each method in the Guard class
    foreach ($class->getMethods() as $method) {
        // Check if the method has any attributes
        foreach ($method->getAttributes() as $attribute) {
            // Get the arguments for the attribute
            $attributeArguments = $attribute->getArguments()[0];

            // Convert the argument to an array if it's not already
            $aliasMethodNames = is_array($attributeArguments) ? $attributeArguments : [$attributeArguments];

            // Iterate over each alias for the method
            foreach ($aliasMethodNames as $alias) {
                // Get the previous method that used this alias, if any
                $prevMethod = $methodAliases[$alias] ?? null;

                // Check if the alias has not been used by another method
                $this->assertArrayNotHasKey(
                    key: $alias,
                    array: $methodAliases,
                    message: "Method alias '".$alias."' is already used by method Guard::".$prevMethod.'().',
                );

                // Store the method name for the current alias
                $methodAliases[$alias] = $method->getName();
            }
        }
    }
});

describe('Alias Docblocks: ', function (): void {
    // Create a ReflectionClass instance for the Guard class
    $reflectionClass = new ReflectionClass(Guard::class);

    // Create an array to store the count of aliases by trait
    $aliasCountByTraits = array_fill_keys($reflectionClass->getTraitNames(), 0);

    // Create an array to store the aliases for each method
    $aliasesByMethods = [];

    // Get all the traits used by the Guard class
    $traits = $reflectionClass->getTraits();

    // Iterate over each trait and its methods
    foreach ($traits as $trait) {
        foreach ($trait->getMethods() as $method) {
            // Check if the method has any attributes
            foreach ($method->getAttributes() as $attribute) {
                // Get the arguments for the attribute
                $attributeArguments = $attribute->getArguments()[0];

                // Convert the argument to an array if it's not already
                $aliasMethodNames = is_array($attributeArguments) ? $attributeArguments : [$attributeArguments];

                // Increment the alias count for the trait and store the alias for the method
                foreach ($aliasMethodNames as $alias) {
                    $aliasesByMethods[$method->getName()][] = $alias;
                    $aliasCountByTraits[$trait->getName()]++;
                }
            }
        }
    }

    // Iterate over each trait
    foreach ($traits as $trait) {
        // Skip the trait if it has no aliases
        if ($aliasCountByTraits[$trait->getName()] === 0) {
            continue;
        }

        // Get the docblock for the trait
        $traitDocComment = $trait->getDocComment();

        // If the trait does not have a docblock, fail the test
        if ($traitDocComment === false) {
            $this->fail(generateTraitDocBlockForAliases($trait));
        }

        // Iterate over each method in the trait
        foreach ($trait->getMethods() as $method) {
            // Skip the method if it has no aliases
            if (!isset($aliasesByMethods[$method->getName()])) {
                continue;
            }

            // Iterate over each alias for the method
            foreach ($method->getAttributes() as $attribute) {
                $attributeArguments = $attribute->getArguments()[0];
                $aliasMethodNames   = is_array($attributeArguments) ? $attributeArguments : [$attributeArguments];
                foreach ($aliasMethodNames as $alias) {
                    // Check if the alias is documented in the trait docblock
                    test("{$alias} (Trait DocBlock)", function () use ($trait, $alias, $method, $traitDocComment): void {
                        $this->assertStringContainsString(
                            needle: generateMethodDocBlockDefinition($method, $alias),
                            haystack: $traitDocComment,
                            message: generateMethodDocBlockDefinitionErrorMessage($alias, $trait->getName()),
                        );
                    });

                    // Check if the alias is documented in the method docblock
                    test("{$alias} (Method DocBlock)", function () use ($alias, $method): void {
                        $this->assertStringContainsString(
                            needle: generateMethodAliasSeeDefinition($alias),
                            haystack: $method->getDocComment() === false ? '' : $method->getDocComment(),
                            message: generateMethodAliasSeeDefinitionErrorMessage($method),
                        );
                    });
                }
            }
        }
    }
});

describe('Guard Alias: ', function (): void {
    // Create a ReflectionClass instance for the Guard class
    $reflectionClass = new ReflectionClass(Guard::class);

    // Get all the traits used by the Guard class
    $traits = $reflectionClass->getTraits();

    // Iterate over each trait and its methods
    foreach ($traits as $trait) {
        foreach ($trait->getMethods() as $method) {
            // Check if the method has any attributes
            foreach ($method->getAttributes() as $attribute) {
                // Get the arguments for the attribute
                $attributeArguments = $attribute->getArguments()[0];

                // Convert the argument to an array if it's not already
                $aliasMethodNames = is_array($attributeArguments) ? $attributeArguments : [$attributeArguments];

                // Increment the alias count for the trait and store the alias for the method
                foreach ($aliasMethodNames as $aliasMethodName) {
                    // Randomly select a passing case for the method
                    $passingCases    = Datasets::get($method->getName().PASSING_CASES);
                    $passingCaseKeys = array_keys($passingCases);
                    $passingCases    = $passingCases[$passingCaseKeys[array_rand($passingCaseKeys)]];

                    // Randomly select a failing case for the method
                    $failingCases    = Datasets::get($method->getName().FAILING_CASES);
                    $failingCaseKeys = array_keys($failingCases);
                    $failingCases    = $failingCases[$failingCaseKeys[array_rand($failingCaseKeys)]];
                    array_pop($failingCases);

                    test($aliasMethodName.'(passing)', function () use ($passingCases, $aliasMethodName): void {
                        expect(call_user_func([Guard::class, $aliasMethodName], ...$passingCases))
                            ->toBe($passingCases[array_key_first($passingCases)]);
                    });

                    test($aliasMethodName.'(failing)', function () use ($failingCases, $aliasMethodName): void {
                        expect(fn () => call_user_func([Guard::class, $aliasMethodName], ...$failingCases))
                            ->toThrow(InvalidGuardArgumentException::class);
                    });
                }
            }
        }
    }
});
