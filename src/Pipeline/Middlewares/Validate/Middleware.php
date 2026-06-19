<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\MiddlewareException,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected array $validators = [],
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		if ($this->validators === []) {
			throw new MiddlewareException('middleware misconfigured');
		}

		$messages = new MessageCollection();

		foreach ($this->validators as $validator) {
			$validator->validate(
				$request,
				$messages->add(...),
			);
		}

		if ($messages->isNotEmpty()) {
			throw new Exceptions\UnprocessableContent\Exception('validation error', '', $messages);
		}

		return $closure($request);
	}
}
