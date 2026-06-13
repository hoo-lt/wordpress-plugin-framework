<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Transaction;

use Hoo\WordPressPluginFramework\{
	Database\DatabaseInterface,
	Pipeline\Middlewares\MiddlewareInterface
};

readonly class MiddlewareFactory implements MiddlewareFactoryInterface
{
	public function __construct(
		protected DatabaseInterface $database,
	) {
	}

	public function create(): MiddlewareInterface
	{
		return new Middleware($this->database);
	}
}