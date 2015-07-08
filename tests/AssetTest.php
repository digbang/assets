<?php namespace Digbang\Assets\Tests;

use Digbang\Assets\Asset;

class AssetTest extends \PHPUnit_Framework_TestCase
{
	private $assets = [
		'a.min.js'    => 'a.min.%d.js',
		'a_style.css' => 'a_style.%d.css',
		'an_asset.js' => 'an_asset.%d.js',
		'a.more.complicated.js.or.css-example.file' => 'a.more.complicated.js.or.css-example.%d.file'
	];

	/** @test */
	public function it_should_generate_a_versioned_file_name()
	{
		$folder = realpath(dirname(__FILE__).'/fixtures');

		foreach ($this->assets as $file => $pattern)
		{
			$version = mt_rand(1, 999);

			$asset = Asset::generate($folder.'/'.$file, $version);

			$this->assertEquals(
				$folder . '/' . sprintf($pattern, $version),
				$asset->getVersionedFile()
			);
		}
	}

	/** @test */
	public function it_should_fail_if_a_managed_asset_does_not_exist()
	{
		$this->setExpectedException('UnexpectedValueException');

		Asset::generate('im_not_a_real_file!');
	}
}
