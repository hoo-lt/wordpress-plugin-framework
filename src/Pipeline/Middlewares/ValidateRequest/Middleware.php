<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Input\InputInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __construct(
		protected RequestInterface $request,
		protected array $inputs = [],
		protected ?InputInterface $currentInput = null,
	) {
	}

	public function post(string $name): self
	{
		return new self($this->request, $this->resolvedInputs(), new Input\Post($this->request, $name));
	}

	public function get(string $name): self
	{
		return new self($this->request, $this->resolvedInputs(), new Input\Get($this->request, $name));
	}

	public function int(): self
	{
		return new self($this->request, $this->inputs, $this->currentInput->withRule(new Rules\Int\Rule()));
	}

	public function float(): self
	{
		return new self($this->request, $this->inputs, $this->currentInput->withRule(new Rules\Float\Rule()));
	}

	public function string(): self
	{
		return new self($this->request, $this->inputs, $this->currentInput->withRule(new Rules\String\Rule()));
	}

	public function in(array $allowed): self
	{
		return new self($this->request, $this->inputs, $this->currentInput->withRule(new Rules\InArray\Rule($allowed)));
	}

	public function rule(RuleInterface $rule): self
	{
		return new self($this->request, $this->inputs, $this->currentInput->withRule($rule));
	}

	protected function resolvedInputs(): array
	{
		return $this->currentInput
			? [...$this->inputs, $this->currentInput]
			: $this->inputs;
	}

	public function __invoke(callable $callable): mixed
	{
		$errors = [];

		foreach ($this->resolvedInputs() as $input) {
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
