<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use Hoo\WordPressPluginFramework\Http\{
	Request\RequestInterface,
	Response\ResponseInterface,
};

interface ClientInterface
{
	public function send(RequestInterface $request): ResponseInterface;
}
