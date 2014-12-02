<?php
namespace Imager\Filter\Shadow;

use Imager\Filter\Filter;
use Imager\Image;

class Shadow implements Filter
{
	private $w = 15;
	private $h = 1;

	private $imageDirectory;

	public function __construct()
	{
		$this->imageDirectory = __DIR__ . '/images';
	}

	/**
	 * @param Image $image
	 * @return resource
	 */
	public function apply(Image $image)
	{
		$originalHeight = $image->getHeight();
		$originalWidth = $image->getWidth();

		$canvasHeight = $originalHeight + (2 * $this->w);
		$canvasWidth = $originalWidth + (2 * $this->w);
		$canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
		imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));

		$this->addBorders($canvas, $originalWidth, $originalHeight, $canvasHeight, $canvasWidth);
		$this->addCorners($canvas, $canvasHeight, $canvasWidth);

		imagecopyresampled(
			$canvas,
			$image->getResource(),
			$this->w,
			$this->w,
			0,
			0,
			$originalWidth,
			$originalHeight,
			$originalWidth,
			$originalHeight
		);
		return $canvas;
	}

	/**
	 * @param $canvas
	 * @param $imageWidth
	 * @param $imageHeight
	 * @param $canvasHeight
	 * @param $canvasWidth
	 * @return int
	 */
	protected function addBorders($canvas, $imageWidth, $imageHeight, $canvasHeight, $canvasWidth)
	{
		$w = $this->w;
		$h = $this->h;

		$cornerPadding = 10;
		$cornerPadding2 = 20;

		// + 1 a -1 jsou kvuli tomu, ze rohy musi sedetna rohu obrazku, tim padem okraje musim posunout taky 
		// aby nechybelo a ani neprekryvalo

		$t = imagecreatefrompng($this->imageDirectory . "/shadow_T.png");
		imagecopyresized($canvas, $t, $w + $cornerPadding, 0, 0, 0, $imageWidth - $cornerPadding2 + 1, $w, $h, $w);

		$r = imagecreatefrompng($this->imageDirectory . "/shadow_R.png");
		imagecopyresized(
			$canvas,
			$r,
			$canvasWidth - $w,
			$w + $cornerPadding,
			0,
			0,
			$w,
			$imageHeight - $cornerPadding2,
			$w,
			$h
		);

		$b = imagecreatefrompng($this->imageDirectory . "/shadow_B.png");
		imagecopyresized(
			$canvas,
			$b,
			$w + $cornerPadding + 1,
			$canvasHeight - $w,
			0,
			0,
			$imageWidth - $cornerPadding2 - 1,
			$w,
			$h,
			$w
		);

		$l = imagecreatefrompng($this->imageDirectory . "/shadow_L.png");
		imagecopyresized(
			$canvas,
			$l,
			0,
			$w + $cornerPadding,
			0,
			0,
			$w,
			$imageHeight - $cornerPadding2,
			$w,
			$h
		);
	}

	/**
	 * @param $canvas
	 * @param $canvasHeight
	 * @param $canvasWidth
	 * @return int
	 */
	protected function addCorners($canvas, $canvasHeight, $canvasWidth)
	{
		$w = 25;
		$h = 25;
		$tl = imagecreatefrompng($this->imageDirectory . "/shadow_TL.png");
		imagecopyresized($canvas, $tl, 0, 0, 0, 0, $w, $h, $w, $h);

		$bl = imagecreatefrompng($this->imageDirectory . "/shadow_BL.png");
		imagecopyresized($canvas, $bl, 1, $canvasHeight - $w, 0, 0, $w, $h, $w, $h);

		$br = imagecreatefrompng($this->imageDirectory . "/shadow_BR.png");
		imagecopyresized($canvas, $br, $canvasWidth - $w, $canvasHeight - $h, 0, 0, $w, $h, $w, $h);
//		
		$tr = imagecreatefrompng($this->imageDirectory . "/shadow_TR.png");
		imagecopyresized($canvas, $tr, $canvasWidth - $w + 1, 0, 0, 0, $w, $h, $w, $h);
	}
}