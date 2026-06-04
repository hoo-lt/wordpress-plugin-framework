<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

readonly class Body implements BodyInterface
{
	public function __construct(
		protected string $body,
	) {
	}

	public function __toString(): string
	{
		return $this->body;
	}
}