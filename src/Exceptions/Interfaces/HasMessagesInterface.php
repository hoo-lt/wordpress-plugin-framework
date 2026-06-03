<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Interfaces;

use Hoo\WordPressPluginFramework\Collections\Message\CollectionInterface as MessageCollectionInterface;

interface HasMessagesInterface
{
	public function getMessages(): MessageCollectionInterface;
}