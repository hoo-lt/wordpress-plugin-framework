<?php

namespace Hoo\WordPressPluginFramework\Exceptions;

interface HasStatusCodeInterface
{
	public function getStatusCode(): int;
}