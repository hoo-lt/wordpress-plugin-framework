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
			'application/x-www-form-urlencoded' => $this->fromForm($body),
			'application/json' => $this->fromJson($body),
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

	protected function fromForm(string $body): BodyInterface
	{
		$keyValueBody = $this->formCoder->decode($body);

		return is_array($keyValueBody) ? new KeyValue\Body(
			$this->keyValueHelper,
			$this->formCoder,
			$keyValueBody
		) : new Body($body);
	}

	protected function fromJson(string $body): BodyInterface
	{
		$keyValueBody = $this->jsonCoder->decode($body);

		return is_array($keyValueBody) ? new KeyValue\Body(
			$this->keyValueHelper,
			$this->jsonCoder,
			$keyValueBody
		) : new Body($body);
	}
}
