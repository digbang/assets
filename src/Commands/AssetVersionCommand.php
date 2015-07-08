<?php namespace Digbang\Assets\Commands;

use Digbang\Assets\Asset;
use Digbang\Assets\LockFile;
use GrahamCampbell\Flysystem\FlysystemManager;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Input\InputOption;

final class AssetVersionCommand extends Command
{
	protected $name = 'asset:version';

	protected $description = 'Check assets versions, create new ones and dump the assets lock file.';

	/**
	 * @type Repository
	 */
	protected $config;

	/**
	 * @type Filesystem
	 */
	protected $filesystem;

	/**
	 * @param Repository $config
	 * @param Filesystem $filesystem
	 */
	public function __construct(Repository $config, Filesystem $filesystem)
	{
		$this->config     = $config;
		$this->filesystem = $filesystem;

		parent::__construct();
	}

	public function fire()
	{
		$lockFile = LockFile::create($this->filesystem, $this->config->get('assets::lock_path', storage_path('/meta/assets.lock')));

		$assets = $this->config->get('assets::assets', []);
		foreach ($assets as $key => $path)
		{
			if ($lockFile->isManaged($path))
			{
				$lockFile->getAsset($path)->check();
			}
			else
			{
				$lockFile->addAsset(Asset::generate($path));
			}

			$asset = $lockFile->getAsset($path);

			$this->info("Copying {$asset->getFile()} to {$asset->getVersionedFile()}");
			$this->copy($asset->getFile(), $asset->getVersionedFile());
		}

		$lockFile->clearMissing($assets);

		$this->info(PHP_EOL . 'Dumping lock file');
		$lockFile->dump();
	}

	private function copy($file, $versionedFile)
	{
		$flysystemConnection = $this->option('flysystem');
		if ($flysystemConnection !== null)
		{
			/** @type FlysystemManager $flysystem */
			$flysystem = $this->laravel->make(FlysystemManager::class);

			/** @type FilesystemInterface $conn */
			$driver = $flysystem->connection($flysystemConnection);
			$driver->copy($file, $versionedFile);
		}
		else
		{
			$this->filesystem->copy(
				public_path($file),
				public_path($versionedFile)
			);
		}

	}

	protected function getOptions()
    {
	    return [
		    ['flysystem', null, InputOption::VALUE_OPTIONAL, 'The flysystem connection to use.', null],
	    ];
    }
}
