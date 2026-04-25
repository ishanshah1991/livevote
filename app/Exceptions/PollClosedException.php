<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class PollClosedException extends RuntimeException
{
    public function __construct(int $pollId, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Poll ID "%d" is not currently accepting votes.', $pollId),
            $code,
            $previous,
        );
    }
}
