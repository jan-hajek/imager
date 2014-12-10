<?php
namespace Imager\Filter\Reflection;

use Imager\Filter\Filter;
use Imager\Image;

class Reflection implements Filter
{
	/**
	 * @var float
	 */
	private $reflectionHeightRatio = 1;
	/**
	 * @var int
	 */
	private $startingTransparency = 70;

	/**
	 * @param float $reflectionHeightRatio
	 * @return $this
	 */
	public function setReflectionHeightRatio($reflectionHeightRatio)
	{
		$this->reflectionHeightRatio = $reflectionHeightRatio;
		return $this;
	}

	/**
	 * @param int $startingTransparency
	 * @return $this
	 */
	public function setStartingTransparency($startingTransparency)
	{
		$this->startingTransparency = $startingTransparency;
		return $this;
	}
	
	
	/**
	 * @param Image $image
	 * @return resource
	 */
	public function apply(Image $image)
	{
		$w = $image->getWidth();
		$h = $image->getHeight();
		
		$addedHeight = 36;
		$newHeight = $h + $addedHeight;
		$canvas = imagecreatetruecolor($w, $newHeight);
		imagecopyresampled($canvas, $image->getResource(), 0, $h, 0, 0, $w, $newHeight, $w, $newHeight);
		imagecopyresampled($canvas, $image->getResource(), 0, 0, 0, 0, $w, $h, $w, $h);

		$li = imagecreatetruecolor($w, 1);
		imagefilledrectangle($li, 0, 0, $w, 1, imagecolorallocate($li, 255, 255, 255));
		$rH = round($h * $this->reflectionHeightRatio);
		$tr = $this->startingTransparency;
		$in = 100 / $rH;
		for ($i = 0; $i <= $rH; $i++) {
			if ($tr > 100) $tr = 100;
			imagecopymerge($canvas, $li, 0, $h + $i, 0, 0, $w, 1, $tr);
			$tr += $in;
		}

		$transparencyImage = imagecreatefrompng(__DIR__ . '/images/transparency.png');
		imagecopyresampled($canvas, $transparencyImage, 0, $h, 0, 0, $w, $newHeight, $w, $newHeight);

		return $canvas;
	}	
	
}
