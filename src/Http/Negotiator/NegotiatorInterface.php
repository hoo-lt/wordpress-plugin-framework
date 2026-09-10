<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesInterface,
};

interface NegotiatorInterface
{
	public function negotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface;
	public function tryNegotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface;
}
