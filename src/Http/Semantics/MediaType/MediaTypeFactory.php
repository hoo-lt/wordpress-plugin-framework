<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\ParametersFactoryInterface,
	Http\Semantics\Token\Token,
	Http\Semantics\Token\TokenInterface,
};

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $mediaType): MediaTypeInterface
	{
		[
			$typeSubtype,
			$parameters,
		] = array_pad(explode(';', trim($mediaType), 2), 2, null);

		[
			$type,
			$subtype,
		] = array_pad(explode('/', trim($typeSubtype), 2), 2, '');

		return new MediaType(
			$this->createType($type),
			$this->createSubtype($subtype),
			$this->parametersFactory->create($parameters ?? ''),
		);
	}

	public function tryCreate(?string $mediaType): ?MediaTypeInterface
	{
		if ($mediaType === null) {
			return null;
		}

		return $this->create($mediaType);
	}

	protected function createType(string $type): TokenInterface
	{
		return new Token(
			strtolower($type),
		);
	}

	protected function createSubtype(string $subtype): TokenInterface
	{
		return new Token(
			strtolower($subtype),
		);
	}
}
