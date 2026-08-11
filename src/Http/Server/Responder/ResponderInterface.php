<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Responder;

use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Http\Response\ResponseInterface,
};

interface ResponderInterface
{
	public function respond(RequestInterface $request, mixed $response): ResponseInterface;
}
