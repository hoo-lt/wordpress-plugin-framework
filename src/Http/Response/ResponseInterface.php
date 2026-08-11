<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\MessageInterface,
	Uuid\UuidInterface,
};

interface ResponseInterface extends MessageInterface
{
	public function uuid(): UuidInterface;

	public function statusCode(): int;
	public function withStatusCode(int $statusCode): static;
}
