<?php

declare(strict_types=1);

namespace EventMachinePHP\Guard\Tests;

use EventMachinePHP\Guard\Guard;

test(description: passingCasesDescription(__FILE__))
    ->with(data: passingCasesDescription(__FILE__))
    ->expect(fn ($value, $limit) => (
        Guard::isLessThanOrEqual(
            value: $value,
            limit: $limit,
        )
    ))
    ->toHaveValue(fn ($value, $limit) => $value)
    ->toHaveValueThat(assertionName: 'toBeLessThanOrEqual', callable: fn ($value, $limit) => $value);
