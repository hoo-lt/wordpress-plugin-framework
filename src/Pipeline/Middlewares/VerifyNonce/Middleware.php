<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Exceptions\Forbidden\Exception as ForbiddenException,
	Http\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewareException,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ?string $name = null,
		protected string|int $action = -1,
	) {
	}

	public function withName(string $name): static
	{
		return new static($name, $this->action);
	}

	public function withAction(string|int $action): static
	{
		return new static($this->name, $action);
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		if ($this->name === null) {
			throw new MiddlewareException('middleware misconfigured');
		}

		$nonce = $request->bodyQueryValue($this->name);
		if ($nonce === null) {
			throw new ForbiddenException('nonce is not presented', 'verify_nonce_error');
		}

		if (!wp_verify_nonce($nonce, $this->action)) {
			throw new ForbiddenException('error verifying nonce', 'verify_nonce_error');
		}

		return $closure($request);
	}
}