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

	public function url(): string
	{
		$scheme = $this->server['REQUEST_SCHEME'] ?? '';
		if ($scheme === '') {
			return '';
		}

		$hostPort = $this->server['HTTP_HOST'] ?? '';
		if ($hostPort === '') {
			return '';
		}

		$pathQuery = $this->server['REQUEST_URI'] ?? '';

		return "{$scheme}://{$hostPort}{$pathQuery}";
	}

	public function scheme(): string
	{
		return $this->server['REQUEST_SCHEME'] ?? '';
	}

	public function host(): string
	{
		$url = $this->url();
		if ($url === '') {
			return '';
		}

		$host = parse_url($url, PHP_URL_HOST);
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
		$url = $this->url();
		if ($url === '') {
			return null;
		}

		$port = parse_url($url, PHP_URL_PORT);
		if ($port === false) {
			//throw
		}

		return $port;
	}

	public function path(): string
	{
		$url = $this->url();
		if ($url === '') {
			return '';
		}

		$path = parse_url($url, PHP_URL_PATH);
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
		$url = $this->url();
		if ($url === '') {
			return null;
		}

		$query = parse_url($url, PHP_URL_QUERY);
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

			$headers[strtolower($key)] = $value;
		}

		if (isset($this->server['CONTENT_LENGTH'])) {
			$headers['content-length'] = $this->server['CONTENT_LENGTH'];
		}

		if (isset($this->server['CONTENT_TYPE'])) {
			$headers['content-type'] = $this->server['CONTENT_TYPE'];
		}

		if ($headers === []) {
			return null;
		}

		return $headers;
	}
}