<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\Query\DecoderInterface,
	Http\Encoders\Query\EncoderInterface,
	Http\Normalizers\NormalizersInterface,
};

readonly class QueryFactory implements QueryFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected DecoderInterface $decoder,
		protected EncoderInterface $encoder,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function create(array $query): QueryInterface
	{
		return new Query($this->accessor, $this->encoder, $query);
	}

	public function createFromEncoded(string $query): QueryInterface
	{
		$query = $this->decoder->decode($query);

		return $this->create($query);
	}
}