<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http\Message\MessageInterface;

interface ResponseInterface extends MessageInterface
{
	public function statusCode(): int;
	public function withStatusCode(int $statusCode): static;
}
