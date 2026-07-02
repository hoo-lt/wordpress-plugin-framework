<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Query;

use Hoo\WordPressPluginFramework\Http;

interface CoderInterface extends Http\Coders\CoderInterface
{
    public function decode(mixed $encoded): array;
}