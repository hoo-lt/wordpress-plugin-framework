<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class BodyFactory
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected Http\Coders\Form\Coder $formCoder,
		protected Http\Coders\Json\Coder $jsonCoder,
	) {
	}

	public function fromJson(string $json): BodyInterface
	{
		return new Json\Body(
			$this->arrayHelper,
			$this->jsonCoder,
			$this->formCoder->decode($json),
		);
	}

	public function fromForm(string $form): BodyInterface
	{
		return new Json\Body(
			$this->arrayHelper,
			$this->jsonCoder,
			$this->jsonCoder->decode($form),
		);
	}

	public function fromStream(mixed $stream): BodyInterface
	{
		return new Stream\Body(
			$stream,
		);
	}

	public function from(mixed $body, ?string $contentType): BodyInterface
	{
		return match ($contentType) {
			'application/x-www-form-urlencoded' => $this->fromForm($body),
			'application/json' => $this->fromJson($body),
			default => $this->fromStream($body),
		};
	}
}
