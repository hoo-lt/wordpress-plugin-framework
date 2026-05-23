<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};
use Throwable;

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected Helpers\KeyValue\HelperInterface $keyValueHelper,
		protected Http\Coders\CoderFactoryInterface $coderFactory,
		protected Http\Server\ServerInterface $server,
	) {
	}

	public function from(?string $contentType, mixed $body): BodyInterface
	{
		if ($contentType === null) {
			return new Body($body);
		}

		$coder = $this->coderFactory->coder($contentType);
		if ($coder === null) {
			return new Body($body);
		}

		if (is_array($body)) {
			return new KeyValue\Body(
				$this->keyValueHelper,
				$coder,
				$body,
			);
		}

		$decodedBody = $coder->decode($body);
		if (is_array($decodedBody)) {
			return new KeyValue\Body(
				$this->keyValueHelper,
				$coder,
				$decodedBody,
			);
		}

		return new Body($body);
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

	public function fromException(?string $contentType, Http\Exceptions\Exception $exception): BodyInterface
	{
		$body = $exception->getBody();
		if (is_array($body)) {
			$body['message'] = $exception->getMessage();
			$body['code'] = $exception->getCode();
		}

		return $this->from(
			$contentType,
			$body,
		);
	}

	public function fromThrowable(?string $contentType, Throwable $throwable): BodyInterface
	{
		return $this->from(
			$contentType,
			[
				'message' => $throwable->getMessage(),
				'code' => $throwable->getCode(),
			]
		);
	}
}
