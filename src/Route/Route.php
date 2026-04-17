<?php

namespace Hoo\WordPressPluginFramework\Route;

use Closure;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Route\Type\Type;

readonly class Route implements RouteInterface
{
	protected function __construct(
		protected Type $type,
		protected string $hook,
		protected Closure $handler,
		protected int $priority = PHP_INT_MAX,
		protected array $middlewares = [],
	) {
	}

	public static function action(string $hook, Closure $handler, int $priority = PHP_INT_MAX): self
	{
		return new self(Type::Action, $hook, $handler, $priority);
	}

	public static function filter(string $hook, Closure $handler, int $priority = PHP_INT_MAX): self
	{
		return new self(Type::Filter, $hook, $handler, $priority);
	}

	public static function feed(string $feedname, Closure $handler): self
	{
		return self::action('init', fn() => add_feed($feedname, $handler));
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): self
	{
		return new self(
			$this->type,
			$this->hook,
			$this->handler,
			$this->priority,
			$middlewares,
		);
	}

	public function type(): Type
	{
		return $this->type;
	}

	public function hook(): string
	{
		return $this->hook;
	}

	public function handler(): Closure
	{
		return $this->handler;
	}

	public function priority(): int
	{
		return $this->priority;
	}

	public function middlewares(): array
	{
		return $this->middlewares;
	}
}
