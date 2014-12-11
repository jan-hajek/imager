<?php
namespace Imager\Image;

use Imager\AddTextParams;
use Imager\Coords;
use Imager\Image;

class TextProcessor
{
	/**
	 * @var Coords
	 */
	private $lastCoords;

	/**
	 * @param Image $image
	 * @param string $text
	 * @param Coords $coords
	 * @param AddTextParams $params
	 */
	public function addText(Image $image, $text, Coords $coords, AddTextParams $params)
	{
		$placeWidth = $params->width === null ? ($image->getWidth() - $coords->getX()) : $params->width;

		$this->lastCoords = $coords;
		$lines = $this->getLines($text, $placeWidth, $params);
		if (count($lines) == 0) {
			return;
		}
		
		if ($params->expandSide == AddTextParams::EXPAND_HEIGHT) {
			$newHeight = $coords->getY() + count($lines) * $params->lineHeight;
			if ($newHeight > $image->getHeight()) {
				$image->resizeCanvasHeight($newHeight);
			}
		}
		
		foreach ($lines as $line) {
			$this->renderLine($image, $params, $line, $placeWidth);
		}
	}

	/**
	 * @param $text
	 * @param int $placeWidth
	 * @param AddTextParams $params
	 * @return array
	 */
	private function getLines($text, $placeWidth,  AddTextParams $params)
	{
		$text = trim($text);
		if (mb_strlen($text, 'UTF-8') == 0) {
			return array();
		}
		$lines = array();
		foreach (preg_split('~\n~', $text) as $splitLine) {
			unset($currentLine);
			$words = preg_split('~\s+~', $splitLine);

			$currentLine = array_shift($words);
			$lines[] = & $currentLine;
			foreach ($words as $word) {
				$textWidth = $this->getTextWidth($currentLine . ' ' . $word, $params->size, $params->fontFile);
				if ($textWidth < $placeWidth) {
					$currentLine .= ' ' . $word;
				} else {
					if ($params->maxLines !== null && count($lines) == $params->maxLines) {
						return $lines;
					}
					unset($currentLine);
					$currentLine = $word;
					$lines[] = & $currentLine;
				}
			}
		}
		return $lines;
	}


	/**
	 * @param string $text
	 * @param float $size
	 * @param null|string $fontFile
	 * @return mixed
	 */
	private function getTextWidth($text, $size, $fontFile)
	{
		return imagettfbbox($size, 0, $fontFile, $text)[2];
	}

	/**
	 * @return Coords
	 */
	public function getLastCoords()
	{
		return $this->lastCoords;
	}

	/**
	 * @param Image $image
	 * @param AddTextParams $params
	 * @param string $text
	 * @param int $placeWidth
	 */
	private function renderLine(Image $image, AddTextParams $params, $text, $placeWidth)
	{
		$leftPadding = $this->getLeftPadding($params, $text, $placeWidth);
		// velikost radku je 30, velikost pisma je 16, takze bude 7px nad a pod textem
		$space = round(($params->lineHeight - $params->size) / 2);
		imagettftext(
			$image->getResource(), 
			$params->size, 
			0, 
			$this->lastCoords->getX() + $leftPadding, 
			$this->lastCoords->getY() + $params->lineHeight - $space, 
			$params->color, 
			$params->fontFile, 
			$text
		);
		$this->lastCoords->addY($params->lineHeight);
	}

	/**
	 * @param AddTextParams $params
	 * @param $text
	 * @param int $placeWidth
	 * @return int
	 */
	private function getLeftPadding(AddTextParams $params, $text, $placeWidth)
	{
		$leftPadding = 0;
		if ($params->align == AddTextParams::ALIGN_RIGHT) {
			$textWidth = $this->getTextWidth($text, $params->size, $params->fontFile);
			$leftPadding = $placeWidth - $textWidth;
			return $leftPadding;
		} elseif ($params->align == AddTextParams::ALIGN_CENTER) {
			$textWidth = $this->getTextWidth($text, $params->size, $params->fontFile);
			$leftPadding = ($placeWidth - $textWidth) / 2;
			return $leftPadding;
		}
		return round($leftPadding);
	}
}