<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Comparators\ComparatorInterface,
	Pipeline\Middlewares\Validate\Operator\Operator,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected KeyValueInterface $a,
		protected KeyValueInterface $b,
		protected ComparatorInterface $comparator,
		protected Operator $operator,
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$comparison = $this->comparator->compare(
			$this->a->value($request),
			$this->b->value($request),
		);

		if ($comparison === null) {
			$closure($this->a->key(), "is not comparable to {$this->b->key()}");
		} else {
			if (!$this->operator->result($comparison)) {
				$closure($this->a->key(), "{$this->operator->message()} {$this->b->key()}");
			}
		}
	}
}
