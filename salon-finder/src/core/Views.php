<?php
namespace DV\core;

// use DV\core\Constants;

class Views {
  public static function render($viewFiles, $args=[], $returnContent=false) {
    ob_start();
    extract($args);
    include Constants::getViewBaseDir().$viewFiles.'.php';
    $content = ob_get_contents();
    ob_end_clean();
    if($returnContent)
      return $content;
    else 
      echo $content;
  }
}