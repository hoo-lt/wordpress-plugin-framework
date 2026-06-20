<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime;

use DateTimeZone;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface;

readonly class ComparatorFactory implements ComparatorFactoryInterface
{
	public function __construct(
		protected string $format,
		protected ?DateTimeZone $timezone = null,
	) {
	}

	public function create(): ComparatorInterface
	{
		return new Comparator($this->format, $this->timezone);
	}
}
