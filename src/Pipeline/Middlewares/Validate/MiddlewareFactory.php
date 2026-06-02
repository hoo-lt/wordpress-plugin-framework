<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\Validate\ValuesRules\ValuesRulesFactoryInterface,
};

readonly class MiddlewareFactory implements MiddlewareFactoryInterface
{
	public function __construct(
		protected ValuesRulesFactoryInterface $valuesRulesFactory,
	) {
	}
	public function create(): MiddlewareInterface
	{
		return new Middleware($this->valuesRulesFactory);
	}
}