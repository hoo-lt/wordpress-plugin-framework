<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\ParametersFactoryInterface,
	Http\Semantics\Semantics,
	Http\Semantics\Subtype\Subtype,
	Http\Semantics\Type\Type,
};

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	protected const ESSENCE = '/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '/';   // media-type essence: type "/" subtype — a field-value has no surrounding OWS (RFC 9110 §5.5)

	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $mediaType): MediaTypeInterface
	{
		preg_match(self::ESSENCE, $mediaType, $essence);

		return new MediaType(
			new Type(strtolower($essence['type'] ?? '')),         // type is case-insensitive
			new Subtype(strtolower($essence['subtype'] ?? '')),   // subtype is case-insensitive
			$this->parametersFactory->create($mediaType),
		);
	}

	public function tryCreate(?string $mediaType): ?MediaTypeInterface
	{
		return $mediaType === null ? null : $this->create($mediaType);
	}
}
