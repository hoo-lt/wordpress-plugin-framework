<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use Stringable;

interface BodyInterface extends Stringable
{
    public function mediaType(): MediaTypeInterface;
}
