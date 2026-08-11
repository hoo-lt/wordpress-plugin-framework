<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\ParametersFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $mediaType): MediaTypeInterface
	{
		$mediaType = $this->tryCreate($mediaType);
		if ($mediaType === null) {
			throw new MediaTypeException('invalid media type');
		}

		return $mediaType;
	}

	public function tryCreate(?string $mediaType): ?MediaTypeInterface
	{
		if ($mediaType === null) {
			return null;
		}

		preg_match_all('/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '|' . Semantics::PARAMETERS . '/', $mediaType, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$type = '';
		$subtype = '';

		foreach ($matches as $match) {
			if ($match['type'] !== null) {
				$type = $match['type'];
			}

			if ($match['subtype'] !== null) {
				$subtype = $match['subtype'];
			}
		}

		if (
			$type === '' ||
			$subtype === '' ||
			$type === '*' ||
			$subtype === '*'
		) {
			return null;
		}

		return new MediaType($type, $subtype, $this->parametersFactory->create($mediaType));
	}
}
