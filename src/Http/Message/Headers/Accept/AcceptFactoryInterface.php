<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept;

interface AcceptFactoryInterface
{
	public function create(string $accept): AcceptInterface;
	public function tryCreate(?string $accept): ?AcceptInterface;
}
