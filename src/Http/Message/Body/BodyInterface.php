<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use stdClass;
use Stringable;

interface BodyInterface extends Stringable
{
    public function mediaType(): MediaTypeInterface;

    public function values(string $key): array;

    public function has(string $key): bool;
    public function get(string $key): string|int|float|bool|null|array|stdClass;

    public function with(string $key, string|int|float|bool|null|array|stdClass $value): static;
    public function without(string $key): static;
}
