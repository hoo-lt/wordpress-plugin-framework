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

	public function post(string $name): self
	{
		return new self(
			$this->request,
			$this->inputs(),
			new Input\Post(
				$this->request,
				$name
			)
		);
	}

	public function get(string $name): self
	{
		return new self(
			$this->request,
			$this->inputs(),
			new Input\Get(
				$this->request,
				$name
			)
		);
	}

	public function int(): self
	{
		return new self(
			$this->request,
			$this->inputs,
			$this->input->withRule(
				new Rules\Int\Rule()
			)
		);
	}

	public function float(): self
	{
		return new self(
			$this->request,
			$this->inputs,
			$this->input->withRule(
				new Rules\Float\Rule()
			)
		);
	}

	public function string(): self
	{
		return new self(
			$this->request,
			$this->inputs,
			$this->input->withRule(
				new Rules\String\Rule()
			)
		);
	}

	public function rule(RuleInterface $rule): self
	{
		return new self(
			$this->request,
			$this->inputs,
			$this->input->withRule(
				$rule
			)
		);
	}

	protected function inputs(): array
	{
		return $this->input ? [
			...$this->inputs,
			$this->input
		] : $this->inputs;
	}

	public function __invoke(callable $callable): mixed
	{
		$errors = [];

		foreach ($this->inputs() as $input) {
			foreach ($input->rules() as $rule) {
				if (!$rule($input->value())) {
					$errors[] = "{$input->name()} {$rule->error()}";
				}
			}
		}

		if ($errors) {
			throw new MiddlewareException($errors);
		}

		return $callable();
	}
}
