<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RulesBuilderInterface,
	Pipeline\Middlewares\Validate\KeyValues\KeyValuesInterface,
	Pipeline\Middlewares\Validate\KeyValues\Body\KeyValues as BodyKeyValues,
	Pipeline\Middlewares\Validate\KeyValues\BodyQuery\KeyValues as BodyQueryKeyValues,
	Pipeline\Middlewares\Validate\KeyValues\Query\KeyValues as QueryKeyValues,
	Pipeline\Middlewares\Validate\KeyValues\Header\KeyValues as HeaderKeyValues,
	Pipeline\Middlewares\Validate\KeyValues\Route\KeyValues as RouteKeyValues,
};

readonly class ValidatorFactory implements ValidatorFactoryInterface
{
	public function __construct(
		protected RulesBuilderInterface $rulesBuilder,
	) {
	}

	public function body(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new BodyKeyValues($key),
			$closure,
		);
	}

	public function bodyQuery(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new BodyQueryKeyValues($key),
			$closure,
		);
	}

	public function query(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new QueryKeyValues($key),
			$closure,
		);
	}

	public function header(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new HeaderKeyValues($key),
			$closure,
		);
	}

	public function route(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new RouteKeyValues($key),
			$closure,
		);
	}

	protected function create(KeyValuesInterface $keyValue, Closure $closure): ValidatorInterface
	{
		return new Validator(
			$keyValue,
			$this->buildRules($closure),
		);
	}

	protected function buildRules(Closure $closure): array
	{
		$rulesBuilder = $closure($this->rulesBuilder);
		if (!$rulesBuilder instanceof RulesBuilderInterface) {
			throw new ValidatorFactoryException('closure must return rules builder instance');
		}

		return $rulesBuilder->build();
	}
}
