<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-soar.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Guanguans\LaravelSoar\Outputs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LogOutput extends Output
{
    protected string $channel;

    public function __construct(string $channel = 'daily')
    {
        $this->channel = $channel;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \JsonException
     */
    public function output(Collection $scores, $dispatcher): void
    {
        $scores->each(fn (array $score) => Log::channel($this->channel)->warning(
            $score['Summary'].PHP_EOL.to_pretty_json($score)
        ));
    }
}
