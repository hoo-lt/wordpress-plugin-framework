<?php

namespace Hoo\WordPressPluginFramework\Router;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Http\Method\Method,
};

interface RouterInterface
{
	public function withRoutes(RouteInterface ...$routes): static;

	public function withAdminAjaxRoute(string $action, Closure $closure): static;
	public function withFeedRoute(string $name, Closure $closure): static;
	public function withRestRoute(string $routeNamespace, string $route, Closure $closure, Method ...$methods): static;

	public function __invoke(): void;

	public function up(): void;
	public function down(): void;
}
