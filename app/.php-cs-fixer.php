<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
	->in([
		__DIR__ . '/app',
		__DIR__ . '/config',
		__DIR__ . '/database',
		__DIR__ . '/resources',
		__DIR__ . '/routes',
		__DIR__ . '/tests',
	])
	->name('*.php')
	->notName('*.blade.php')
	->ignoreDotFiles(true)
	->ignoreVCS(true);

$config = (new Config())
	->setFinder($finder)
	->setIndent("\t")
	->setLineEnding("\n")
	->setRules([
		'@PSR12' => true,
		'array_syntax' => ['syntax' => 'short'],
		'braces' => [
			'position_after_control_structures' => 'next',
			'position_after_anonymous_constructs' => 'next',
		],
		'new_with_braces' => [
			'anonymous_class' => false
		],
	]);

return $config;
