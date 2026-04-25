<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class PollNotFoundException extends RuntimeException
{
    public function __construct(int $pollId, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Poll with ID "%d" was not found.', $pollId),
            $code,
            $previous,
        );
    }
}
