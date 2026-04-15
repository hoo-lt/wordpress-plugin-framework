<?php

namespace Hoo\WordPressPluginFramework\Http;

readonly class Response implements ResponseInterface
{
	public function __construct(
		protected array $headers,
		protected string $body,
	) {
	}

	public function __invoke(): void
	{
		foreach ($this->headers as $header) {
			header($header);
		}

		echo $this->body;
	}
}