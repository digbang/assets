<?php namespace Digbang\Assets\Facade;

use Digbang\Assets\AssetManager;
use Illuminate\Support\Facades\Facade;

class Asset extends Facade
{
	protected static function getFacadeAccessor()
	{
		return AssetManager::class;
	}
}
