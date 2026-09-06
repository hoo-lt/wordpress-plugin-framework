<?php

namespace Hoo\WordPressPluginFramework\Http\Server;

readonly class Server implements ServerInterface
{
	protected const HEADER = '/^HTTP_/';

	public function __construct(
		protected array $globals,
		protected string $input,
	) {
	}

	public function method(): string
	{
		return $this->globals['_SERVER']['REQUEST_METHOD'] ?? 'GET';
	}

	public function url(): string
	{
		$scheme = $this->scheme();
		$hostPort = $this->hostPort();
		$pathQuery = $this->pathQuery();

		return "{$scheme}://{$hostPort}{$pathQuery}";
	}

	public function headers(): array
	{
		$headers = [];

		foreach ($this->globals['_SERVER'] as $name => $value) {
			if (!preg_match(self::HEADER, $name)) {
				continue;
			}

			$name = preg_replace([
				self::HEADER,
				'/_/'
			], [
				'',
				'-'
			], $name);

			$headers[strtolower($name)] = $value;
		}

		$contentLength = $this->contentLength();
		if ($contentLength !== null) {
			$headers['content-length'] = $contentLength;
		}

		$contentType = $this->contentType();
		if ($contentType !== null) {
			$headers['content-type'] = $contentType;
		}

		return $headers;
	}

	public function contentLength(): ?int
	{
		$contentLength = $this->globals['_SERVER']['CONTENT_LENGTH'] ?? '';
		if ($contentLength === '') {
			return null;
		}

		return $contentLength;
	}

	public function contentType(): ?string
	{
		$contentType = $this->globals['_SERVER']['CONTENT_TYPE'] ?? '';
		if ($contentType === '') {
			return null;
		}

		return $contentType;
	}

	public function body(): ?string
	{
		$contentLength = $this->contentLength();
		if ($contentLength === null) {
			return null;
		}

		return $this->input;
	}

	protected function scheme(): string
	{
		return $this->globals['_SERVER']['REQUEST_SCHEME'] ?? 'http';
	}

	protected function hostPort(): string
	{
		return $this->globals['_SERVER']['HTTP_HOST'] ?? 'localhost';
	}

	protected function pathQuery(): string
	{
		return $this->globals['_SERVER']['REQUEST_URI'] ?? '';
	}
}
