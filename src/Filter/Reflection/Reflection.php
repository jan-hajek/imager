<?php
namespace Imager\Filter\Reflection;

use Imager\Color;
use Imager\Filter\Filter;
use Imager\Image;

class Reflection implements Filter
{
	/**
	 * @var int
	 */
	private $startTransparency = 70;
	/**
	 * @var int
	 */
	private $endTransparency = 100;
	/**
	 * 0.5 = half height reflection
	 * @var float
	 */
	private $reflectionHeightRatio = 0.15;

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
	 * @param float $reflectionHeightRatio
	 */
	public function setReflectionHeightRatio($reflectionHeightRatio)
	{
		$this->reflectionHeightRatio = $reflectionHeightRatio;
	}

	/**
	 * @param Image $image
	 * @return resource
	 */
	public function apply(Image $image)
	{
		$w = $image->getWidth();
		$h = $image->getHeight();
		$newHeight = $h + ($h * $this->reflectionHeightRatio);

		$canvas = $this->createNewCanvas($w, $newHeight);
		$this->placeOriginalImage($canvas, $image);
		$this->placeFlippedImage($canvas, $image);
		$this->addTransition($canvas, $image, $newHeight);

		return $canvas;
	}

	/**
	 * @param int $w
	 * @param int $h
	 * @return resource
	 */
	private function createNewCanvas($w, $h)
	{
		return imagecreatetruecolor($w, $h);
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
	 * odraz ve skle
	 * @param resource $canvas
	 * @param Image $image
	 */
	private function placeFlippedImage($canvas, Image $image)
	{
		$flippedImage = Image\Functions::cloneImage($image->getResource());
		Image\Functions::flipVertical($flippedImage);
		Image\Functions::placeImage($canvas, $flippedImage, 0, $image->getHeight());
	}

	/**
	 * @param resource $canvas
	 * @param Image $image
	 * @param int $end
	 */
	private function addTransition($canvas, Image $image, $end)
	{
		$start = $image->getHeight();
		$w = $image->getWidth();

		$whiteLine = imagecreatetruecolor($w, 1);
		imagefilledrectangle($whiteLine, 0, 0, $w, 1, Color::createWhite());

		$lineTransDiff = ($this->endTransparency - $this->startTransparency) / ($end - $start);
		$trans = $this->startTransparency;
		for ($line = $start; $line <= $end; $line++) {
			imagecopymerge($canvas, $whiteLine, 0, $line, 0, 0, $w, 1, round($trans));
			$trans += $lineTransDiff;
		}
	}
}
