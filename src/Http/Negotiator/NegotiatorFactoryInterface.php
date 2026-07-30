<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiator;

interface NegotiatorFactoryInterface
{
	public function create(string $mediaType): NegotiatorInterface;
}
