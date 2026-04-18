<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Hoo\WordPressPluginFramework\Http\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected string $name,
		protected string|int $action = -1,
	) {
	}

	public function withAction(string|int $action): self
	{
		return new self(
			$this->request,
			$this->name,
			$action,
		);
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