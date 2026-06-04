<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Transaction;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\MiddlewareInterface,
	Http\Server\Request\RequestInterface,
	Database\DatabaseInterface,
};
use Throwable;

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected DatabaseInterface $database,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		try {
			$this->database->startTransaction();

			$return = $closure($request);

			$this->database->commit();
		} catch (Throwable $throwable) {
			$this->database->rollback();

			throw $throwable;
		}

		return $return;
	}
}