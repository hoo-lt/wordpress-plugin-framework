<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan\Exceptions\Forbidden;

use Hoo\WordPressPluginFramework\{
	Collections\Message\CollectionInterface as MessageCollectionInterface,
	Exceptions\Interfaces\HasMessagesInterface,
	Http\Exceptions\Forbidden\Exception as ForbiddenException,
};

class Exception extends ForbiddenException implements HasMessagesInterface
{
	public function __construct(
		string $message,
		string $code,
		protected MessageCollectionInterface $messages,
	) {
		parent::__construct(
			$message,
			$code,
		);
	}

	public function getMessages(): MessageCollectionInterface
	{
		return $this->messages;
	}
}