<?php
	class Flyer extends AppModel
	{
		var $name = 'Flyer';
		var $displayField = 'friendly_name';
		
		function afterSave($created)
		{
			if ($created && class_exists('imagick'))
			{
				// create the thumbnail of the flyer after it's uploaded
				$pdf = WWW_ROOT . 'wsflyers' . DS . $this->data[$this->alias]['name'] . '[0]';
				$im = new imagick($pdf);
				$thumbDimMax = 200;
				
				// set the largest side to 200px
				$width = $im->getImageWidth();
				$height = $im->getImageHeight();
				if ($width > $height)
					$im->thumbnailImage($thumbDimMax, null);
				else
					$im->thumbnailImage(null, $thumbDimMax);
				
				// create a background
				$bg = new imagick();
				$bg->newImage($thumbDimMax, $thumbDimMax, new imagickpixel('#222222'));
				$bg->setImageFormat('png');
				
				// center thumbnail by composite
				$compX = 0;
				$compY = 0;
				$thumbWidth = $im->getImageWidth();
				$thumbHeight = $im->getImageHeight();
				
				if ($thumbHeight < $thumbDimMax)
					$compY = ($thumbDimMax - $thumbHeight) / 2;
				else if ($thumbWidth < $thumbDimMax)
					$compX = ($thumbDimMax - $thumbWidth) / 2;
				$bg->compositeImage($im, imagick::COMPOSITE_ATOP, $compX, $compY);
				
				$bg->writeImage(WWW_ROOT . 'wsflyers' . DS . 'thumbnails' . DS . $this->id . '.png');
			}
		}
	}
?>