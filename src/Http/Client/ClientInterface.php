<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use Hoo\WordPressPluginFramework\{
	Http\Client\Request\RequestInterface,
	Http\Client\Response\ResponseInterface,
};

interface ClientInterface
{
	public function request(RequestInterface $request): ResponseInterface;
}
