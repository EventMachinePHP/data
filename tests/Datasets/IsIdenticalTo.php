<?php

declare(strict_types=1);

dataset(passingCasesDescription(filePath: __FILE__), [
    '(1, 1)' => [1, 1],
]);

dataset(failingCasesDescription(filePath: __FILE__), [
    "(1, '1')"   => [1, '1', 'Expected a value identical to: 1 (int). Got: "1" (string)'],
    '(1, true)'  => [1, true, 'Expected a value identical to: 1 (int). Got: true (bool)'],
    '(1, false)' => [1, false, 'Expected a value identical to: 1 (int). Got: false (bool)'],
]);

dataset(errorMessagesDescription(filePath: __FILE__), [
    'default error message' => [null, 'DEFAULT'],
    'custom error message'  => ['Custom Error Message', 'Custom Error Message'],
]);
