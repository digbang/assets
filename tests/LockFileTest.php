<?php namespace Digbang\Assets\Tests;

use Digbang\Assets\Asset;
use Digbang\Assets\LockFile;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class LockFileTest extends \PHPUnit_Framework_TestCase
{
	private $assets = [
		'a.min.js',
		'a_style.css',
		'an_asset.js',
		'a.more.complicated.js.or.css-example.file'
	];

	/** @test */
	public function it_should_create_itself()
	{
		$folder = realpath(dirname(__FILE__).'/fixtures');

		$config     = \Mockery::mock(Repository::class)->shouldIgnoreMissing();
		$filesystem = \Mockery::mock(Filesystem::class)->shouldIgnoreMissing();

		$config
			->shouldReceive('get')
			->with('assets::assets', [])
			->andReturn(
				array_map(function($path) use ($folder){
					return $folder . '/' . $path;
				}, $this->assets)
		);

		$lockFile = LockFile::create($filesystem, $config);

		$this->assertInstanceOf(LockFile::class, $lockFile);
	}

	/** @test */
	public function it_should_create_itself_from_a_json_file()
	{
		$folder = realpath(dirname(__FILE__).'/fixtures');

		$assets = array_map(function($path) use ($folder){
			return $folder . '/' . $path;
		}, $this->assets);

		$config     = \Mockery::mock(Repository::class)->shouldIgnoreMissing();
		$filesystem = \Mockery::mock(Filesystem::class)->shouldIgnoreMissing();

		$filesystem
			->shouldReceive('get')
			->with('lock_path')
			->andReturn(json_encode([
				'assets' => array_map(function($path){
					return Asset::generate($path, strlen($path))->toArray();
				}, $assets)
			]));

		$config
			->shouldReceive('get')
			->with('assets::assets', [])
			->andReturn($assets);
		$config
			->shouldReceive('get')
			->with('assets::lock_path')
			->andReturn('lock_path');

		$lockFile = LockFile::create($filesystem, $config);

		$this->assertInstanceOf(LockFile::class, $lockFile);

		foreach ($assets as $path)
		{
			$asset = $lockFile->getAsset($path);

			$this->assertEquals(strlen($path), $asset->getVersion());
		}
	}

	/** @test */
	public function it_should_ignore_a_broken_json_file()
	{
		$folder = realpath(dirname(__FILE__).'/fixtures');

		$assets = array_map(function($path) use ($folder){
			return $folder . '/' . $path;
		}, $this->assets);

		$config     = \Mockery::mock(Repository::class)->shouldIgnoreMissing();
		$filesystem = \Mockery::mock(Filesystem::class)->shouldIgnoreMissing();

		$filesystem
			->shouldReceive('get')
			->with('lock_path')
			->andReturn(json_encode([
				'assets' => array_map(function($path){
					return Asset::generate($path, strlen($path))->toArray();
				}, $assets)
			]) . 'wefwef'); // Oops! Broken json file.

		$config
			->shouldReceive('get')
			->with('assets::assets', [])
			->andReturn($assets);
		$config
			->shouldReceive('get')
			->with('assets::lock_path')
			->andReturn('lock_path');

		$lockFile = LockFile::create($filesystem, $config);

		$this->assertInstanceOf(LockFile::class, $lockFile);

		foreach ($assets as $path)
		{
			$asset = $lockFile->getAsset($path);

			$this->assertEquals(1, $asset->getVersion());
		}
	}

	/** @test */
	public function it_should_dump_itself_to_filesystem()
	{
		$folder = realpath(dirname(__FILE__).'/fixtures');

		$assets = array_map(function($path) use ($folder){
			return $folder . '/' . $path;
		}, $this->assets);

		$config     = \Mockery::mock(Repository::class)->shouldIgnoreMissing();
		$filesystem = \Mockery::mock(Filesystem::class);

		$json = json_encode([
			'assets' => array_map(function($path){
				return Asset::generate($path, strlen($path))->toArray();
			}, $assets)
		]);

		$filesystem
			->shouldReceive('get')
			->with('lock_path')
			->andReturn($json);

		$config
			->shouldReceive('get')
			->with('assets::assets', [])
			->andReturn($assets);
		$config
			->shouldReceive('get')
			->with('assets::lock_path')
			->andReturn('lock_path');

		$lockFile = LockFile::create($filesystem, $config);

		$this->assertInstanceOf(LockFile::class, $lockFile);

		$filesystem->shouldReceive('put')
			->once()
			->with('lock_path', json_encode($lockFile->toArray()));

		$lockFile->dump();
	}
}
