<?php
namespace Imager;

class Color
{
	/**
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return int
	 */
	public static function create($r, $g, $b)
	{
		return self::createColor($r, $g, $b);
	}

	/**
	 * array(r, g, b)
	 * @param $array
	 * @return int
	 */
	public static function createFromArray($array)
	{
		return self::createColor($array[0], $array[1], $array[2]);
	}

	/**
	 * @return int
	 */
	public static function createBlack()
	{
		return self::createColor(0, 0, 0);
	}

	/**
	 * @return int
	 */
	public static function createWhite()
	{
		return self::createColor(255, 255, 255);
	}

	/**
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return int
	 */
	private static function createColor($r, $g, $b)
	{
		$image = imagecreatetruecolor(1, 1);
		return imagecolorallocate($image, $r, $g, $b);
	}
}
