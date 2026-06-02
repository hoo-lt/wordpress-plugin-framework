<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\ValuesRules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RulesBuilderInterface,
	Pipeline\Middlewares\Validate\Values\ValuesFactoryInterface,
};

readonly class ValuesRulesFactory implements ValuesRulesFactoryInterface
{
	public function __construct(
		protected ValuesFactoryInterface $valuesFactory,
		protected RulesBuilderInterface $rulesBuilder,
	) {
	}

	public function body(string $key, Closure $closure): ValuesRulesInterface
	{
		return new ValuesRules(
			$this->valuesFactory->body($key),
			$this->rules($closure),
		);
	}

	public function query(string $key, Closure $closure): ValuesRulesInterface
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
			throw new ValuesRulesFactoryException('closure must return rules builder instance');
		}

		return $rulesBuilder->build();
	}
}
