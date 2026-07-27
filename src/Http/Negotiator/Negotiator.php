<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\AcceptInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

readonly class Negotiator implements NegotiatorInterface
{
	public function __construct(
		protected array $coders,
		protected ?MediaTypeInterface $mediaType = null,
	) {
		if ($this->mediaType === null) {
			return;
		}

		if (!$this->codes($this->mediaType, $this->coders)) {
			throw new NegotiatorException('no coder for default media type');
		}
	}

	public function withMediaType(MediaTypeInterface $mediaType): static
	{
		return new static($this->coders, $mediaType);
	}

	public function withoutMediaType(): static
	{
		return new static($this->coders, null);
	}

	public function negotiate(?AcceptInterface $accept, mixed $decoded): MediaTypeInterface
	{
		$coders = $this->coders($decoded);
		if ($coders === []) {
			throw new NegotiatorException('no coder encodes the payload');
		}

		$mediaType = $this->mediaType($accept, $coders);
		if ($mediaType === null) {
			throw new NotAcceptableException('no acceptable representation');
		}

		return $mediaType;
	}

	protected function coders(mixed $decoded): array
	{
		return array_filter($this->coders, fn($coder) => $coder->encodes($decoded));
	}

	protected function mediaType(?AcceptInterface $accept, array $coders): ?MediaTypeInterface
	{
		$mediaTypes = [];

		if (
			$this->mediaType !== null &&
			$this->codes($this->mediaType, $coders)
		) {
			$mediaTypes[] = $this->mediaType;
		}

		foreach ($coders as $coder) {
			foreach ($coder->mediaTypes() as $mediaType) {
				$mediaTypes[] = $mediaType;
			}
		}

		if ($accept === null) {
			return $mediaTypes[0] ?? null;
		}

		foreach ($accept->mediaTypes() as $mediaType) {
			if ($this->codes($mediaType, $coders)) {
				$mediaTypes[] = $mediaType;
			}
		}

		$mediaTypes = array_filter($mediaTypes, fn($mediaType) => $accept->q($mediaType) > 0);

		usort($mediaTypes, fn($a, $b) => $accept->q($b) <=> $accept->q($a));

		return $mediaTypes[0] ?? null;
	}

	protected function codes(MediaTypeInterface $mediaType, array $coders): bool
	{
		foreach ($coders as $coder) {
			if ($coder->codes($mediaType)) {
				return true;
			}
		}

		return false;
	}
}