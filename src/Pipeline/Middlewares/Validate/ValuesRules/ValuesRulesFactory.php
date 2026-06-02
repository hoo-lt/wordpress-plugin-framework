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

	public function create(ValuesInterface $values, Closure $closure): ValuesRulesInterface
	{
		return new ValuesRules(
			$values,
			$this->rules($closure),
		);
	}

	protected function rules(Closure $closures): array
	{
		$rulesBuilder = $closures($this->rulesBuilder);
		if (!$rulesBuilder instanceof RulesBuilderInterface) {
			throw new ValuesRulesFactoryException('closure must return rules builder instance');
		}

		return $rulesBuilder->build();
	}
}
