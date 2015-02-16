<?php
namespace Imager\Filter\Reflection;

use Imager\Filter\Filter;
use Imager\Image;

class Reflection implements Filter
{
	/**
	 * @var float
	 */
	private $startTransparency = 0.7;
	/**
	 * @var float
	 */
	private $endTransparency = 1;
	/**
	 * @var int
	 */
	private $reflectionHeight = 36;

	/**
	 * @param int $startTransparency
	 * @return $this
	 */
	public function setStartTransparency($startTransparency)
	{
		$this->startTransparency = $startTransparency;
		return $this;
	}

	/**
	 * @param int $endTransparency
	 * @return $this
	 */
	public function setEndTransparency($endTransparency)
	{
		$this->endTransparency = $endTransparency;
		return $this;
	}

	/**
	 * @param Image $image
	 * @return resource
	 */
	public function apply(Image $image)
	{
		$canvas = $this->createNewCanvas($image);
		$this->placeOriginalImage($canvas, $image);
		$this->addReflection($canvas, $image);
		return $canvas;
	}

	/**
	 * @param Image $image
	 * @return resource
	 */
	private function createNewCanvas(Image $image)
	{
		$newCanvas = imagecreatetruecolor($image->getWidth(), $image->getHeight() + $this->reflectionHeight);
		$trans_colour = imagecolorallocatealpha($newCanvas, 0, 0, 0, 127);
		imagefill($newCanvas, 0, 0, $trans_colour);
		return $newCanvas;
	}

	/**
	 * @param resource $canvas
	 * @param Image $image
	 */
	private function placeOriginalImage($canvas, Image $image)
	{
		Image\Functions::placeImage($canvas, $image->getResource(), 0, 0);
	}

	/**
	 * @param resource $canvas
	 * @param Image $image
	 */
	private function addReflection($canvas, Image $image)
	{
		$width = $image->getWidth();
		$start = $image->getHeight();
		$end = imagesy($canvas);
		$imageResource = $image->getResource();

		$range = $end - $start;
		$opacityDiffPerLine = ($this->endTransparency - $this->startTransparency) / $range;
		$opacity = $this->startTransparency;

		for ($y = 0; $y < $range; ++$y, $opacity += $opacityDiffPerLine) {
			$sourceLineIndex = $start - $y - 1;
			$targetLineIndex = $start + $y;
			for ($x = 0; $x < $width; ++$x) {
				// found rgb in source image
				$rgb = imagecolorsforindex($imageResource, imagecolorat($imageResource, $x, $sourceLineIndex));

				// copy rgb from source image to canvas with opacity
				$color = imagecolorallocatealpha($canvas, $rgb['red'], $rgb['green'], $rgb['blue'], round(127 * $opacity));
				imagesetpixel($canvas, $x, $targetLineIndex, $color);
			}
		}
	}
}
