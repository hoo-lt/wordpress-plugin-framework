<?php

namespace Hoo\WordPressPluginFramework\Http\Server;

readonly class Server implements ServerInterface
{
	protected array $server;

	public function __construct()
	{
		$this->server = $GLOBALS['_SERVER'];
	}

	public function method(): string
	{
		return $this->server['REQUEST_METHOD'] ?? '';
	}

	public function scheme(): string
	{
		return $this->server['REQUEST_SCHEME'] ?? '';
	}

	public function host(): string
	{
		$host = parse_url($this->server['HTTP_HOST'] ?? '', PHP_URL_HOST);
		if ($host === false) {
			//throw
		}

		if ($host === null) {
			return '';
		}

		return $host;
	}

	public function port(): ?int
	{
		$port = parse_url($this->server['HTTP_HOST'] ?? '', PHP_URL_PORT);
		if ($port === false) {
			//throw
		}

		return $port;
	}

	public function path(): string
	{
		$path = parse_url($this->server['REQUEST_URI'] ?? '', PHP_URL_PATH);
		if ($path === false) {
			//throw
		}

		if ($path === null) {
			return '';
		}

		return $path;
	}

	public function query(): ?string
	{
		$query = parse_url($this->server['REQUEST_URI'] ?? '', PHP_URL_QUERY);
		if ($query === false) {
			//throw
		}

		return $query;
	}

	public function contentLength(): ?int
	{
		return $this->server['CONTENT_LENGTH'] ?? null;
	}

	public function contentType(): ?string
	{
		return $this->server['CONTENT_TYPE'] ?? null;
	}

	public function body(): ?string
	{
		$body = file_get_contents('php://input');
		if ($body === false) {
			//throw
		}

		if ($body === '') {
			return null;
		}

		return $body;
	}

	public function headers(): ?array
	{
		$headers = [];

		foreach ($this->server as $key => $value) {
			if (!str_starts_with($key, 'HTTP_')) {
				continue;
			}

			$key = str_replace([
				'HTTP_',
				'_'
			], [
				'',
				'-'
			], $key);

			$headers[$key] = $value;
		}

		if (isset($this->server['CONTENT_LENGTH'])) {
			$headers['Content-Length'] = $this->server['CONTENT_LENGTH'];
		}

		if (isset($this->server['CONTENT_TYPE'])) {
			$headers['Content-Type'] = $this->server['CONTENT_TYPE'];
		}

		if ($headers === []) {
			return null;
		}

		return $headers;
	}
}