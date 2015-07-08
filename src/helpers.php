<?php
if (! function_exists('vasset'))
{
	function vasset($path, $secure = null)
	{
		return \Digbang\Assets\Facade\Asset::asset($path, $secure);
	}
}
