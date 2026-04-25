<?php

declare(strict_types=1);

namespace App\Exceptions;

use InvalidArgumentException;
use Throwable;

final class InvalidOptionException extends InvalidArgumentException
{
    public function __construct(int $optionId, int $pollId, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Option ID "%d" does not belong to poll ID "%d".', $optionId, $pollId),
            $code,
            $previous,
        );
    }
}
