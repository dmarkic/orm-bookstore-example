<?php

namespace Blrf\Bookstore\Log;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * A simple logger implementation
 */
class StderrLogger extends AbstractLogger
{
    public function __construct(
        protected bool $isStderr = true
    ) {
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        if ($this->isStderr) {
            $bt = debug_backtrace();
            $who = isset($bt[2]['class']) ? $bt[2]['class'] . '::' : '';
            $who .= $bt[2]['function'] ?? '';
            fwrite(STDERR, "[" . date('H:i:s') . "]: [$who]: [$level]: $message\n");
        }
    }
}
