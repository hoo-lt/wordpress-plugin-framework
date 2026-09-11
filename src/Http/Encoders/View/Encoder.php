<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\View;

use Hoo\WordPressPluginFramework\{
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Renderer\RendererInterface,
	View\ViewInterface,
};

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected RendererInterface $renderer,
		protected MediaTypeInterface $mediaType = new MediaType('text', 'html'),
	) {
		if (!$this->encodesMediaType($mediaType)) {
			throw new EncoderException('does not encode this media type');
		}
	}

	public function mediaType(): MediaTypeInterface
	{
		return $this->mediaType;
	}

	public function withMediaType(MediaTypeInterface $mediaType): static
	{
		return new static($this->renderer, $mediaType);
	}

	public function encode(mixed $decoded): string
	{
		if (!$this->encodesType($decoded)) {
			throw new EncoderException('does not encode');
		}

		return $this->renderer->render($decoded);
	}

	public function encodesType(mixed $decoded): bool
	{
		if (!$decoded instanceof ViewInterface) {
			return false;
		}

		return true;
	}

	public function encodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return $mediaType->type() === 'text' && $mediaType->subtype() === 'html';
	}
}
