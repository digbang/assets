<?php namespace Digbang\Assets;

use Illuminate\Support\ServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(AssetManager::class, AssetManager::class);
	}

	public function boot()
	{
		$this->package('digbang/assets');

		$this->commands([
			Commands\AssetVersionCommand::class
		]);
	}
}
