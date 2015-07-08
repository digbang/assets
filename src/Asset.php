<?php namespace Digbang\Assets;

final class Asset
{
	private $file;
	private $version = 1;
	private $md5;

	private function __construct($file, $md5, $version)
	{
		$this->file    = $file;
		$this->version = $version;
		$this->md5     = $md5;
	}

	public static function fromArray($data)
	{
		return new static($data['file'], $data['md5'], $data['version']);
	}

	public static function generate($path, $version = 1)
	{
		return new static($path, md5_file(public_path($path)), $version);
	}

	public function toArray()
	{
		return [
			'file'    => $this->file,
			'version' => $this->version,
			'md5'     => $this->md5
		];
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	public function getVersionedFile()
	{
		return preg_replace('#(.*)\.([^.]*)$#', '\1.' . $this->version . '.\2', $this->file);
	}

	public function check()
	{
		$md5 = md5_file(public_path($this->file));

		if ($this->md5 != $md5)
		{
			$this->md5 = $md5;
			$this->version++;
		}
	}

	public function getVersion()
	{
		return $this->version;
	}
}
