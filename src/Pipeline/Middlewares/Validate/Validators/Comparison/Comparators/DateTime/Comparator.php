<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\DateTime;

use DateTime;
use DateTimeInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	public function __construct(
		protected string $format,
	) {
	}

	protected function normalize(mixed $value): ?DateTimeInterface
	{
		if (!is_string($value)) {
			return null;
		}

		$dateTime = DateTime::createFromFormat($this->format, $value);
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
