<?php

namespace Hoo\WordPressPluginFramework\Http;

class Response implements ResponseInterface
{
	public function __construct(
		protected readonly array $headers,
		protected readonly string $body,
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