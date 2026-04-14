<?php

namespace Hoo\WordPressPluginFramework\Middlewares\VerifyNonce;

use Hoo\WordPressPluginFramework\Http\RequestInterface;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;

class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected readonly RequestInterface $request,
		protected readonly string $nonceName,
	) {
	}

	public function __invoke(object $object, callable $callable): mixed
	{
		$nonce = $this->request->post($this->nonceName);
		if (!$nonce) {
			throw new MiddlewareException('nonce is not presented');
		}

		if (!wp_verify_nonce($nonce, -1)) {
			throw new MiddlewareException('error verifying nonce');
		}

		return $callable($object);
	}
}