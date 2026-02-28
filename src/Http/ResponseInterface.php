<?php

namespace Hoo\WordpressPluginFramework\Http;

interface ResponseInterface
{
	public function __invoke(): void;
}