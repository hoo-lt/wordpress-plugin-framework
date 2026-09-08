<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\Headers,
	Http\Response\Response,
	Uuid\UuidInterface,
	View\ViewFactoryInterface,
};

readonly class ResponsesBuilder implements ResponsesBuilderInterface
{
	public function __construct(
		protected BodyFactoryInterface $bodyFactory,
		protected ViewFactoryInterface $viewFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		protected array $headers = [],
		protected array $bodies = [],
	) {
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $statusCode, $this->headers, $this->bodies);
	}

	public function withHeaders(array $headers): static
	{
		$headers = array_change_key_case($headers, CASE_LOWER);

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $headers, $this->bodies);
	}

	public function withoutHeaders(): static
	{
		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, [], $this->bodies);
	}

	public function withHeader(string $name, string $value): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $value;

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $headers, $this->bodies);
	}

	public function withoutHeader(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $headers, $this->bodies);
	}

	public function withBodies(mixed $body): static
	{
		$bodies = [
			...$this->bodies,
			...$this->bodyFactory->createBodiesFromUnnormalized($body),
		];

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function withoutBodies(): static
	{
		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $this->headers, []);
	}

	public function withBody(string $contentType, mixed $body): static
	{
		$bodies = $this->bodies;
		$bodies[$contentType] = $this->bodyFactory->createBodyFromUnnormalized($contentType, $body);

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function withoutBody(string $contentType): static
	{
		$bodies = $this->bodies;
		unset($bodies[$contentType]);

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function withView(string $contentType, string $view, mixed $viewModel): static
	{
		$view = $this->viewFactory->create($view, $viewModel);

		$bodies = $this->bodies;
		$bodies[$contentType] = $this->bodyFactory->createBody($contentType, $view);

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function withoutView(string $contentType): static
	{
		$bodies = $this->bodies;
		unset($bodies[$contentType]);

		return new static($this->bodyFactory, $this->viewFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function build(): ResponsesInterface
	{
		if ($this->statusCode === null) {
			throw new ResponsesBuilderException('status code is mandatory');
		}

		$responses = new Responses();

		if ($this->bodies === []) {
			$headers = $this->headers;
			unset($headers['content-type']);

			$headers = new Headers($headers);

			$responses->add(
				new Response($this->uuid, $this->statusCode, $headers),
			);
		}

		foreach ($this->bodies as $contentType => $body) {
			$headers = $this->headers;
			$headers['content-type'] = $contentType;

			$headers = new Headers($headers);

			$responses->add(
				new Response($this->uuid, $this->statusCode, $headers, $body),
			);
		}

		return $responses;
	}
}
