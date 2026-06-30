<?php

namespace Hoo\WordPressPluginFramework\Exceptions\Handler\ViewModel;

use Hoo\WordPressPluginFramework\{
    Collections\Message\CollectionInterface as MessageCollectionInterface,
    Exceptions\Interfaces\HasMessagesInterface,
    View\Model\ModelInterface as ViewModelInterface
};
use Throwable;

readonly class ViewModel implements ViewModelInterface
{
    protected function __construct(
        public string $message,
        public string $code,
        public ?MessageCollectionInterface $messages,
    ) {
    }

    public static function createFromThrowable(Throwable $throwable): static
    {
        $messages = $throwable instanceof HasMessagesInterface ? $throwable->getMessages() : null;
        
        return new self(
            $throwable->getMessage(),
            $throwable->getCode(),
            $messages,
        );
    }
}