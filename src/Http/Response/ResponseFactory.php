<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http\Adapters\Response\AdapterInterface;

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
	) {
	}

	public function createFromAdapter(AdapterInterface $adapter): ResponseInterface
	{
	}
}
