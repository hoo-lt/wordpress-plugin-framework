<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

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

	public function __invoke(callable $callable): mixed
	{
		$nonce = $this->request->post($this->name);
		if (!$nonce) {
			throw new MiddlewareException('nonce is not presented');
		}

		if (!wp_verify_nonce($nonce, $this->action)) {
			throw new MiddlewareException('error verifying nonce');
		}

		return $callable();
	}
}