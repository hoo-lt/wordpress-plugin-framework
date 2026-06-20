<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime;

use DateTime;
use DateTimeZone;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	public function __construct(
		protected string $format,
		protected ?DateTimeZone $timezone = null,
	) {
	}

	protected function normalize(mixed $value): ?DateTime
	{
		if (!is_string($value)) {
			return null;
		}

		$dateTime = DateTime::createFromFormat($this->format, $value, $this->timezone);
		if ($dateTime === false) {
			return null;
		}

		$lastErrors = DateTime::getLastErrors();
		if (
			$lastErrors !== false &&
			(
				$lastErrors['warning_count'] > 0 ||
				$lastErrors['error_count'] > 0
			)
		) {
			return null;
		}

		return $dateTime;
	}
}
