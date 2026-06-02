<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\ValuesRules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RulesBuilderInterface,
	Pipeline\Middlewares\Validate\Values\ValuesInterface,
};

readonly class ValuesRulesFactory implements ValuesRulesFactoryInterface
{
	public function __construct(
		protected RulesBuilderInterface $rulesBuilder,
	) {
	}

	public function create(ValuesInterface $values, Closure $rulesBuilderClosure): ValuesRulesInterface
	{
		return new ValuesRules(
			$values,
			$this->rules($rulesBuilderClosure),
		);
	}

	protected function rules(Closure $rulesBuilderClosure): array
	{
		$rulesBuilder = $rulesBuilderClosure($this->rulesBuilder);
		if (!$rulesBuilder instanceof RulesBuilderInterface) {
			throw new ValuesRulesFactoryException('closure must return rules builder instance');
		}

		return $rulesBuilder->build();
	}
}
