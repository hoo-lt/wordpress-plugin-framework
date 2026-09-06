<?php

namespace Hoo\WordPressPluginFramework\Http\ContentNegotiation;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesInterface,
};

interface ContentNegotiatorInterface
{
	public function negotiate(RequestInterface $request, ResponsesInterface $responses): ?ResponseInterface;
}
