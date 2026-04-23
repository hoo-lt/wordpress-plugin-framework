<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Body;

interface BodyFactoryInterface
{
	public function from(string $raw, ?string $contentType): BodyInterface;
}
