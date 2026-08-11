<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\{
	Http\Exceptions\NotAcceptable\Exception as NotAcceptableException,
	Http\Semantics\Accept\AcceptInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

readonly class Negotiator implements NegotiatorInterface
{
	public function __construct(
		protected MediaTypeInterface $mediaType,
		protected array $coders,
	) {
		if (!$this->codes($this->mediaType)) {
			throw new NegotiatorException('no coder for default media type');
		}
	}

	public function negotiate(?AcceptInterface $accept): MediaTypeInterface
	{
		$mediaType = $this->tryNegotiate($accept);
		if ($mediaType === null) {
			throw new NotAcceptableException('no acceptable representation', 'negotiator_error');
		}

		return $mediaType;
	}

	public function tryNegotiate(?AcceptInterface $accept): ?MediaTypeInterface
	{
		$mediaTypes = $this->mediaTypes();

		if ($accept === null) {
			return $mediaTypes[0];
		}

		foreach ($accept->mediaTypes() as $mediaType) {
			if ($this->codes($mediaType)) {
				$mediaTypes[] = $mediaType;
			}
		}

		$mediaTypes = array_filter($mediaTypes, fn($mediaType) => $accept->q($mediaType) > 0);

		usort($mediaTypes, fn($a, $b) => $accept->q($b) <=> $accept->q($a));

		return $mediaTypes[0] ?? null;
	}

	protected function mediaTypes(): array
	{
		$mediaTypes = [$this->mediaType];

		foreach ($this->coders as $coder) {
			foreach ($coder->mediaTypes() as $mediaType) {
				$mediaTypes[] = $mediaType;
			}
		}

		return $mediaTypes;
	}

	protected function codes(MediaTypeInterface $mediaType): bool
	{
		foreach ($this->coders as $coder) {
			if ($coder->codes($mediaType)) {
				return true;
			}
		}

		return false;
	}
}
