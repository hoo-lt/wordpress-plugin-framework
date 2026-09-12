<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\Query\DecoderInterface,
	Http\Encoders\Query\EncoderInterface,
	Http\Normalizers\NormalizersInterface,
};
use stdClass;

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected DecoderInterface $decoder,
		protected EncoderInterface $encoder,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function create(array|stdClass $query): QueryInterface
	{
		return new Query($this->accessor, $this->encoder, $query);
	}

	public function createFromEncoded(string $query): QueryInterface
	{
		$query = $this->decoder->decode($query);

		return $this->create($query);
	}

	public function createFromUnnormalized(mixed $query): QueryInterface
	{
		$query = $this->normalizers->normalize($query);

		if (!is_array($query) && !$query instanceof stdClass) {
			throw new QueryFactoryException('query must be an array or an object');
		}

		return $this->create($query);
	}
}
