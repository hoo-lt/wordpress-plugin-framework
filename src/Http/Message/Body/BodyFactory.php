<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\DecoderInterface,
	Http\Decoders\DecodersInterface,
	Http\Encoders\EncodersInterface,
	Http\Encoders\Query\EncoderInterface,
	Http\Message\Headers\ContentType\ContentTypeFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Normalizers\NormalizersInterface,
};

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected ContentTypeFactoryInterface $contentTypeFactory,
		protected DecodersInterface $decoders,
		protected EncodersInterface $encoders,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function createBody(string $contentType, mixed $body): BodyInterface
	{
		$encoder = $this->encoder($contentType, $body);

		return new Body($this->accessor, $encoder, $body);
	}

	public function createBodyFromEncoded(string $contentType, mixed $body): BodyInterface
	{
		$body = $this->decode($contentType, $body);

		return $this->createBody($contentType, $body);
	}

	public function createBodyFromUnnormalized(string $contentType, mixed $body): BodyInterface
	{
		$body = $this->normalize($body);

		return $this->createBody($contentType, $body);
	}

	public function createBodiesFromUnnormalized(mixed $body): array
	{
		$body = $this->normalize($body);

		$bodies = [];

		$encoders = $this->encoders($body);
		foreach ($encoders as $encoder) {
			$mediaTypes = $encoder->mediaTypes();
			foreach ($mediaTypes as $mediaType) {
				$bodies[(string) $mediaType] = new Body($this->accessor, $encoder, $body);
			}
		}

		return $bodies;
	}

	protected function mediaType(string $contentType): MediaTypeInterface
	{
		return $this->contentTypeFactory->create($contentType)->mediaType();
	}

	protected function encoders(mixed $body): EncodersInterface
	{
		return $this->encoders
			->filterByType($body);
	}

	protected function encoder(string $contentType, mixed $body): EncoderInterface
	{
		$mediaType = $this->mediaType($contentType);

		return $this->encoders
			->filterByType($body)
			->filterByMediaType($mediaType)
			->first();
	}

	protected function decoder(string $contentType, mixed $body): DecoderInterface
	{
		$mediaType = $this->mediaType($contentType);

		return $this->decoders
			->filterByType($body)
			->filterByMediaType($mediaType)
			->first();
	}

	protected function decode(string $contentType, mixed $body): mixed
	{
		$decoder = $this->decoder($contentType, $body);
		return $decoder->decode($body);
	}

	protected function normalize(mixed $body): mixed
	{
		$normalizer = $this->normalizers->get($body);
		return $normalizer === null ? $body : $normalizer->normalize($body);
	}
}
