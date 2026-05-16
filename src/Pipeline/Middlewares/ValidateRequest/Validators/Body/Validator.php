<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Validators\Body;

use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
	Pipeline\Middlewares\ValidateRequest\Errors\Errors,
};

readonly class Validator implements Pipeline\Middlewares\ValidateRequest\Validators\ValidatorInterface
{
	public function __construct(
		protected string $key,
		protected array $rules = [],
	) {
	}

	public function key(): string
	{
		return $this->key;
	}

	public function values(Http\Request\RequestInterface $request): array
	{
		return $request->body() instanceof Http\KeyValue\KeyValueInterface ? $request->body()->values($this->key) : [];
	}

	public function rules(): array
	{
		return $this->rules;
	}

	public function withRules(Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface ...$rules): static
	{
		return new self(
			$this->key,
			[
				...$this->rules,
				...$rules,
			]
		);
	}
}
