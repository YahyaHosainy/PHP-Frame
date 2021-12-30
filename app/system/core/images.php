<?php
/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com> 
 * @Date: 2020-11-13
 * @Desc: images width and height controller
 */

class images
{
  private $valid_extensions = ['png', 'pns', 'jpeg', 'jpg', 'jpe', 'bmp', 'gif', 'svg', 'wbmp', 'webp'];
  private $data_array = [];
  public $image_permission_key_in_data_array;
  public $admin = false;
  private $images_dir;

  function __construct(
    array $data,
    string $images_dir,
    $image_permission_key_in_data_array,
    bool $admin = false
  ) {
    $this->data_array = $data;
    $this->images_dir = $images_dir;
    $this->image_permission_key_in_data_array = $image_permission_key_in_data_array;
    $this->admin = $admin;
  }

  public function show (
    $target_image_name,
    $image_width = 'full'
  ) {
    $image_name = trim(basename($target_image_name));
    $image_permission = $this->data_array[$image_name][$this->image_permission_key_in_data_array];
    if (
      (!empty($image_permission)) and
      (
        $image_permission == 1 or
        $image_permission == true or
        $this->admin
      )
    ) {
      $extension = strtolower(pathinfo($target_image_name, PATHINFO_EXTENSION));
      $backtrace = debug_backtrace();
      if (
        array_search($extension, $this->valid_extensions) !== false
      ) {
        if ($extension == 'svg') {
          header("Content-Type: image/svg+xml");
          echo (file_get_contents($this->images_dir . $image_name));
        } else {
          header("Content-Type: image/{$extension}");
          list($width, $height) = getimagesize($this->images_dir . $image_name);
          if ($image_width == 'full') {
            $newwidth = $width;
            $newheight = $height;
          } else {
            if ($image_width > $width) {
              $image_width = $width;
            }
            $AR = (float) $width / (float) $height;
            $newwidth = $image_width;
            $newheight = (float) $image_width / $AR;
          }
          $destination = imagecreatetruecolor($newwidth, $newheight);
          if ($extension == 'jpeg' or $extension == 'jpg' or $extension == 'jpe') {
            $source = imagecreatefromjpeg($this->images_dir . $image_name);
            imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagejpeg($destination);
          } elseif ($extension == 'png' or $extension == 'pns') {
            $source = imagecreatefrompng($this->images_dir . $image_name);
            imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagepng($destination);
          } elseif ($extension == 'gif') {
            $source = imagecreatefromgif($this->images_dir . $image_name);
            imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagegif($destination);
          } elseif ($extension == 'bmp') {
            $source = imagecreatefrombmp($this->images_dir . $image_name);
            imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagebmp($destination);
          } elseif ($extension == 'webp') {
            $source = imagecreatefromwebp($this->images_dir . $image_name);
            imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagewebp($destination);
          } elseif ($extension == 'wbmp') {
            $source = imagecreatefromwbmp($this->images_dir . $image_name);
            imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagewbmp($destination);
          }
        }
      } else {
        die('<p style="padding:10px;background-color:red;border-radius:5px;color:$000;font-size:18px"><b><code>frame\image_handler error : image not found in ' . $backtrace[0]['file'] . ' at line ' . $backtrace[0]['line'] . '</code></b></p>');
      }
    } else {
      header('HTTP/1.1 404 ERROR');
    }
  }
}
