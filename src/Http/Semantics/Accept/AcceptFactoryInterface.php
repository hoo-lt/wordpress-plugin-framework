<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept;

interface AcceptFactoryInterface
{
	public function create(string $accept): AcceptInterface;
	public function tryCreate(?string $accept): ?AcceptInterface;
}
