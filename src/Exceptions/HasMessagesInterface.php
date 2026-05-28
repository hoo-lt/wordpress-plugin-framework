<?php

namespace Hoo\WordPressPluginFramework\Exceptions;

use Hoo\WordPressPluginFramework\Collections\MessageCollection;

interface HasMessagesInterface
{
	public function getMessages(): MessageCollection;
}