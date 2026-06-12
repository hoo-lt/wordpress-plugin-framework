<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator\ValidatorFactoryInterface;

readonly class MiddlewareFactory implements MiddlewareFactoryInterface
{
	public function __construct(
		protected ValidatorFactoryInterface $validatorFactory,
	) {
	}
	public function create(): MiddlewareInterface
	{
		return new Middleware($this->validatorFactory);
	}
}