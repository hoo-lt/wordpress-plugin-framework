<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Exceptions\Forbidden\Exception as ForbiddenException,
	Http\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewareInterface,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected string $name,
		protected string|int $action,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		$nonce = $request->value($this->name);
		if ($nonce === null) {
			throw new ForbiddenException('nonce is not presented', 'verify_nonce_error');
		}

		if (!wp_verify_nonce($nonce, $this->action)) {
			throw new ForbiddenException('error verifying nonce', 'verify_nonce_error');
		}

		return $closure($request);
	}
}