<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

interface RouteInterface
{
	public function __invoke(): void;

	public function up(): void;
	public function down(): void;
}
