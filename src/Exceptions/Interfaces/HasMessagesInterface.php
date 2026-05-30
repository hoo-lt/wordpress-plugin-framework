<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Interfaces;

use Hoo\WordPressPluginFramework\Collections\MessageCollection;

interface HasMessagesInterface
{
	public function getMessages(): MessageCollection;
}