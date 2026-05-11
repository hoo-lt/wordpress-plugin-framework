<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest;

use Closure;
use Hoo\WordPressPluginFramework\Http;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\{
	MiddlewareInterface,
	MiddlewareTrait,
	ValidateRequest\Input\InputInterface,
	ValidateRequest\Rules\RuleInterface,
};

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __construct(
		protected array $inputs = [],
		protected ?InputInterface $input = null,
	) {
	}

	public function withInput(InputInterface $input): self
	{
		return new self(
			$this->inputs(),
			$input,
		);
	}

	public function body(string $key): self
	{
		return $this->withInput(
			new Input\Body($key),
		);
	}

	public function query(string $key): self
	{
		return $this->withInput(
			new Input\Query($key),
		);
	}

	public function withRules(RuleInterface ...$rules): self
	{
		return new self(
			$this->inputs,
			$this->input->withRules(
				...$rules
			)
		);
	}

	public function bool(): self
	{
		return $this->withRules(
			new Rules\Bool\Rule(),
		);
	}

	public function float(): self
	{
		return $this->withRules(
			new Rules\Float\Rule(),
		);
	}

	public function int(): self
	{
		return $this->withRules(
			new Rules\Int\Rule(),
		);
	}

	public function string(): self
	{
		return $this->withRules(
			new Rules\String\Rule(),
		);
	}

	public function __invoke(Http\Request\RequestInterface $request, Closure $closure): mixed
	{
		if (!$request->body() instanceof Http\Body\KeyValue\BodyInterface) {
			throw new MiddlewareException([]);
		}

		$errors = [];

		foreach ($this->inputs() as $input) {
			foreach ($input->values($request) as $key => $value) {
				foreach ($input->rules() as $rule) {
					if (!$rule($value)) {
						$errors[$key][] = $rule->error();
					}
				}
			}
		}

		if ($errors) {
			throw new MiddlewareException($errors);
		}

		return $closure($request);
	}

	protected function inputs(): array
	{
		return $this->input ? [
			...$this->inputs,
			$this->input,
		] : $this->inputs;
	}
}
