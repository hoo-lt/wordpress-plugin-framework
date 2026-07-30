<?php

namespace Hoo\WordPressPluginFramework\Router;

interface RouterInterface
{
	public function __invoke(): void;

	public function up(): void;
	public function down(): void;
}
