<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares;

interface MiddlewareInterface extends Middlewares\MiddlewareInterface
{
	public function name(): ?string;
	public function withName(string $name): static;
	public function withoutName(): static;

	public function action(): string|int;
	public function withAction(string|int $action): static;
	public function withoutAction(): static;
}