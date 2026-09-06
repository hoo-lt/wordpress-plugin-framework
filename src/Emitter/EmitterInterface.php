<?php

namespace Hoo\WordPressPluginFramework\Emitter;

use Hoo\WordPressPluginFramework\Http\Response\ResponseInterface;

interface EmitterInterface
{
	public function emit(ResponseInterface $response): void;
}
