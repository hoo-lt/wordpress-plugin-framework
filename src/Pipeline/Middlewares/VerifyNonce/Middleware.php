<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Closure;
use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __construct(
		protected string $name,
		protected string|int $action,
	) {
	}

	public function __invoke(?RequestInterface $request, Closure $closure): mixed
	{
		if ($request === null) {
			throw new MiddlewareException('no request provided', 500);
		}

		$nonce = match ($request->method()) {
			Method::Post, Method::Put, Method::Patch => $request->body()->value($this->name),
			default => $request->url()->query()->value($this->name),
		};

		if (!$nonce) {
			throw new MiddlewareException('nonce is not presented', 'verify_nonce_error');
		}

		if (!wp_verify_nonce($nonce, $this->action)) {
			throw new MiddlewareException('error verifying nonce', 'verify_nonce_error');
		}

		return $closure($request);
	}
}