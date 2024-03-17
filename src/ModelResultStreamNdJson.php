<?php

declare(strict_types=1);

namespace Blrf\Bookstore;

use Blrf\Orm\Model;
use Blrf\Orm\Model\ResultStream;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use React\Stream\Util;
use Evenement\EventEmitter;

use function React\Async\await;

/**
 * Streaming newline-delimited JSON model result
 *
 */
class ModelResultStreamNdJson extends EventEmitter implements ReadableStreamInterface
{
    protected bool $closed = false;

    public function __construct(
        public readonly ReadableStreamInterface $stream,
    ) {
        $this->stream->on('data', [$this, 'onData']);
        $this->stream->on('end', [$this, 'onEnd']);
        $this->stream->on('error', [$this, 'onError']);
        $this->stream->on('close', [$this, 'close']);
    }

    public function isReadable(): bool
    {
        return !$this->closed;
    }

    public function close(): void
    {
        if ($this->closed) {
            return;
        }
        $this->closed = true;
        $this->stream->close();
        $this->emit('close');
        $this->removeAllListeners();
    }

    public function pause(): void
    {
        $this->stream->pause();
    }

    public function resume(): void
    {
        $this->stream->resume();
    }

    public function pipe(WritableStreamInterface $dest, array $options = []): WritableStreamInterface
    {
        Util::pipe($this, $dest, $options);
        return $dest;
    }

    public function onData(Model $model)
    {
        $this->emit('data', [json_encode(await($model->toArray())) . "\n"]);
    }

    public function onEnd()
    {
        if (!$this->closed) {
            $this->emit('end');
            $this->close();
        }
    }

    public function onError(\Throwable $error)
    {
        $this->emit('error', [$error]);
        $this->close();
    }
}
