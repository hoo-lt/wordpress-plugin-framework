<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\DecodersInterface,
	Http\Encoders\EncodersInterface,
	Http\Normalizers\NormalizersInterface,
};

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected DecodersInterface $decoders,
		protected EncodersInterface $encoders,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function create(string $contentType, mixed $body): BodyInterface
	{
		$encoder = $this->encoders->get($contentType, $body);

		return new Body($this->accessor, $encoder, $body);
	}

	public function createFromEncoded(string $contentType, mixed $body): BodyInterface
	{
		$body = $this->decode($contentType, $body);

		return $this->create($contentType, $body);
	}

	public function createFromUnnormalized(string $contentType, mixed $body): BodyInterface
	{
		$body = $this->normalize($body);

		return $this->create($contentType, $body);
	}

	protected function decode(string $contentType, mixed $body): mixed
	{
		$decoder = $this->decoders->get($contentType, $body);
		return $decoder->decode($body);
	}

	protected function normalize(mixed $body): mixed
	{
		$normalizer = $this->normalizers->get($body);
		return $normalizer === null ? $body : $normalizer->normalize($body);
	}
}
