<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

interface BodyFactoryInterface
{
	public function formBody(string $body): BodyInterface;
	public function jsonBody(string $body): BodyInterface;

	public function from(string $body, ?string $contentType): BodyInterface;
}
