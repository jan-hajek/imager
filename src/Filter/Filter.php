<?php
namespace Imager\Filter;

use Imager\Image;

interface Filter
{
	/**
	 * @param Image $image
	 * @return resource
	 */
	public function apply(Image $image);
}