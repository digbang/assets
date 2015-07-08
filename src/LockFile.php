<?php namespace Digbang\Assets;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

final class LockFile
{
	/**
	 * @type array
	 */
	private $assets = [];

	/**
	 * @type Filesystem
	 */
	private $filesystem;

	/**
	 * @type string
	 */
	private $path;

	/**
	 * @param Filesystem $filesystem
	 */
	private function __construct(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	/**
	 * @param Filesystem $filesystem
	 * @param Repository $config
	 *
	 * @return static
	 * @throws \Illuminate\Filesystem\FileNotFoundException
	 */
	public static function create(Filesystem $filesystem, $lockPath)
	{
		$lockFile = new static($filesystem);

		$lockFile->path = $lockPath;

		try
		{
			if (($json = json_decode($filesystem->get($lockFile->path))) !== null)
			{
				foreach ($json->assets as $jsonAsset)
				{
					$lockFile->addAsset(Asset::fromArray((array) $jsonAsset));
				}
			}
		}
		catch (FileNotFoundException $e){ }

		return $lockFile;
	}

	public function isManaged($path)
	{
		return array_key_exists($path, $this->assets);
	}

	/**
	 * @param Asset $asset
	 */
	public function addAsset(Asset $asset)
	{
		$this->assets[$asset->getFile()] = $asset;
	}

	public function dump()
	{
		$this->filesystem->put(
			$this->path,
			json_encode($this->toArray(), JSON_PRETTY_PRINT)
		);
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'assets' => array_map(function(Asset $asset){
				return $asset->toArray();
			}, $this->assets)
		];
	}

	public function check($path)
	{
		if (!$this->isManaged($path))
		{
			$this->addAsset(Asset::generate($path));
		}
		else
		{
			$this->getAsset($path)->check();
		}
	}

	/**
	 * @param string $path
	 * @return Asset
	 */
	public function getAsset($path)
	{
		return $this->assets[$path];
	}

	/**
	 * Remove all assets that are not in the given assets array
	 * @param Asset[]|array $assets
	 */
	public function clearMissing(array $assets)
	{
		$paths = array_map(function($asset){
			if ($asset instanceof Asset)
			{
				return $asset->getFile();
			}

			return $asset;
		}, $assets);

		foreach ($this->assets as $asset)
		{
			/** @type Asset $asset */
			if (! in_array($asset->getFile(), $paths))
			{
				unset($this->assets[$asset->getFile()]);
			}
		}
	}
}
