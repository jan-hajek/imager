<?php
namespace Imager;

class AddTextParams
{
	const ALIGN_LEFT = 1;
	const ALIGN_CENTER = 2;
	const ALIGN_RIGHT = 3;

	const EXPAND_NONE = 1;
	const EXPAND_HEIGHT = 3;

	/**
	 * pixels
	 * @var int
	 */
	public $size;
	/**
	 * int, use class Color
	 * @var int
	 */
	public $color;
	/**
	 * pixels
	 * @var int
	 */
	public $lineHeight;
	/**
	 * int, use constants
	 * @var int
	 */
	public $align;
	/**
	 * path to font file
	 * @var string
	 */
	public $fontFile;
	/**
	 * place width in pixels, for center or right align
	 * @var int
	 */
	public $width;
	/**
	 * @var int
	 */
	public $maxLines;
	/**
	 * pokud je text vetsi nez vkladana oblast tak ji zvetsi
	 * @var int
	 */
	public $expandSide = self::EXPAND_NONE;

	public function __construct($size, $color)
	{
		$this->size = $size;
		$this->color = $color;
		$this->lineHeight = $this->size;
		$this->fontFile = self::getDefaultFontPath();
	}
	
	/**
	 * @return string
	 */
	public static function getDefaultFontPath()
	{
		return __DIR__ . '/files/OpenSans-Regular.ttf';
	}

	/**
	 * @return string
	 */
	public static function getDefaultBoldFontPath()
	{
		return __DIR__ . '/files/OpenSans-Bold.ttf';
	}
}