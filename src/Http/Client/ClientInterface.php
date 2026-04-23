<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

interface ClientInterface
{
	/**
	 * @throws ClientException
	 */
	public function send(ClientRequestInterface $request): ClientResponseInterface;
}
