<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\View;

use Hoo\WordPressPluginFramework\{
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Renderer\RendererInterface,
	View\ViewInterface,
};

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected RendererInterface $renderer,
	) {
	}

	public function mediaTypes(): array
	{
		return [];
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
		return true;
	}
}
