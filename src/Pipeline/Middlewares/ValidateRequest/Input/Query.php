<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Input;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Query implements InputInterface
{
	public function __construct(
		private string $key,
		private array $rules = [],
	) {
	}

	public function key(): string
	{
		return $this->key;
	}

	public function values(RequestInterface $request): array
	{
		return $request->url()->query()->values($this->key);
	}

	public function rules(): array
	{
		return $this->rules;
	}

	public function withRules(RuleInterface ...$rules): InputInterface
	{
		return new self(
			$this->key,
			[
				...$this->rules,
				...$rules
			]
		);
	}
}
