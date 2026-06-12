<?php

namespace Hoo\WordPressPluginFramework\Http\Server;

interface ServerInterface
{
	public function method(): string;

	public function url(): string;
	public function scheme(): string;
	public function host(): string;
	public function port(): ?int;
	public function path(): string;
	public function query(): ?string;

	public function headers(): ?array;
	public function body(): ?string;
}