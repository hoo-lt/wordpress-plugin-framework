<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

readonly class CoderFactory implements CoderFactoryInterface
{
	public function from(string $mediaType): CoderInterface
	{
		$coder = $this->tryFrom($mediaType);
		if ($coder === null) {
			throw new CoderFactoryException('failed to create coder');
		}

		return $coder;
	}

	public function tryFrom(string $mediaType): ?CoderInterface
	{
		return match ($mediaType) {
			'application/x-www-form-urlencoded' => new Form\Coder(),
			'application/json' => new Json\Coder(),
			default => null,
		};
	}
}