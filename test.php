<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Hoo\WordPressPluginFramework\Helpers\Array\DraftHelper;

$data = [
	'users' => [
		[
			'name' => 'Viacheslav',
			'email' => 'ravdinve@yandex.ru',
			'addresses' => [
				['country' => 'LT'],
				['country' => 'IL'],
			],
		],
		[
			'name' => 'Viktorija',
			'email' => 'gogi4ka@yahoo.com',
			'addresses' => [
				['country' => 'LT'],
				['country' => 'UA'],
			],
		],
	],
];

$helper = new DraftHelper();
$key = isset($_GET['key']) ? (string) $_GET['key'] : 'users.*.email';

$valueOutput = null;
$valuesOutput = null;
$valueError = null;
$valuesError = null;

try {
	$valueOutput = $helper->value($data, $key);
} catch (Throwable $e) {
	$valueError = $e::class . ': ' . $e->getMessage();
}

try {
	$valuesOutput = $helper->values($data, $key);
} catch (Throwable $e) {
	$valuesError = $e::class . ': ' . $e->getMessage();
}

$dump = static function (mixed $v): string {
	return htmlspecialchars(
		var_export($v, true),
		ENT_QUOTES | ENT_SUBSTITUTE,
		'UTF-8'
	);
};

$presets = [
	'Normal' => [
		'Single value (literal)' => [
			'users',
			'users.0',
			'users.0.email',
			'users.0.name',
			'users.1.email',
			'users.0.addresses',
			'users.0.addresses.0',
			'users.0.addresses.0.country',
			'users.0.addresses.1.country',
			'users.1.addresses.1.country',
		],
		'List of values (single wildcard)' => [
			'users.*',
			'users.*.name',
			'users.*.email',
			'users.*.addresses',
			'users.0.addresses.*',
			'users.0.addresses.*.country',
		],
		'List of values (chained wildcards)' => [
			'users.*.addresses.*',
			'users.*.addresses.*.country',
		],
		'Wildcard at root' => [
			'*',
			'*.0',
			'*.0.email',
			'*.*.email',
			'*.*.addresses.*.country',
		],
	],
	'Edge — missing literal keys' => [
		'Top-level missing' => [
			'missing',
			'missing.deep',
			'missing.deep.path',
		],
		'Index out of range' => [
			'users.99',
			'users.99.email',
			'users.99.addresses.0.country',
			'users.0.addresses.99',
			'users.0.addresses.99.country',
		],
		'Missing leaf key on each item' => [
			'users.0.missing',
			'users.*.missing',
			'users.*.addresses.*.missing',
		],
		'Descend into a scalar' => [
			'users.0.email.subkey',
			'users.0.email.deep.path',
			'users.0.name.0',
		],
	],
	'Edge — wildcard shape mismatches' => [
		'Wildcard over a scalar' => [
			'users.0.email.*',
			'users.0.email.*.x',
			'users.*.email.*',
			'users.*.name.*.deep',
		],
		'Wildcard with missing parent' => [
			'missing.*',
			'missing.*.deep',
			'users.99.*.country',
			'users.99.addresses.*.country',
			'users.0.addresses.99.*.country',
		],
	],
	'Edge — unusual but valid keys' => [
		'Whitespace-only and embedded whitespace' => [
			' ',
			'  ',
			'users. .email',
			"users.\t.email",
		],
		'Numeric-string vs integer indices' => [
			'users.0',
			'users.00',   // not the same as 0
			'users.+0',   // not the same as 0
		],
		'Literal star key' => [
			// no escape mechanism — "*" always means wildcard
			'*',
		],
	],
	'Edge — invalid keys (must throw)' => [
		'Empty key' => [
			'',
		],
		'Empty parts (leading / trailing / consecutive dots)' => [
			'.',
			'..',
			'...',
			'.users',
			'users.',
			'users..email',
			'users...email',
			'users.*.',
			'.*',
		],
	],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>DraftHelper sandbox</title>
	<style>
		body { font-family: ui-monospace, Menlo, Consolas, monospace; max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
		form { margin-bottom: 1.5rem; }
		input[type=text] { width: 60%; padding: .5rem; font: inherit; }
		button { padding: .5rem 1rem; font: inherit; }
		.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
		.box { border: 1px solid #ccc; border-radius: 6px; padding: 1rem; background: #fafafa; }
		.box h2 { margin: 0 0 .5rem; font-size: 1rem; }
		pre { margin: 0; white-space: pre-wrap; word-break: break-word; }
		.error { color: #b00; }
		.data { background: #f0f0f0; padding: 1rem; border-radius: 6px; margin-top: 2rem; }
		ul.presets { columns: 2; padding-left: 1.2rem; margin-top: .25rem; }
		ul.presets li { break-inside: avoid; margin-bottom: .2rem; }
		ul.presets a { text-decoration: none; }
		ul.presets a:hover { text-decoration: underline; }
		h3 { margin: 1.5rem 0 .25rem; padding-bottom: .25rem; border-bottom: 2px solid #888; }
		h4 { margin: .75rem 0 .25rem; color: #555; font-size: .9rem; }
		.current { background: #ffffd6; padding: .25rem .5rem; border-radius: 4px; }
	</style>
</head>
<body>
	<h1>DraftHelper sandbox</h1>

	<form method="get">
		<label>
			Key:
			<input type="text" name="key" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" autofocus>
		</label>
		<button type="submit">Run</button>
	</form>

	<p>Current key: <code class="current"><?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?></code></p>

	<div class="grid">
		<div class="box">
			<h2>value()</h2>
			<?php if ($valueError !== null): ?>
				<pre class="error"><?= htmlspecialchars($valueError, ENT_QUOTES, 'UTF-8') ?></pre>
			<?php else: ?>
				<pre><?= $dump($valueOutput) ?></pre>
			<?php endif; ?>
		</div>
		<div class="box">
			<h2>values()</h2>
			<?php if ($valuesError !== null): ?>
				<pre class="error"><?= htmlspecialchars($valuesError, ENT_QUOTES, 'UTF-8') ?></pre>
			<?php else: ?>
				<pre><?= $dump($valuesOutput) ?></pre>
			<?php endif; ?>
		</div>
	</div>

	<h2>Preset keys</h2>
	<?php foreach ($presets as $groupName => $subgroups): ?>
		<h3><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></h3>
		<?php foreach ($subgroups as $subName => $items): ?>
			<h4><?= htmlspecialchars($subName, ENT_QUOTES, 'UTF-8') ?></h4>
			<ul class="presets">
				<?php foreach ($items as $preset): ?>
					<li>
						<a href="?key=<?= rawurlencode($preset) ?>">
							<?= $preset === '' ? '<em>(empty)</em>' : htmlspecialchars($preset, ENT_QUOTES, 'UTF-8') ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	<?php endforeach; ?>

	<div class="data">
		<h2>Dataset</h2>
		<pre><?= $dump($data) ?></pre>
	</div>
</body>
</html>
