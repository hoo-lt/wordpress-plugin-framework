<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\{
	KeyValues\KeyValuesInterface,
	Comparators\ComparatorInterface,
	Comparators\Operator,
	Comparators\Numeric\Comparator as NumericComparator,
	Comparators\Date\Comparator as DateComparator,
	Comparators\String\Comparator as StringComparator,
	Validators\ValidatorInterface,
};

readonly class Builder implements BuilderInterface
{
	public function __construct(
		protected Closure $keyValue,                  // fn(string $key): KeyValuesInterface — binds the source
		protected ?KeyValuesInterface $left = null,
		protected ?ComparatorInterface $comparator = null,
		protected ?Operator $operator = null,
		protected ?KeyValuesInterface $right = null,
	) {
	}

	public function key(string $key): static
	{
		return $this->with(left: ($this->keyValue)($key));
	}
	public function against(string $key): static
	{
		return $this->with(right: ($this->keyValue)($key));
	}

	public function numeric(): static
	{
		return $this->with(comparator: new NumericComparator());
	}
	public function date(string $format): static
	{
		return $this->with(comparator: new DateComparator($format));
	}
	public function string(): static
	{
		return $this->with(comparator: new StringComparator());
	}

	public function identical(): static
	{
		return $this->with(operator: Operator::Identical);
	}
	public function notIdentical(): static
	{
		return $this->with(operator: Operator::NotIdentical);
	}
	public function lessThan(): static
	{
		return $this->with(operator: Operator::LessThan);
	}
	public function lessThanOrEqual(): static
	{
		return $this->with(operator: Operator::LessThanOrEqual);
	}
	public function greaterThan(): static
	{
		return $this->with(operator: Operator::GreaterThan);
	}
	public function greaterThanOrEqual(): static
	{
		return $this->with(operator: Operator::GreaterThanOrEqual);
	}

	public function build(): ValidatorInterface
	{
		if (
			$this->left === null ||
			$this->comparator === null ||
			$this->operator === null ||
			$this->right === null
		) {
			throw new BuilderException('comparison is incomplete');
		}

		return new Validator($this->left, $this->comparator, $this->operator, $this->right);
	}

	private function with(
		?KeyValuesInterface $left = null,
		?ComparatorInterface $comparator = null,
		?Operator $operator = null,
		?KeyValuesInterface $right = null,
	): static {
		return new static(
			$this->keyValue,
			$left ?? $this->left,
			$comparator ?? $this->comparator,
			$operator ?? $this->operator,
			$right ?? $this->right,
		);
	}
}
