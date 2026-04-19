<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
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
		protected RequestInterface $request,
		protected array $inputs = [],
		protected ?InputInterface $input = null,
	) {
	}

	public function withInput(InputInterface $input): self
	{
		return new self(
			$this->request,
			$this->inputs(),
			$input,
		);
	}

	public function post(string $key): self
	{
		return $this->withInput(
			new Input\Post(
				$this->request,
				$key,
			),
		);
	}

	public function get(string $key): self
	{
		return $this->withInput(
			new Input\Get(
				$this->request,
				$key,
			),
		);
	}

	public function withRules(RuleInterface ...$rules): self
	{
		return new self(
			$this->request,
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

	public function __invoke(callable $callable): mixed
	{
		$errors = [];

		foreach ($this->inputs() as $input) {
			foreach ($input->rules() as $rule) {
				if (!$rule($input->value())) {
					$errors[] = "{$input->key()} {$rule->error()}";
				}
			}
		}

		if ($errors) {
			throw new MiddlewareException($errors);
		}

		return $callable();
	}

	protected function inputs(): array
	{
		return $this->input ? [
			...$this->inputs,
			$this->input
		] : $this->inputs;
	}
}
