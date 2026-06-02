<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\ValuesRules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\ValidateRequest\Rules\RulesBuilder,
	Pipeline\Middlewares\ValidateRequest\Values\ValuesFactory,
	Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface, Pipeline\Middlewares\ValidateRequest\Values\Values,
};

readonly class ValuesRulesFactory
{
	public function __construct(
		protected ValuesFactory $valuesFactory,
		protected RulesBuilder $rulesBuilder,
	) {
	}

	public function valuesRules(Values $values, array $rules): ValuesRules {
		return new ValuesRules(
			$values,
			$rules,
		);
	}

	public function body(string $key, Closure $closure): ValuesRules
	{
		return new ValuesRules(
			$this->valuesFactory->body($key),
			$this->rules($closure),
		);
	}

	public function query(string $key, Closure $closure): ValuesRules
	{
		return new ValuesRules(
			$this->valuesFactory->query($key),
			$this->rules($closure),
		);
	}

	protected function rules(Closure $closures): array
	{
		$rulesBuilder = $closures($this->rulesBuilder);
		if (!$rulesBuilder instanceof RuleBuilder) {
			//throw there
		}

		return $rulesBuilder->build();
	}
}
