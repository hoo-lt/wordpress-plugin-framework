<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Responder;

interface ResponderFactoryInterface
{
	public function create(string $mediaType): ResponderInterface;
}
