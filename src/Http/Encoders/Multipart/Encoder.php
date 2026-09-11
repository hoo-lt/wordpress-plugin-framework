<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Multipart;

use Hoo\WordPressPluginFramework\{
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;

readonly class Encoder implements EncoderInterface
{
	protected MediaTypeInterface $mediaType;

	public function __construct(
		MediaTypeInterface $mediaType = new MediaType('multipart', 'form-data'),
	) {
		if (!$this->encodesMediaType($mediaType)) {
			throw new EncoderException('does not encode this media type');
		}

		$this->mediaType = $mediaType->parameters()->has('boundary')
			? $mediaType
			: $mediaType->withParameters(fn($parameters) => $parameters->with('boundary', bin2hex(random_bytes(16))));
	}

	public function mediaType(): MediaTypeInterface
	{
		return $this->mediaType;
	}

	public function withMediaType(MediaTypeInterface $mediaType): static
	{
		return new static($mediaType);
	}

	public function encode(mixed $decoded): string
	{
		if (!$this->encodesType($decoded)) {
			throw new EncoderException('does not encode');
		}

		$boundary = $this->mediaType->parameters()->get('boundary');

		$encoded = '';

		foreach ($this->parts($decoded) as [$name, $value]) {
			$encoded .= "--{$boundary}\r\n";
			$encoded .= "Content-Disposition: form-data; name=\"{$name}\"\r\n";
			$encoded .= "\r\n";
			$encoded .= "{$value}\r\n";
		}

		return "{$encoded}--{$boundary}--\r\n";
	}

	public function encodesType(mixed $decoded): bool
	{
		if (!is_array($decoded) && !$decoded instanceof stdClass) {
			return false;
		}

		foreach ($decoded as $name => $value) {
			if (preg_match('/["\r\n]/', (string) $name) === 1) {
				return false;
			}

			if (is_array($value) || $value instanceof stdClass) {
				if (!$this->encodesType($value)) {
					return false;
				}

				continue;
			}

			if (is_object($value) || is_resource($value)) {
				return false;
			}
		}

		return true;
	}

	public function encodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return $mediaType->type() === 'multipart' && $mediaType->subtype() === 'form-data';
	}

	protected function parts(mixed $decoded, string $prefix = ''): array
	{
		$parts = [];

		foreach ($decoded as $name => $value) {
			$name = $prefix === '' ? (string) $name : "{$prefix}[{$name}]";

			if (is_array($value) || $value instanceof stdClass) {
				$parts = [...$parts, ...$this->parts($value, $name)];

				continue;
			}

			$parts[] = [$name, $value];
		}

		return $parts;
	}
}
