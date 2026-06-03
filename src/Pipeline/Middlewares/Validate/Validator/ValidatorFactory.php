<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RulesBuilderInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
};

readonly class ValidatorFactory implements ValidatorFactoryInterface
{
	public function __construct(
		protected RulesBuilderInterface $rulesBuilder,
	) {
	}

	public function create(KeyValueInterface $keyValue, Closure $rulesBuilderClosure): ValidatorInterface
	{
		return new Validator(
			$keyValue,
			$this->rules($rulesBuilderClosure),
		);
	}

	protected function rules(Closure $rulesBuilderClosure): array
	{
		$rulesBuilder = $rulesBuilderClosure($this->rulesBuilder);
		if (!$rulesBuilder instanceof RulesBuilderInterface) {
			throw new ValidatorFactoryException('closure must return rules builder instance');
		}

		return $rulesBuilder->build();
	}
}
