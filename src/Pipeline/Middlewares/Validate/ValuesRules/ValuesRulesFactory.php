<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\ValuesRules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RulesBuilderInterface,
	Pipeline\Middlewares\Validate\Values\Body\Values as BodyValues,
	Pipeline\Middlewares\Validate\Values\Query\Values as QueryValues,
	Pipeline\Middlewares\Validate\Values\Header\Values as HeaderValues,
	Pipeline\Middlewares\Validate\Values\Route\Values as RouteValues,
};

readonly class ValuesRulesFactory implements ValuesRulesFactoryInterface
{
	public function __construct(
		protected RulesBuilderInterface $rulesBuilder,
	) {
	}

	public function body(string $key, Closure $closure): ValuesRulesInterface
	{
		return new ValuesRules(
			new BodyValues($key),
			$this->rules($closure),
		);
	}

	public function query(string $key, Closure $closure): ValuesRulesInterface
	{
		return new ValuesRules(
			new QueryValues($key),
			$this->rules($closure),
		);
	}

	public function header(string $key, Closure $closure): ValuesRulesInterface
	{
		return new ValuesRules(
			new HeaderValues($key),
			$this->rules($closure),
		);
	}

	public function route(string $key, Closure $closure): ValuesRulesInterface
	{
		return new ValuesRules(
			new RouteValues($key),
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
