<?php

namespace Hoo\WordPressPluginFramework\Uuid;

use Hoo\WordPressPluginFramework\Uuid\UuidInterface;

interface UuidFactoryInterface
{
    public function create(): UuidInterface;
}
