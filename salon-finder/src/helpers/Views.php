<?php

namespace DV\helpers;

class Views {
  public static function render($viewFiles, $args=[], $returnContent=false) {
    ob_start();
    extract($args);
    include Constants::getBaseDir().$viewFiles.'.php';
    $content = ob_get_contents();
    ob_end_clean();
    if($returnContent)
      return $content;
    else 
      echo $content;
  }
}