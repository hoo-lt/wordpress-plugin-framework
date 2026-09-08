<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Countable;
use Hoo\WordPressPluginFramework\{
    Http\Message\Headers\Accept\AcceptInterface,
    Http\Response\ResponseInterface,
};
use IteratorAggregate;

interface ResponsesInterface extends IteratorAggregate, Countable
{
    public function with(ResponseInterface $response): static;

    public function first(): ResponseInterface;
    public function last(): ResponseInterface;

    public function filterByAccept(AcceptInterface $accept): static;
    public function sortByAccept(AcceptInterface $accept): static;
}
