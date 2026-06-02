<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareException,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\Validate\ValuesRules\ValuesRulesInterface,
	Pipeline\Middlewares\Validate\ValuesRules\ValuesRulesFactoryInterface
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ValuesRulesFactoryInterface $valuesRulesFactory,
		protected array $valuesRules = [],
	) {
	}

	public function withValuesRules(ValuesRulesInterface ...$valuesRules): static
	{
		return new static($this->valuesRulesFactory, $valuesRules);
	}

	public function withValuesRule(ValuesRulesInterface $valuesRules): static
	{
		return $this->withValuesRules(...$this->valuesRules, $valuesRules);
	}

	public function body(string $key, Closure $closure): static
	{
		return $this->withValuesRule(
			$this->valuesRulesFactory->body($key, $closure)
		);
	}

	public function query(string $key, Closure $closure): static
	{
		return $this->withValuesRule(
			$this->valuesRulesFactory->query($key, $closure)
		);
	}

	public function header(string $key, Closure $closure): static
	{
		return $this->withValuesRule(
			$this->valuesRulesFactory->header($key, $closure)
		);
	}

	public function route(string $key, Closure $closure): static
	{
		return $this->withValuesRule(
			$this->valuesRulesFactory->route($key, $closure)
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
