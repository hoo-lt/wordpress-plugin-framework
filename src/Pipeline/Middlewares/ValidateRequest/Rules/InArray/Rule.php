<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\InArray;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __construct(
		protected array $allowed,
	) {
	}

	public function __invoke(mixed $value): bool
	{
		return in_array($value, $this->allowed, true);
	}

	public function error(): string
	{
		return 'must be one of: ' . implode(', ', $this->allowed);
	}
}
