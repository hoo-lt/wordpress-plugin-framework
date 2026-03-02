<?php

namespace Hoo\WordPressPluginFramework\Http;

readonly class Request implements RequestInterface
{
	public function __construct(
		protected readonly array $get,
		protected readonly array $post,
	) {
	}

	public function get(string $key): ?string
	{
		return $this->get[$key] ?? null;
	}

	public function post(string $key): ?string
	{
		return $this->post[$key] ?? null;
	}
}