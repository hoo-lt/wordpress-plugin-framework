<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected Helpers\KeyValue\HelperInterface $keyValueHelper,
		protected Http\Coders\Form\CoderInterface $formCoder,
		protected Http\Coders\Json\CoderInterface $jsonCoder,
		protected Http\Server\ServerInterface $server,
	) {
	}

	public function from(?string $contentType, string $body): BodyInterface
	{
		return match ($contentType) {
			'application/x-www-form-urlencoded' => $this->fromCoder(
				$this->formCoder,
				$this->formCoder,
				$body,
			),
			'application/json' => $this->fromCoder(
				$this->jsonCoder,
				$this->jsonCoder,
				$body,
			),
			default => new Body($body),
		};
	}

	public function fromServer(): ?BodyInterface
	{
		$body = $this->server->body();
		if ($body === null) {
			return null;
		}

		$contentType = $this->server->contentType();
		return $this->from(
			$contentType,
			$body,
		);
	}

	protected function fromCoder(
		Http\Coders\DecoderInterface $decoder,
		Http\Coders\EncoderInterface $encoder,
		string $body,
	): BodyInterface {
		$keyValueBody = $decoder->decode($body);

		return is_array($keyValueBody) ? new KeyValue\Body(
			$this->keyValueHelper,
			$encoder,
			$keyValueBody
		) : new Body($body);
	}
}
