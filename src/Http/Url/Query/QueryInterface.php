<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use stdClass;
use Stringable;

interface QueryInterface extends Stringable
{
    public function values(string $key): array;

    public function has(string $key): bool;
    public function get(string $key): string|int|float|bool|null|array|stdClass;

    public function with(string $key, string|int|float|bool|null|array|stdClass $value): static;
    public function without(string $key): static;
}
