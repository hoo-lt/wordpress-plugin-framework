<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\{
	Http,
	Json,
};

readonly class CoderFactory
{
	public function __construct(
		protected Json\JsonInterface $json,
	) {
	}

	public function coder(string ...$mediaTypes): ?CoderInterface
	{
		foreach ($mediaTypes as $mediaType) {
			$coder = match ($mediaType) {
				'application/x-www-form-urlencoded' => new Http\Coders\Form\Coder(),
				'application/json' => new Http\Coders\Json\Coder(
					$this->json,
				),
				default => null,
			};
			if ($coder !== null) {
				return $coder;
			}
		}

		return null;
	}
}