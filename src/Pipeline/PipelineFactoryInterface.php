<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\Http\Server\Request\RequestInterface;

interface PipelineFactoryInterface
{
	public function create(RequestInterface $request, ?Closure $middlewaresBuilderClosure = null): PipelineInterface;
}
