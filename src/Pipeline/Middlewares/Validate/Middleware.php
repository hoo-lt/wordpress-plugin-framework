<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareException,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\Validate\Values\ValuesInterface,
	Pipeline\Middlewares\Validate\ValuesRules\ValuesRulesFactoryInterface,
	Pipeline\Middlewares\Validate\Values\Body\Values as BodyValues,
	Pipeline\Middlewares\Validate\Values\Query\Values as QueryValues,
	Pipeline\Middlewares\Validate\Values\Header\Values as HeaderValues,
	Pipeline\Middlewares\Validate\Values\Route\Values as RouteValues,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ValuesRulesFactoryInterface $valuesRulesFactory,
		protected array $valuesRules = [],
	) {
	}

	public function withValuesRules(ValuesInterface $values, Closure $closure): static
	{
		return new static(
			$this->valuesRulesFactory,
			[
				...$this->valuesRules,
				$this->valuesRulesFactory->create($values, $closure),
			],
		);
	}

	public function body(string $key, Closure $closure): static
	{
		return $this->withValuesRules(
			new BodyValues($key),
			$closure,
		);
	}

	public function query(string $key, Closure $closure): static
	{
		return $this->withValuesRules(
			new QueryValues($key),
			$closure,
		);
	}

	public function header(string $key, Closure $closure): static
	{
		return $this->withValuesRules(
			new HeaderValues($key),
			$closure,
		);
	}

	public function route(string $key, Closure $closure): static
	{
		return $this->withValuesRules(
			new RouteValues($key),
			$closure,
		);
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		if ($this->valuesRules === []) {
			throw new MiddlewareException('middleware misconfigured');
		}

		$messages = new MessageCollection();

		foreach ($this->valuesRules as $valuesRules) {
			$values = $valuesRules->values($request);
			if ($values === []) {
				throw new Exceptions\BadRequest\Exception('incorrect request', '');
			}

			$rules = $valuesRules->rules();

			foreach ($values as $key => $value) {
				foreach ($rules as $rule) {
					$rule($value, fn($message) => $messages->add($key, $message));
				}
			}
		}

		if ($messages->isNotEmpty()) {
			throw new Exceptions\UnprocessableContent\Exception('validation error', '', $messages);
		}

		return $closure($request);
	}
}
