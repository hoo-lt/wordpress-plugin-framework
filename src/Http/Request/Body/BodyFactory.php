<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Body;

use Hoo\WordPressPluginFramework\Helpers;
use Hoo\WordPressPluginFramework\Json\JsonInterface;

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected JsonInterface $json,
	) {
	}

	public function from(string $raw, ?string $contentType): BodyInterface
	{
		$mediaType = $this->mediaType($contentType);

		$values = match ($mediaType) {
			'application/json' => $this->parseJson($raw),
			'application/x-www-form-urlencoded' => $this->parseForm($raw),
			default => throw new BodyFactoryException(
				$mediaType === ''
				? 'content-type is required to parse body'
				: "unsupported content-type \"{$mediaType}\""
			),
		};

		return new Body($this->arrayHelper, $values);
	}

	protected function mediaType(?string $contentType): string
	{
		if ($contentType === null) {
			return '';
		}

		// Strip parameters: "application/json; charset=utf-8" -> "application/json"
		$semicolon = strpos($contentType, ';');
		if ($semicolon !== false) {
			$contentType = substr($contentType, 0, $semicolon);
		}

		return strtolower(trim($contentType));
	}

	protected function parseJson(string $raw): array
	{
		if ($raw === '') {
			return [];
		}

		return $this->json->decode($raw);
	}

	protected function parseForm(string $raw): array
	{
		$values = [];
		parse_str($raw, $values);

		return $values;
	}
}
