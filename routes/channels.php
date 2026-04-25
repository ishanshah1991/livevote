<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;

// poll.{pollId} is a public channel — anyone may subscribe without auth.
Broadcast::channel('poll.{pollId}', fn (): bool => true);
