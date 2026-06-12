<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Handler;

use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Http\Server\Response\ResponseInterface,
};
use Throwable;

interface HandlerInterface
{
	public function handle(RequestInterface $request, Throwable $throwable): ResponseInterface;
}