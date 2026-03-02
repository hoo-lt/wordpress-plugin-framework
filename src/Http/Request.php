<?php

namespace Hoo\WordPressPluginFramework\Http;

readonly class Request implements RequestInterface
{
	public function __construct(
		protected readonly array $get,
		protected readonly array $post,
	) {
	}

	public function get(string $name): ?string
	{
		return $this->get[$name] ?? null;
	}

	public function post(string $name): ?string
	{
		return $this->post[$name] ?? null;
	}
}