<?php
namespace Imager;

class Coords
{
	/**
	 * @var int
	 */
	private $x;
	/**
	 * @var int
	 */
	private $y;

	/**
	 * @param int $x
	 * @param int $y
	 */
	function __construct($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 * @param int $x
	 * @return $this
	 */
	public function setX($x)
	{
		$this->x = $x;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getX()
	{
		return $this->x;
	}

	/**
	 * @param int $y
	 * @return $this
	 */
	public function setY($y)
	{
		$this->y = $y;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getY()
	{
		return $this->y;
	}

	/**
	 * @param int $value
	 * @return $this
	 */
	public function addX($value)
	{
		$this->x += $value;
		return $this;
	}

	/**
	 * @param int $value
	 * @return $this
	 */
	public function addY($value)
	{
		$this->y += $value;
		return $this;
	}
}
