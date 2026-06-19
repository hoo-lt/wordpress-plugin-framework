<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\ValidatorInterface;

interface BuilderInterface
{
	public function key(string $key): static;          // left

	public function numeric(): static;                 // comparator
	public function date(string $format): static;
	public function string(): static;

	public function identical(): static;               // operator
	public function notIdentical(): static;
	public function lessThan(): static;
	public function lessThanOrEqual(): static;
	public function greaterThan(): static;
	public function greaterThanOrEqual(): static;

	public function against(string $key): static;      // right

	public function build(): ValidatorInterface;
}
