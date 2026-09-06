<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Closure;
use Countable;
use Hoo\WordPressPluginFramework\Http\Response\ResponseInterface;
use IteratorAggregate;

interface ResponsesInterface extends IteratorAggregate, Countable
{
    public function first(): ?ResponseInterface;
    public function last(): ?ResponseInterface;

    public function filter(Closure $closure): static;
    public function sort(Closure $closure): static;
}
