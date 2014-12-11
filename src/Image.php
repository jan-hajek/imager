<?php
namespace Imager;

use Imager\Filter\Filter;
use Imager\Image\TextProcessor;

class Image
{
	/**
	 * @var resource
	 */
	private $resource;
	/**
	 * @var int
	 */
	private $width;
	/**
	 * @var int
	 */
	private $height;
	/**
	 * @var Coords
	 */
	private $lastCoords;
	/**
	 * @var int
	 */
	private $backgroundColor;

	/**
	 * @param int $width
	 * @param int $height
	 * @return Image
	 */
	public static function createNew($width, $height)
	{
		$resource = imagecreatetruecolor($width, $height);
		$image = new self($resource, $width, $height);
		$image->fillCanvasWithBackgroundColor();
		return $image;
	}

	/**
	 * @param $resource
	 * @return Image
	 */
	public static function createFromResource($resource)
	{
		list($width, $height) = self::getResourceDimensions($resource);
		return new self($resource, $width, $height);
	}
	
	/**
	 * @param string $path
	 * @return Image
	 */
	public static function createFromJpeg($path)
	{
		$resource = imagecreatefromjpeg($path);
		list($width, $height) = self::getFileDimensions($path);
		return new self($resource, $width, $height);
	}

	/**
	 * @param string $path
	 * @return Image
	 */
	public static function createFromPng($path)
	{
		$resource = imagecreatefrompng($path);
		list($width, $height) = self::getFileDimensions($path);
		return new self($resource, $width, $height);
	}

	/**
	 * @param string $path
	 * @throws \RuntimeException
	 * @return array
	 */
	private static function getFileDimensions($path)
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
	private static function getResourceDimensions($resource)
	{
		return array(imagesx($resource), imagesy($resource));
	}

	/**
	 * @param resource $resource
	 * @param int $width
	 * @param int $height
	 */
	private function __construct($resource, $width, $height)
	{
		$this->resource = $resource;
		$this->width = $width;
		$this->height = $height;
		$this->lastCoords = new Coords(0, 0);
		$this->backgroundColor = Color::createWhite();
	}

	public function __destruct()
	{
		imagedestroy($this->resource);
	}

	/**
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @return resource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @return Coords
	 */
	public function getLastCoords()
	{
		return clone $this->lastCoords;
	}

	/**
	 * @param int $color
	 * @return $this
	 */
	public function setBackgroundColor($color)
	{
		$this->backgroundColor = $color;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function fillCanvasWithBackgroundColor()
	{
		imagefilledrectangle($this->resource, 0, 0, $this->width, $this->height, $this->backgroundColor);
		return $this;
	}
	
	/**
	 * @param Image $image
	 * @param Coords $coords
	 */
	public function addImage(Image $image, Coords $coords)
	{
		imagecopy(
			$this->resource, 
			$image->getResource(), 
			$coords->getX(), 
			$coords->getY(), 
			0, 
			0, 
			$image->getWidth(), 
			$image->getHeight()
		);
	}

	/**
	 * @param string $text
	 * @param Coords $coords
	 * @param AddTextParams $params
	 */
	public function addText($text, Coords $coords, AddTextParams $params)
	{
		$textProcessor = new TextProcessor();
		$textProcessor->addText($this, $text, $coords, $params);
		$this->lastCoords = $textProcessor->getLastCoords();
	}

	/**
	 * @param string $path
	 * @param int|null $quality
	 */
	public function saveAsFile($path, $quality = 100)
	{
		imagejpeg($this->resource, $path, $quality);
	}

	/**
	 * @param int|null $quality
	 */
	public function renderToOutput($quality = 100)
	{
		imagejpeg($this->resource, null, $quality);
	}

	/**
	 * @param int $newHeight
	 */
	public function resizeCanvasHeight($newHeight)
	{
		$newImage = imagecreatetruecolor($this->width, $newHeight);
		imagefill($newImage, 0, 0, $this->backgroundColor);

		imagecopy($newImage, $this->resource, 0, 0, 0, 0, $this->width, $this->height);
		$this->resource = $newImage;
		$this->height = $newHeight;
	}

	
	/**
	 * aplikuje filter a ihned zmeni zdrojovy obrazek,
	 * 
	 * @param Filter $filter
	 */
	public function applyFilter(Filter $filter)
	{
		$this->resource = $filter->apply($this);
		list($this->width, $this->height) = self::getResourceDimensions($this->resource);
	}

}