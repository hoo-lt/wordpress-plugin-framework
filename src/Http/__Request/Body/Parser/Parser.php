<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Body\Parser;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Json\JsonInterface;

readonly class Parser implements ParserInterface
{
	public function __construct(
		protected JsonInterface $json,
		protected Method $method,
		protected ?string $contentType,
		protected array $post,
		protected string $input,
	) {
	}

	public function __invoke(): ?array
	{
		if (
			!in_array(
				$this->method,
				[
					Method::Post,
					Method::Put,
					Method::Patch,
				],
				true
			)
		) {
			return null;
		}

		if ($this->contentType === null) {
			return null;
		}

		[
			$contentType,
		] = explode(';', $this->contentType);

		return match (strtolower($contentType)) {
			'application/json' => $this->json->decode($this->input),
			'application/x-www-form-urlencoded', 'multipart/form-data' => $this->post,
			default => null,
		};
	}
}
