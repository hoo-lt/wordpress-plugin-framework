<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison;

use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Operator\Operator,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

interface ValidatorBuilderInterface
{
	public function comparator(): ComparatorInterface;
	public function withComparator(ComparatorInterface $comparator): static;

	public function a(): KeyValueInterface;
	public function withA(KeyValueInterface $a): static;

	public function operator(): Operator;
	public function withOperator(Operator $operator): static;

	public function b(): KeyValueInterface;
	public function withB(KeyValueInterface $b): static;

	public function body(string $key): static;
	public function bodyQuery(string $key): static;
	public function query(string $key): static;
	public function header(string $key): static;
	public function route(string $key): static;

	public function equal(): static;
	public function notEqual(): static;
	public function lessThan(): static;
	public function greaterThan(): static;
	public function lessThanOrEqual(): static;
	public function greaterThanOrEqual(): static;

	public function toBody(string $key): static;
	public function toBodyQuery(string $key): static;
	public function toQuery(string $key): static;
	public function toHeader(string $key): static;
	public function toRoute(string $key): static;

	public function build(): ValidatorInterface;
}
