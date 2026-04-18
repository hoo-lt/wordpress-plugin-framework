<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Input;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Get implements InputInterface
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
		return $this->request->get($this->name);
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
				...$rules
			]
		);
	}
}
