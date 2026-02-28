<?php

namespace Hoo\WordPressPluginFramework\Http;

interface ResponseInterface
{
	public function __invoke(): void;
}