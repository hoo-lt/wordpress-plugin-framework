<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
};

readonly class Middleware implements Pipeline\Middlewares\MiddlewareInterface
{
	use Pipeline\Middlewares\MiddlewareTrait;

	public function __construct(
		protected Capability\Capability $capability,
	) {
	}

	public function __invoke(Http\Request\RequestInterface $request, Closure $closure): mixed
	{
		if (!current_user_can($this->capability->value)) {
			throw new Http\Exceptions\Forbidden\Exception(
				'can not',
				'current_user_can_error',
				$this->exceptionHeaders($request),
			);
		}

		return $closure($request);
	}

	protected function exceptionHeaders(Http\Request\RequestInterface $request): ?array
	{
		$accept = $request->accept();
		if (!$accept) {
			return null;
		}

		return [
			'Content-Type' => $accept,
		];
	}
}