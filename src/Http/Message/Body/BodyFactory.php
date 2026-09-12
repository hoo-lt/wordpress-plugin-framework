<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\DecoderInterface,
	Http\Encoders\EncoderInterface,
	Http\Decoders\DecodersInterface,
	Http\Encoders\EncodersInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Normalizers\NormalizersInterface,
};
use stdClass;

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected MediaTypeFactoryInterface $mediaTypeFactory,
		protected DecodersInterface $decoders,
		protected EncodersInterface $encoders,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function createBody(MediaTypeInterface|string $contentType, mixed $body): BodyInterface
	{
		$encoder = $this->encoder($contentType, $body);

		if (is_array($body) || $body instanceof stdClass) {
			return new Accessor\Body($this->accessor, $encoder, $body);
		}

		return new Body($encoder, $body);
	}

	public function createBodies(mixed $body): array
	{
		$bodies = [];

		$encoders = $this->encoders($body);
		foreach ($encoders as $encoder) {
			$bodies[] = is_array($body) || $body instanceof stdClass
				? new Accessor\Body($this->accessor, $encoder, $body)
				: new Body($encoder, $body);
		}

		return $bodies;
	}

	public function createBodyFromEncoded(string $contentType, string $body): BodyInterface
	{
		$body = $this->decode($contentType, $body);

		return $this->createBody($contentType, $body);
	}

	public function createBodyFromUnnormalized(MediaTypeInterface|string $contentType, mixed $body): BodyInterface
	{
		$body = $this->normalizers->normalize($body);

		return $this->createBody($contentType, $body);
	}

	public function createBodiesFromUnnormalized(mixed $body): array
	{
		$body = $this->normalizers->normalize($body);

		return $this->createBodies($body);
	}

	protected function encoders(mixed $body): EncodersInterface
	{
		return $this->encoders
			->filterByType($body);
	}

	protected function encoder(MediaTypeInterface|string $contentType, mixed $body): EncoderInterface
	{
		$mediaType = $contentType instanceof MediaTypeInterface ? $contentType : $this->mediaTypeFactory->create($contentType);

		return $this->encoders
			->filterByType($body)
			->filterByMediaType($mediaType)
			->mapMediaType($mediaType)
			->first();
	}

	protected function decoder(string $contentType): DecoderInterface
	{
		$mediaType = $this->mediaTypeFactory->create($contentType);

		return $this->decoders
			->filterByMediaType($mediaType)
			->mapMediaType($mediaType)
			->first();
	}

	protected function decode(string $contentType, string $body): mixed
	{
		$decoder = $this->decoder($contentType);
		return $decoder->decode($body);
	}
}
