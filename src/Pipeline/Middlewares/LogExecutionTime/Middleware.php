<?php

namespace Hoo\WooCommercePluginFramework\Middlewares\LogExecutionTime;

use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Loggers\LoggerInterface;

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected LoggerInterface $logger,
	) {
	}

	public function __invoke(Closure $closure): mixed
	{
		$startTime = microtime(true);

		$result = $callable();

		$stopTime = microtime(true);

		$this->logger->info(sprintf('Execution time: %d ms', ($stopTime - $startTime) * 1000));

		return $result;
	}
}