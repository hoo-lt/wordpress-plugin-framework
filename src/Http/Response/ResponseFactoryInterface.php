<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http\Adapters\Response\AdapterInterface;

interface ResponseFactoryInterface
{
	public function createFromAdapter(AdapterInterface $adapter): ResponseInterface;
}
