<?php
namespace Imager\Image;

use Imager\Image;

class Functions
{
	const DIRECTION_HORIZONTAL = 1;
	const DIRECTION_VERTICAL = 2;

	/**
	 * @param resource $image
	 * @return resource
	 */
	public static function cloneImage($image)
	{
		list($w, $h) = self::getResourceDimensions($image);
		$newCanvas = imagecreatetruecolor($w, $h);
		self::placeImage($newCanvas, $image, 0, 0);
		return $newCanvas;
	}

	/**
	 * @param resource $image
	 */
	public static function flipVertical(&$image)
	{
		list($w, $h) = self::getResourceDimensions($image);
		$result = imagecopyresampled($image, $image, 0, 0, 0, ($h - 1), $w, $h, $w, 0 - $h);
		if (!$result) {
			throw new \RuntimeException("Unable to flip image");
		}
	}

	/**
	 * @param resource $image
	 */
	public static function flipHorizontal(&$image)
	{
		list($w, $h) = self::getResourceDimensions($image);
		$result = imagecopyresampled($image, $image, 0, 0, ($w - 1), 0, $w, $h, 0 - $w, $h);
		if (!$result) {
			throw new \RuntimeException("Unable to flip image");
		}
	}

	/**
	 * @param resource $canvas
	 * @param resource $image
	 * @param int $shiftX
	 * @param int $shiftY
	 */
	public static function placeImage($canvas, $image, $shiftX, $shiftY)
	{
		list($w, $h) = self::getResourceDimensions($image);
		$result = imagecopyresampled(
			$canvas,
			$image,
			$shiftX, $shiftY, 0, 0,
			$w, $h, $w, $h
		);
		if (!$result) {
			throw new \RuntimeException("Unable to placeImage image");
		}
	}

	/**
	 * @param string $path
	 * @throws \RuntimeException
	 * @return array
	 */
	public static function getFileDimensions($path)
	{
		if (!$size = getimagesize($path)) {
			throw new \RuntimeException("get dimensions from file '{$path}' failed");
		} else {
			return array($size[0], $size[1]);
		}
	}

	/**
	 * @param $resource
	 * @return array
	 */
	public static function getResourceDimensions($resource)
	{
		if (!is_resource($resource)) {
			throw new \RuntimeException("1 is not valid resource");
		}
		return array(imagesx($resource), imagesy($resource));
	}

	/**
	 * @param string $hex
	 * @return int[]
	 */
	public static function hex2rgb($hex) {
		$rgb[0] = hexdec(substr($hex, 0, 2));
		$rgb[1] = hexdec(substr($hex, 2, 2));
		$rgb[2] = hexdec(substr($hex, 4, 2));
		return $rgb;
	}
}