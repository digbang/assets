<?php
if (! function_exists('vasset'))
{
	function vasset($path, $secure = null, $route = null)
	{
		return \Digbang\Assets\Facade\Asset::asset($path, $secure, $route);
	}
}
