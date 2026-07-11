<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameter\ParameterFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	// One pass tokenizes the field value into classified facets (RFC 9110 §8.3.1):
	//   essence   — type "/" subtype, only at the very start (a field-value has no leading OWS, §5.5);
	//   parameter — a media type parameter, captured bare — its framing consumed by the scan, its
	//               quoted value consumed whole, so the scan can never resume inside quoted data.
	// A media-type has no weight: a "q" here is an ordinary parameter (weight belongs to Accept, §12.5.1).
	protected const MEDIA_TYPE = '/'
		. '\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE
		. '|' . Semantics::PARAMETERS
		. '/';

	public function __construct(
		protected ParameterFactoryInterface $parameterFactory,
	) {
	}

	public function create(string $mediaType): MediaTypeInterface
	{
		preg_match_all(self::MEDIA_TYPE, $mediaType, $matched, PREG_UNMATCHED_AS_NULL);

		$parameters = array_map(
			$this->parameterFactory->create(...),
			array_values(array_filter($matched['parameter'], static fn (?string $parameter): bool => $parameter !== null)),   // null = the parameter branch didn't fire on this row (it was the essence); reindexed to a list
		);

		return new MediaType(
			strtolower(implode($matched['type'])),        // essence fires at most once (\A-anchored) — its column folds to it, or to empty-but-present
			strtolower(implode($matched['subtype'])),
			$parameters,
		);
	}

	public function tryCreate(?string $mediaType): ?MediaTypeInterface
	{
		return $mediaType === null ? null : $this->create($mediaType);
	}
}
