<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Interfaces;

use Hoo\WordPressPluginFramework\Collections\Message\CollectionInterface;

interface HasMessagesInterface
{
	public function getMessages(): CollectionInterface;
}