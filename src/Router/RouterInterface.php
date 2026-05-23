<?php

namespace Hoo\WordPressPluginFramework\Router;

use Hoo\WordPressPluginFramework\Router\Routes\RouteInterface;

interface RouterInterface
{
	public function withRoutes(RouteInterface ...$routes): static;

	public function __invoke(): void;

	public function up(): void;

	public function down(): void;
}
