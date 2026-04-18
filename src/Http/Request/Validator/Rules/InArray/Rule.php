<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\InArray;

use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleException;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __construct(
		protected array $allowed,
	) {
	}

	public function __invoke(mixed $value): void
	{
		if (!in_array($value, $this->allowed, true)) {
			throw new RuleException('must be one of: ' . implode(', ', $this->allowed));
		}
	}
}
