<?php

$finder = (new PhpCsFixer\Finder())
	->in([__DIR__ . '/src', __DIR__ . '/tests']);

return (new PhpCsFixer\Config())
	->setRiskyAllowed(false)
	->setIndent("\t")
	->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
	->setRules([
		'@PSR12' => true,
		// Opening braces stay on the same line, with a blank line after a class opening brace
		'braces_position' => [
			'classes_opening_brace' => 'same_line',
			'functions_opening_brace' => 'same_line',
			'anonymous_classes_opening_brace' => 'same_line',
			'anonymous_functions_opening_brace' => 'same_line',
			'control_structures_opening_brace' => 'same_line',
		],
		'no_blank_lines_after_class_opening' => false,
		'array_syntax' => ['syntax' => 'short'],
		'no_unused_imports' => true,
		'single_quote' => true,
		'no_trailing_comma_in_singleline' => true,
		'single_line_empty_body' => true,
	])
	->setFinder($finder);
