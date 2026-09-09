<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\DecodersInterface,
	Http\Encoders\EncodersInterface,
	Http\Message\Headers\ContentType\ContentTypeFactoryInterface,
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
		$mediaType = $this->contentTypeFactory->create($contentType)->mediaType();

		$encoder = $this->encoders
			->filterByType($body)
			->filterByMediaType($mediaType)
			->first();

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

		$encoders = $this->encoders->filterByType($body);
		foreach ($encoders as $encoder) {
			$mediaTypes = $encoder->mediaTypes();
			foreach ($mediaTypes as $mediaType) {
				$bodies[(string) $mediaType] = new Body($this->accessor, $encoder, $body);
			}
		}

		return $bodies;
	}

	protected function decode(string $contentType, mixed $body): mixed
	{
		$mediaType = $this->contentTypeFactory->create($contentType)->mediaType();

		$decoder = $this->decoders
			->filterByType($body)
			->filterByMediaType($mediaType)
			->first();

		return $decoder->decode($body);
	}

	protected function normalize(mixed $body): mixed
	{
		$normalizer = $this->normalizers->get($body);
		return $normalizer === null ? $body : $normalizer->normalize($body);
	}
}
