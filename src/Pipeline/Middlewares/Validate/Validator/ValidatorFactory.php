<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RulesBuilderInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\KeyValue\Body\KeyValue as BodyKeyValue,
	Pipeline\Middlewares\Validate\KeyValue\BodyQuery\KeyValue as BodyQueryKeyValue,
	Pipeline\Middlewares\Validate\KeyValue\Query\KeyValue as QueryKeyValue,
	Pipeline\Middlewares\Validate\KeyValue\Header\KeyValue as HeaderKeyValue,
	Pipeline\Middlewares\Validate\KeyValue\Route\KeyValue as RouteKeyValue,
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
			new BodyKeyValue($key),
			$closure,
		);
	}

	public function bodyQuery(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new BodyQueryKeyValue($key),
			$closure,
		);
	}

	public function query(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new QueryKeyValue($key),
			$closure,
		);
	}

	public function header(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new HeaderKeyValue($key),
			$closure,
		);
	}

	public function route(string $key, Closure $closure): ValidatorInterface
	{
		return $this->create(
			new RouteKeyValue($key),
			$closure,
		);
	}

	protected function create(KeyValueInterface $keyValue, Closure $closure): ValidatorInterface
	{
		return new Validator(
			$keyValue,
			$this->rules($closure),
		);
	}

	protected function rules(Closure $closure): array
	{
		$rulesBuilder = $closure($this->rulesBuilder);
		if (!$rulesBuilder instanceof RulesBuilderInterface) {
			throw new ValidatorFactoryException('closure must return rules builder instance');
		}

		return $rulesBuilder->build();
	}
}
