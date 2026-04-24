<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Input;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Body implements InputInterface
{
	public function __construct(
		private RequestInterface $request,
		private string $name,
		private array $rules = [],
	) {
	}

	public function key(): string
	{
		return $this->name;
	}

	public function value(): mixed
	{
		return $this->request->body()->value($this->name);
	}

	public function entries(): array
	{
		if (!str_contains($this->name, '*')) {
			return [$this->name => $this->value()];
		}

		return $this->request->bodyValues($this->name);
	}

	public function rules(): array
	{
		return $this->rules;
	}

	public function withRules(RuleInterface ...$rules): InputInterface
	{
		return new self(
			$this->request,
			$this->name,
			[
				...$this->rules,
				...$rules,
			]
		);
	}
}
