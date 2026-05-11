<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http;

interface ResponseInterface extends Http\Message\MessageInterface
{
	public function statusCode(): int;
	public function withStatusCode(int $statusCode): static;
}
