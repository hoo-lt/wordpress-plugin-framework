<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
};

readonly class Middleware implements Pipeline\Middlewares\MiddlewareInterface
{
	use Pipeline\Middlewares\MiddlewareTrait;

	public function __construct(
		protected string $name,
		protected string|int $action,
	) {
	}

	public function __invoke(Http\Request\RequestInterface $request, Closure $closure): mixed
	{
		$nonce = match ($request->method()) {
			Http\Method\Method::Post, Http\Method\Method::Put, Http\Method\Method::Patch => $request->body() instanceof Http\KeyValue\KeyValueInterface ? $request->body()->value($this->name) : '',
			default => $request->url()->query() instanceof Http\KeyValue\KeyValueInterface ? $request->url()->query()->value($this->name) : '',
		};

		if (!$nonce) {
			throw new Http\Exceptions\Forbidden('nonce is not presented', 'verify_nonce_error');
		}

		if (!wp_verify_nonce($nonce, $this->action)) {
			throw new Http\Exceptions\Forbidden('error verifying nonce', 'verify_nonce_error');
		}

		return $closure($request);
	}
}