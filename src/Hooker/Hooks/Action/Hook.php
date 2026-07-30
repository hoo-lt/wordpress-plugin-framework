<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Action;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Http\Server\Request\RequestInterface,
	Http\Server\Request\RequestFactoryInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineFactoryInterface,
};

readonly class Hook implements HookInterface
{
	protected RequestInterface $request;
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestFactoryInterface $requestFactory,
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $name,
		protected Closure $closure,
		protected int $priority = 10,
		protected ?Closure $middlewaresBuilderClosure = null,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			$this->name,
			$this->callback(...),
			$this->priority,
			PHP_INT_MAX,
		);
	}

	protected function callback(...$args): mixed
	{
		$pipeline = $this->pipeline();

		return $pipeline(fn($request) => ($this->closure)($request, ...$args));
	}

	protected function pipeline(): PipelineInterface
	{
		$request = $this->request();

		return $this->pipeline ??= $this->pipelineFactory->create($request, $this->middlewaresBuilderClosure);
	}

	protected function request(): RequestInterface
	{
		return $this->request ??= $this->requestFactory->createFromServer();
	}
}
