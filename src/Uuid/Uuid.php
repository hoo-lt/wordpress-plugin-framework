<?php

namespace Hoo\WordPressPluginFramework\Uuid;

readonly class Uuid implements UuidInterface
{
    protected function __construct(
        protected string $uuid,
    ) {
    }

    public function __tostring(): string
    {
        return $this->uuid;
    }
}