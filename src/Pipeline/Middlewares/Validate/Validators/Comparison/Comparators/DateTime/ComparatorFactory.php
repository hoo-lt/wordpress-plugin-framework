<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface;

readonly class ComparatorFactory implements ComparatorFactoryInterface
{
	public function __construct(
		protected string $format,
	) {
	}

	public function create(): ComparatorInterface
	{
		return new Comparator($this->format);
	}
}
