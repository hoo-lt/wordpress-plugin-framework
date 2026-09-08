<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use ArrayIterator;
use Closure;
use Hoo\WordPressPluginFramework\{
    Http\Message\Headers\Accept\AcceptInterface,
    Http\Response\ResponseInterface,
};
use Traversable;

readonly class Responses implements ResponsesInterface
{
    public function __construct(
        protected array $responses = [],
    ) {
    }

    public function with(ResponseInterface $response): static
    {
        $responses = $this->responses;
        $responses[] = $response;

        return new static($responses);
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

    public function filterByAccept(AcceptInterface $accept): static
    {
        return $this->filter(fn(ResponseInterface $response) => $this->q($accept, $response) > 0);
    }

    public function sortByAccept(AcceptInterface $accept): static
    {
        return $this->sort(fn(ResponseInterface $a, ResponseInterface $b) => $this->q($accept, $b) <=> $this->q($accept, $a));
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

    protected function filter(Closure $closure): static
    {
        $responses = array_filter($this->responses, $closure);

        return new static($responses);
    }

    protected function sort(Closure $closure): static
    {
        $responses = $this->responses;
        usort($responses, $closure);

        return new static($responses);
    }

    protected function q(AcceptInterface $accept, ResponseInterface $response): float
    {
        $contentType = $response->headers()->contentType();
        if ($contentType === null) {
            return 1;
        }

        $mediaType = $contentType->mediaType();
        return $accept->q($mediaType);
    }
}
