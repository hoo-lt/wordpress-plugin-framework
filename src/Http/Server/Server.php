<?php

namespace Hoo\WordPressPluginFramework\Http\Server;

readonly class Server implements ServerInterface
{
	public function __construct(
		protected string $input,
		protected array $server,
	) {
	}

	public function method(): string
	{
		return $this->server['REQUEST_METHOD'] ?? '';
	}

	public function url(): string
	{
		$scheme = ($this->server['REQUEST_SCHEME'] ?? '') ?: 'http';
		$hostPort = ($this->server['HTTP_HOST'] ?? '') ?: 'localhost';
		$pathQuery = $this->server['REQUEST_URI'] ?? '';

		return "{$scheme}://{$hostPort}{$pathQuery}";
	}

	public function scheme(): string
	{
		$url = $this->url();

		$scheme = parse_url($url, PHP_URL_SCHEME);
		if ($scheme === false) {
			throw new ServerException('an error occured while parsing url');
		}

		if ($scheme === null) {
			return '';
		}

		return $scheme;
	}

	public function host(): string
	{
		$url = $this->url();

		$host = parse_url($url, PHP_URL_HOST);
		if ($host === false) {
			throw new ServerException('an error occured while parsing url');
		}

		if ($host === null) {
			return '';
		}

		return $host;
	}

	public function port(): ?int
	{
		$url = $this->url();

		$port = parse_url($url, PHP_URL_PORT);
		if ($port === false) {
			throw new ServerException('an error occured while parsing url');
		}

		return $port;
	}

	public function path(): string
	{
		$url = $this->url();

		$path = parse_url($url, PHP_URL_PATH);
		if ($path === false) {
			throw new ServerException('an error occured while parsing url');
		}

		if ($path === null) {
			return '';
		}

		return $path;
	}

	public function query(): ?string
	{
		$url = $this->url();

		$query = parse_url($url, PHP_URL_QUERY);
		if ($query === false) {
			throw new ServerException('an error occured while parsing url');
		}

		return $query;
	}

	public function body(): ?string
	{
		if ($this->input === '') {
			return null;
		}

		return $this->input;
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