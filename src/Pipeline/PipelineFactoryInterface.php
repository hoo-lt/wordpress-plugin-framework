<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;

interface PipelineFactoryInterface
{
	public function create(RequestInterface $request, ?Closure $middlewaresBuilderClosure = null): PipelineInterface;
}
