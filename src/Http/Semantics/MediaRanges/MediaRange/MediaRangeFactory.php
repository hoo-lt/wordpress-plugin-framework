<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\ParametersFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	// media-range = type "/" subtype *( OWS ";" OWS parameter ) [ weight ] — RFC 9110 §12.5.1 (no accept-ext ⇒ weight anchored to the tail); element arrives OWS-free from the splitter
	protected const MEDIA_RANGE = '/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '(?<parameters>.*?)(?:' . Semantics::WEIGHT . ')?\z/';

	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $mediaRange): MediaRangeInterface
	{
		preg_match(self::MEDIA_RANGE, $mediaRange, $matched);

		return new MediaRange(
			strtolower($matched['type'] ?? ''),         // type is case-insensitive; no essence → empty-but-present
			strtolower($matched['subtype'] ?? ''),      // subtype is case-insensitive; no essence → empty-but-present
			$this->parametersFactory->create($matched['parameters'] ?? ''),        // parameters tail, weight already excluded by the grammar
			($matched['qvalue'] ?? '') === '' ? null : (float) $matched['qvalue'], // strict ===: '' (absent q) → null, never truthiness
		);
	}

	public function tryCreate(?string $mediaRange): ?MediaRangeInterface
	{
		return $mediaRange === null ? null : $this->create($mediaRange);
	}
}
