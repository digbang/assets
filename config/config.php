<?php
return [
	/**
	 * Enable / disable asset versioning
	 */
	'enabled' => true,
	/**
	 * Change the lock file path
	 */
	'lock_path' => storage_path('meta/assets.lock'),
	/**
	 * Assets to version. Don't do absolute paths! Fill them as you would fill
	 * the asset() function call (usually relative to the public folder)
	 */
	'assets' => [

	]
];