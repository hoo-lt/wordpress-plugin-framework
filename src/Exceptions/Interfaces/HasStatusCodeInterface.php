<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Interfaces;

interface HasStatusCodeInterface
{
	public function getStatusCode(): int;
}