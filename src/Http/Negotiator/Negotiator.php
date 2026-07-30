<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

use Hoo\WordPressPluginFramework\{
	Http\Exceptions\NotAcceptable\Exception as NotAcceptableException,
	Http\Semantics\Accept\AcceptFactoryInterface,
	Http\Semantics\Accept\AcceptInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeFactoryInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

readonly class Negotiator implements NegotiatorInterface
{
	protected MediaTypeInterface $mediaType;

	public function __construct(
		protected AcceptFactoryInterface $acceptFactory,
		MediaTypeFactoryInterface $mediaTypeFactory,
		string $mediaType,
		protected array $coders,
	) {
		$this->mediaType = $mediaTypeFactory->create($mediaType);
		if (!$this->codes($this->mediaType, $this->coders)) {
			throw new NegotiatorException('no coder for default media type');
		}
	}

	public function negotiate(?string $accept, mixed $decoded): MediaTypeInterface
	{
		$mediaType = $this->tryNegotiate($accept, $decoded);
		if ($mediaType === null) {
			throw new NotAcceptableException('no acceptable representation', 'negotiator_error');
		}

		return $mediaType;
	}

	public function tryNegotiate(?string $accept, mixed $decoded): ?MediaTypeInterface
	{
		$coders = $this->coders($decoded);
		if ($coders === []) {
			throw new NegotiatorException('no coder encodes the payload');
		}

		$accept = $this->acceptFactory->tryCreate($accept);

		return $this->mediaType($accept, $coders);
	}

	protected function coders(mixed $decoded): array
	{
		return array_filter($this->coders, fn($coder) => $coder->encodes($decoded));
	}

	protected function mediaType(?AcceptInterface $accept, array $coders): ?MediaTypeInterface
	{
		$mediaTypes = [];

		if ($this->codes($this->mediaType, $coders)) {
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
