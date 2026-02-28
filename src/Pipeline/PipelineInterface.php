<?php

namespace Hoo\WordpressPluginFramework\Pipeline;

interface PipelineInterface
{
	public function object(object $object): self;
	public function middlewares(string ...$middlewares): self;
	public function __invoke(callable $callable): mixed;
}