<?php
/**
 * @author Simon Jarvis, Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2006-2014 Simon Jarvis, Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
class ImageUtils {
   
   private $image;
   private $image_type;
 
   
   /**
    * @return ImageUtils
    */
   public static function create($filename, $filenameIsSource = false)
   {
   		return new self($filename, $filenameIsSource);
   }
   
   public function __construct($filename, $filenameIsSource = false)
   {
   		if($filenameIsSource) {
   			$this->image = $filename;
   		}
   		else {
			$this->load($filename);
   		}
   		
   		return $this;
   }
   
   
   /**
    * @return ImageUtils
    */   
   public function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);

      }
      
      return $this;
   }
   
   /**
    * @return ImageUtils
    */   
   public function save($filename, $permissions=null, $image_type=null, $compression=100) {
   	  if(!$image_type)
   	  	$image_type = $this->image_type;
   	  
      if($image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
      	 imagealphablending($this->image, false);
		 imagesavealpha($this->image,true);
         imagepng($this->image,$filename);
      }   
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   	  return $this;
   }
   
   
   public function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   
   
   public function getWidth() {
      return imagesx($this->image);
   }
   
   public function getHeight() {
      return imagesy($this->image);
   }
   
   public function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
      return $this;
   }
   public function resizeToHeightSmart($height) {
   	  if($height >= $this->getHeight()) {
   	  	 return $this;
   	  }   	
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
      return $this;
   }

   /**
    * @return ImageUtils
    */   
   public function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
      return $this;
   }
   
   /**
    * @return ImageUtils
    */   
   public function resizeToWidthSmart($width) {
   	  if($width >= $this->getWidth()) {
   	  	 return $this;
   	  }
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
      return $this;
   }

   /**
    * @return ImageUtils
    */   
   public function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
      return $this;
   }
   
   /**
    * @return ImageUtils
    */   
   public function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      
	  if(($this->image_type == IMAGETYPE_GIF) OR ($this->image_type == IMAGETYPE_PNG)){
		imagealphablending($new_image, false);
		imagesavealpha($new_image,true);
		$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
		imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
	  }
      
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
      return $this;
   }  
      
   /**
    * @return ImageUtils
    */   
   public function resizeSmart($width,$height) {
   	  if($this->getWidth() >= $this->getHeight()) {
   	  	 if($width < $this->getWidth()) {
   	  	 	$this->resizeToHeightSmart($height);
   	  	 	return $this->resizeToWidthSmart($width);
   	  	 }
   	  }    
   	  else {
   	  	 if($height < $this->getHeight()) {
   	  	 	$this->resizeToWidthSmart($width);
   	  	 	return $this->resizeToHeightSmart($height);
   	  	 }
   	  }
      return $this->resizeToWidthSmart($height);

   }      
}
?>