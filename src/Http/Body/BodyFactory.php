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

		$coder = $this->coderFactory->from($contentType);
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

		/*
		$decodedBody = $coder->decode($body);
		if (is_array($decodedBody)) {
			return new KeyValue\Body(
				$this->keyValueHelper,
				$coder,
				$decodedBody,
			);
		}
		*/

		return new Body($coder->encode($body));
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

	public function fromException(Http\Request\RequestInterface $request, Http\Exceptions\Exception $exception): ?BodyInterface
	{
		return null;
	}

	public function fromThrowable(Http\Request\RequestInterface $request, Throwable $throwable): ?BodyInterface
	{
		return null;
	}
}
