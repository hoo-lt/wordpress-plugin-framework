<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\Http\Coders\CoderInterface;

readonly class Body implements BodyInterface
{
	public function __construct(
		protected CoderInterface $coder,
		protected string|float|int|bool|null $body,
	) {
	}

	public function __toString(): string
	{
		return $this->coder->encode($this->body);
	}
}