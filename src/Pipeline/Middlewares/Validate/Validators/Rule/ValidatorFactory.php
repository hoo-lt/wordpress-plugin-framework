<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RulesBuilderInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class ValidatorFactory implements ValidatorFactoryInterface
{
	public function __construct(
		protected RulesBuilderInterface $rulesBuilder,
	) {
	}

	public function create(KeyValueInterface $keyValue, Closure $closure): ValidatorInterface
	{
		return new Validator(
			$keyValue,
			$this->buildRules($closure),
		);
	}

	public function buildRules(Closure $closure): array
	{
		$rulesBuilder = $closure($this->rulesBuilder);
		if (!$rulesBuilder instanceof RulesBuilderInterface) {
			throw new ValidatorFactoryException('closure must return rules builder instance');
		}

		return $rulesBuilder->build();
	}
}
