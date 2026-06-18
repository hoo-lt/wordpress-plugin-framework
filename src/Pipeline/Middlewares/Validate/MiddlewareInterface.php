<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares,
	Pipeline\Middlewares\Validate\Validator\ValidatorInterface,
};

interface MiddlewareInterface extends Middlewares\MiddlewareInterface
{
	public function validators(): array;
	public function withValidators(ValidatorInterface ...$validators): static;
	public function withoutValidators(): static;

	public function withValidator(ValidatorInterface $validator): static;
}
