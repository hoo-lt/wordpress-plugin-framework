<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Input;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\RuleInterface;

readonly class Post implements InputInterface
{
	public function __construct(
		private RequestInterface $request,
		private string $name,
		private array $rules = [],
	) {
	}

	public function name(): string
	{
		return $this->name;
	}

	public function value(): mixed
	{
		return $this->request->post($this->name);
	}

	public function rules(): array
	{
		return $this->rules;
	}

	public function withRule(RuleInterface $rule): InputInterface
	{
		return new self($this->request, $this->name, [...$this->rules, $rule]);
	}
}
