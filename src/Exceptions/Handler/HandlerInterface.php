<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Handler;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
};
use Throwable;

interface HandlerInterface
{
	public function handle(RequestInterface $request, Throwable $throwable): ResponseInterface;
}