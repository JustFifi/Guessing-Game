<?php

define("DEFAULT_FONT", "NellaSueDEMO.ttf");
define("DEFAULT_COLOR", "000000");
define("DEFAULT_SIZE", 48);
define("DEFAULT_ANGLE", 0);
define("DEFAULT_PADDING", 10);
define("DEFAULT_SPACING", 0);

$text = $_GET['text'];

$font = DEFAULT_FONT;
$color = "0x" . DEFAULT_COLOR;
$size = DEFAULT_SIZE;
$angle = DEFAULT_ANGLE;
$padding = DEFAULT_PADDING*4;
$spacing = DEFAULT_SPACING;
$text_dimensions = calculateTextDimensions($text, $font, $size, $angle, $spacing);
$image_width = $text_dimensions["width"] + $padding;
$image_height = $text_dimensions["height"] + $padding;

header("content-type: image/png");
$new_image = imagecreatetruecolor($image_width, $image_height);
ImageFill($new_image, 0, 0, IMG_COLOR_TRANSPARENT);
imagesavealpha($new_image, true);
imagealphablending($new_image, false);
imagettftextSp($new_image, $size, $angle, $text_dimensions["left"] + ($image_width / 2) - ($text_dimensions["width"] / 2), $text_dimensions["top"] + ($image_height / 2) - ($text_dimensions["height"] / 2), $color, $font, $text, $spacing);
imagepng($new_image);
imagedestroy($new_image); 

function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0)
{
    if ($spacing == 0)
    {
        imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
    }
    else
    {
        $temp_x = $x;
        for ($i = 0; $i < strlen($text); $i++)
        {
            $bbox = imagettftext($image, $size, $angle, $temp_x, $y, $color, $font, $text[$i]);
            $temp_x += $spacing + ($bbox[2] - $bbox[0]);
        }
    }
}

function calculateTextDimensions($text, $font, $size, $angle, $spacing) {
    $rect = imagettfbbox($size, $angle, $font, $text);
    $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
    $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
    $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
    $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));
    $spacing = ($spacing*(strlen($text)+2));
    return array(
     "left"   => abs($minX) - 1,
     "top"    => abs($minY) - 1,
     "width"  => ($maxX - $minX)+$spacing,
     "height" => $maxY - $minY,
     "box"    => $rect
    );
} 

?>