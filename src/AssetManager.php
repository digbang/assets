<?php namespace Digbang\Assets;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\UrlGenerator;

final class AssetManager
{
	/**
	 * @type Repository
	 */
	private $config;

	/**
	 * @type UrlGenerator
	 */
	private $urlGenerator;

	/**
	 * @type LockFile
	 */
	private $lockFile;

	/**
	 * @type Filesystem
	 */
	private $filesystem;

	/**
	 * @type bool
	 */
	private $enabled;

	public function __construct(Repository $config, UrlGenerator $urlGenerator, Filesystem $filesystem)
	{
		$this->config = $config;
		$this->urlGenerator = $urlGenerator;
		$this->filesystem = $filesystem;
	}

	public function asset($path, $secure = null, $route = null)
	{
		$this->init();

		if ($this->enabled && $this->lockFile->isManaged($path))
		{
			$path = $this->lockFile->getAsset($path)->getVersionedFile();
		}

		if ($route)
		{
			return $route . $path;
		}

		return $this->urlGenerator->asset($path, $secure);
	}

	private function init()
	{
		if ($this->enabled === null)
		{
			$this->enabled = (bool) $this->config->get('assets::enabled', false);

			if ($this->enabled)
			{
				$this->lockFile = LockFile::create(
					$this->filesystem,
					$this->config->get('assets::lock_path', storage_path('/meta/assets.lock'))
				);
			}
		}
	}
}
