<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __construct(
		protected RequestInterface $request,
		protected string $name,
		protected string|int $action,
	) {
	}

	public function __invoke(Closure $closure): mixed
	{
		$nonce = match ($this->request->method()) {
			Method::Post, Method::Put, Method::Patch => $this->request->body($this->name),
			default => $this->request->query($this->name),
		};

		if (!$nonce) {
			throw new MiddlewareException('nonce is not presented', 'verify_nonce_error');
		}

		if (!wp_verify_nonce($nonce, $this->action)) {
			throw new MiddlewareException('error verifying nonce', 'verify_nonce_error');
		}

		return $callable();
	}
}