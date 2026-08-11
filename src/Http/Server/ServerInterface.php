<?php

namespace Hoo\WordPressPluginFramework\Http\Server;

interface ServerInterface
{
	public function method(): string;
	public function url(): string;
	public function headers(): array;

	public function contentLength(): ?int;
	public function contentType(): ?string;

	public function body(): ?string;
}
