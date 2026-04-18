<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

interface ResponseInterface
{
	public function __invoke(): void;
}