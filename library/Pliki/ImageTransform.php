<?php

class ImageTransform {

  static private function _compute_size($imgWidth, $imgHeight, $frameWidth, $frameHeight, $scaleType) {
    //scaleType: 
    //  0 wpasowuje obrazek w ramke (z dokładnością co do krawędzi)
    //  1 jezeli obrazek jest mniejszy nie zmienia go
    //  2 rozciaga obrazek na cala ramke (znieksztalca)
    //  3 rozciaga obrazek na szerokosc
    //  4 rozciaga obrazek na wysokosc
    
    $rateWidth = $frameWidth/$imgWidth;
    $rateHeight = $frameHeight/$imgHeight;
    
    switch ($scaleType) {
      case 0:
        $rate = min($rateHeight, $rateWidth);
        $newHeight = $imgHeight * $rate;
        $newWidth = $imgWidth * $rate;
        break;
      case 1:
        if ($rateHeight > 1)
          $rateHeight = 1;
        if ($rateWidth > 1)
          $rateWidth = 1;
        $rate = min($rateHeight, $rateWidth);
        $newHeight = $imgHeight * $rate;
        $newWidth = $imgWidth * $rate;
        break;
      case 2:
        $newHeight = $imgHeight * $rateHeight;
        $newWidth = $imgWidth * $rateWidth;
        break;  
      case 3:
      	$newHeight = $imgHeight * $rateWidth;
      	$newWidth = $frameWidth;
      	break;
      case 4: 
      	$newHeight = $frameHeight;
      	$newWidth = $frameWidth * $rateHeight;
      	break;
    }
    
    return  array('width'=> $newWidth, 'height'=>$newHeight);
  }
  
  static private function _transform($fileName, $toWidth, $toHeight, $typeTransform) {
    //funkcja zwraca uchwyt do zasobu
    //zasob jest przeskalowanym odpowiednio obrazkiem
    $inImage = ImageCreateFromJPEG($fileName);
    $imgWidth = ImageSX($inImage); 
    $imgHeight = ImageSY($inImage);
    $size = ImageTransform::_compute_size($imgWidth, $imgHeight, $toWidth, $toHeight, $typeTransform);
    $outImage = ImageCreatetruecolor($size['width'],$size['height']);
    imagecopyresampled($outImage, $inImage, 0, 0, 0, 0, $size['width'], $size['height'], $imgWidth, $imgHeight);
    return $outImage;
  } 
  
  static public function copyTransformImage($srcName, $dstName, $toWidth, $toHeight, $qualityImage = 75, $typeTransform = 0) {
    //transformuje i kopiuje
    //jezeli jako $dstName podamy null wynik idzie na ekran, trzeba przeslac naglowek
    $imgRes = ImageTransform::_transform($srcName, $toWidth, $toHeight, $typeTransform);
    imagejpeg($imgRes, $dstName, $qualityImage); 
  }
  
  static public function scaleTransformImage($srcName, $toWidth, $toHeight, $qualityImage = 75, $typeTransform = 0) {
    //przeksztalca obrazek 'w miejscu'
    ImageTransform::copyTransformImage($srcName, $srcName, $toWidth, $toHeight, $qualityImage, $typeTransform);
  }
  
  static public function moveTransformImage($srcName, $dstName, $toWidth, $toHeight, $qualityImage = 75, $typeTransform = 0) {
    //przeksztalca i przesuwa obrazek
    ImageTransform::copyTransformImage($srcName, $dstName, $toWidth, $toHeight, $qualityImage, $typeTransform);
    if ($srcName != $dstName)
      @unlink($srcName); 
  }
  
}

?>