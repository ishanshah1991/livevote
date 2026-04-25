<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class AlreadyVotedException extends RuntimeException
{
    public function __construct(int $pollId, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('A vote has already been cast for poll ID "%d" from this identity.', $pollId),
            $code,
            $previous,
        );
    }
}
