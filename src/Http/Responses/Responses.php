<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use ArrayIterator;
use Closure;
use Hoo\WordPressPluginFramework\Http\Response\ResponseInterface;
use Traversable;

class Responses implements ResponsesInterface
{
    public function __construct(
        protected array $responses = [],
    ) {
    }

    public function add(ResponseInterface $response): void
    {
        $this->responses[] = $response;
    }

    public function first(): ResponseInterface
    {
        $key = array_key_first($this->responses);
        if ($key === null) {
            throw new ResponsesException('collection is empty');
        }

        return $this->responses[$key];
    }

    public function last(): ResponseInterface
    {
        $key = array_key_last($this->responses);
        if ($key === null) {
            throw new ResponsesException('collection is empty');
        }

        return $this->responses[$key];
    }

    public function filter(Closure $closure): static
    {
        $responses = array_filter($this->responses, $closure);

        return new static($responses);
    }

    public function sort(Closure $closure): static
    {
        $responses = $this->responses;
        usort($responses, $closure);

        return new static($responses);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator(
            array_values($this->responses),
        );
    }

    public function count(): int
    {
        return count($this->responses);
    }
}
