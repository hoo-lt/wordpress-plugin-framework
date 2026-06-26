<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

use Hoo\WordPressPluginFramework\{
    Http\Client\Response\ResponseInterface as ClientResponseInterface,
    Uuid\UuidInterface,
};

interface ResponseInterface extends ClientResponseInterface
{
    public function uuid(): UuidInterface;
}
