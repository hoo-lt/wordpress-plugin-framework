<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Exceptions\UnprocessableContent;

use Hoo\WordPressPluginFramework\{
	Collections\Message\CollectionInterface as MessageCollectionInterface,
	Exceptions\Interfaces\HasMessagesInterface,
	Http\Exceptions\UnprocessableContent\Exception as UnprocessableContentException,
};

class Exception extends UnprocessableContentException implements HasMessagesInterface
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