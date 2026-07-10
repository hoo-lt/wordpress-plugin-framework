<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\ParametersFactoryInterface,
	Http\Semantics\Semantics,
	Http\Semantics\Subtype\Subtype,
	Http\Semantics\Type\Type,
	Http\Semantics\Weight\WeightFactory,
	Http\Semantics\Weight\WeightFactoryInterface,
};

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	protected const ESSENCE = '/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '/';   // media-range essence: type "/" subtype — element arrives OWS-free from the splitter

	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
		protected WeightFactoryInterface $weightFactory,
	) {
	}

	public function create(string $mediaRange): MediaRangeInterface
	{
		preg_match(self::ESSENCE, $mediaRange, $essence);

		return new MediaRange(
			new Type(strtolower($essence['type'] ?? '')),         // type is case-insensitive; no essence → empty-but-present
			new Subtype(strtolower($essence['subtype'] ?? '')),   // subtype is case-insensitive; no essence → empty-but-present
			$this->parametersFactory->create(preg_replace(WeightFactory::WEIGHT, '', $mediaRange)),   // parameters read the wire without its trailing weight
			$this->weightFactory->create($mediaRange),
		);
	}

	public function tryCreate(?string $mediaRange): ?MediaRangeInterface
	{
		return $mediaRange === null ? null : $this->create($mediaRange);
	}
}
