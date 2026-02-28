<?php

namespace Hoo\WordPressPluginFramework\Http;

class Request implements RequestInterface
{
	public function __construct(
		protected readonly array $get,
		protected readonly array $post,
	) {
	}
}