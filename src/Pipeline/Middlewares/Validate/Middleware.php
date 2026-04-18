<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;
use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Fields\Field;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;
use Hoo\WordPressPluginFramework\Http\Request\Validator\ValidatorInterface;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __construct(
		protected ValidatorInterface $validator,
	) {
	}

	public function post(string $name): self
	{
		return new self($this->validator->withField(new Field($name, Method::Post)));
	}

	public function get(string $name): self
	{
		return new self($this->validator->withField(new Field($name, Method::Get)));
	}

	public function int(): self
	{
		return new self($this->validator->withRule(new Rules\Int\Rule()));
	}

	public function float(): self
	{
		return new self($this->validator->withRule(new Rules\Float\Rule()));
	}

	public function string(): self
	{
		return new self($this->validator->withRule(new Rules\String\Rule()));
	}

	public function in(array $allowed): self
	{
		return new self($this->validator->withRule(new Rules\InArray\Rule($allowed)));
	}

	public function rule(RuleInterface $rule): self
	{
		return new self($this->validator->withRule($rule));
	}

	public function __invoke(callable $callable): mixed
	{
		($this->validator)();

		return $callable();
	}
}
