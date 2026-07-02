<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiation;

use Hoo\WordPressPluginFramework\Http\Coders\CoderInterface;

readonly class ContentNegotiator implements ContentNegotiatorInterface
{
	/**
	 * @var string[]
	 */
	private array $producible;

	public function __construct(
		CoderInterface ...$coders,
	) {
		$producible = [];
		foreach ($coders as $coder) {
			$producible = array_merge($producible, $coder->produces());
		}

		$this->producible = $producible;
	}

	public function negotiate(?string $accept): ?string
	{
		foreach ($this->parse($accept) as $range) {
			foreach ($this->producible as $mediaType) {
				if ($range->q > 0 && $range->accepts($mediaType)) {
					return $mediaType;
				}
			}
		}

		return null;
	}

	/**
	 * @return MediaRange[]
	 */
	private function parse(?string $accept): array
	{
		$accept = trim((string) $accept);
		if ($accept === '') {
			$accept = '*/*';
		}

		$ranges = [];
		foreach (explode(',', $accept) as $order => $part) {
			$parameters = explode(';', trim($part));
			$mediaType = strtolower(trim(array_shift($parameters)));

			[
				$type,
				$subtype,
			] = array_pad(explode('/', $mediaType, 2), 2, '*');

			$q = 1.0;
			foreach ($parameters as $parameter) {
				[
					$key,
					$value,
				] = array_pad(explode('=', trim($parameter), 2), 2, '');

				if (strtolower(trim($key)) === 'q') {
					$q = (float) $value;
				}
			}

			$ranges[] = new MediaRange($type, $subtype ?: '*', $q, $order);
		}

		usort(
			$ranges,
			fn (MediaRange $a, MediaRange $b): int =>
				[$b->q, $b->specificity(), $a->order] <=> [$a->q, $a->specificity(), $b->order],
		);

		return $ranges;
	}
}
