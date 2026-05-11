<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

use Hoo\WordPressPluginFramework\Http;

interface BodyFactoryInterface
{
	public function from(?string $contentType, string $body): BodyInterface;
	public function fromServer(): ?BodyInterface;
}
