<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

readonly class CoderFactory implements CoderFactoryInterface
{
	public function create(string $mediaType): CoderInterface
	{
		$coder = $this->tryCreate($mediaType);
		if ($coder === null) {
			throw new CoderFactoryException('failed to create coder');
		}

		return $coder;
	}

	public function tryCreate(string $mediaType): ?CoderInterface
	{
		return match ($mediaType) {
			'application/x-www-form-urlencoded' => new Form\Coder(),
			'application/json' => new Json\Coder(),
			default => null,
		};
	}
}