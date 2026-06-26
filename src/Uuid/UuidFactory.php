<?php

namespace Hoo\WordPressPluginFramework\Uuid;

use Hoo\WordPressPluginFramework\{
    Uuid\Uuid,
    Uuid\UuidInterface,
};

readonly class UuidFactory implements UuidFactoryInterface
{
    public function create(): UuidInterface
    {
        return new Uuid(
            wp_generate_uuid4(),
        );
    }
}
