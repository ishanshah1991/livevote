<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Thrown when an account is being created with an email that already exists.
 */
final class DuplicateEmailException extends RuntimeException
{
    /**
     * @param  string  $email
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct(string $email, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('An account with the email "%s" already exists.', $email),
            $code,
            $previous,
        );
    }
}
