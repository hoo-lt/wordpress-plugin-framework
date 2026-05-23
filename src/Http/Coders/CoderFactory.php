<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\{
	Http,
	Json,
};

readonly class CoderFactory implements CoderFactoryInterface
{
	public function __construct(
		protected Json\JsonInterface $json,
	) {
	}

	public function from(string $mediaType): ?CoderInterface
	{
		return match ($mediaType) {
			'application/x-www-form-urlencoded' => new Http\Coders\Form\Coder(),
			'application/json' => new Http\Coders\Json\Coder(
				$this->json,
			),
			default => null,
		};
	}
}