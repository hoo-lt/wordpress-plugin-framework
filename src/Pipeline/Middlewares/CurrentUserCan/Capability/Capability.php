<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan\Capability;

enum Capability: string
{
	case ManageWoocommerce = 'manage_woocommerce';
}