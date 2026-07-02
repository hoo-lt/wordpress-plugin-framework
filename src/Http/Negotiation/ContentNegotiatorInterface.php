<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiation;

interface ContentNegotiatorInterface
{
	public function negotiate(?string $accept): ?string;
}
