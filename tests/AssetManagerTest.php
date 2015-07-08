<?php namespace Digbang\Assets\Tests;

use Digbang\Assets\AssetManager;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\UrlGenerator;

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
	private $assets = [
		'a.min.js',
		'a_style.css',
		'an_asset.js',
		'a.more.complicated.js.or.css-example.file'
	];

	/** @test */
	public function it_should_give_me_a_different_url_for_a_managed_asset()
	{
		$folder = realpath(dirname(__FILE__).'/fixtures');

		$config = \Mockery::mock(Repository::class)->shouldIgnoreMissing();
		$url = \Mockery::mock(UrlGenerator::class)->shouldIgnoreMissing();
		$filesystem = \Mockery::mock(Filesystem::class)->shouldIgnoreMissing();

		$config
			->shouldReceive('get')
			->with('assets::assets', [])
			->andReturn(
				array_map(function($path) use ($folder){
					return $folder . '/' . $path;
				}, $this->assets)
		);

		$config->shouldReceive('get')->with('assets::enabled', false)->andReturn(true);

		$url->shouldReceive('asset')->with($folder . '/' . 'a_style.1.css', \Mockery::any())->andReturn('http://localhost/fake/a_style.1.css');

		$assetManager = new AssetManager($config, $url, $filesystem);

		$path = $assetManager->asset($folder . '/' . 'a_style.css');

		$this->assertEquals('http://localhost/fake/a_style.1.css', $path);
	}
}
