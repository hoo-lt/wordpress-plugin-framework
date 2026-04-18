<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan\Capability;

enum Capability: string implements CapabilityInterface
{
	case Hearts = 'H';
	case Diamonds = 'D';
}