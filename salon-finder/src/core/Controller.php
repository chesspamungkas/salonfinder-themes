<?php 
namespace DV\core;

class Controller {

  public $queryVars = [];

  public static function init() {
    $className = get_called_class();
    $class = new $className();
    
    add_action( 'init', [$class, 'addRoutes']);
    add_action( 'init', [$class, 'addTags'], 10, 0);
    add_filter( 'query_vars', [$class, 'newQueryVars'] );
    add_action( 'template_redirect', [$class, 'templateRedirect'] );
  }

  public function templateRedirect() {

    
  }

  public function addTags() {
    foreach($this->queryVars as $var=>$exp) {
      add_rewrite_tag( '%'.$var.'%', $exp );
    }
  }

  public function addRoutes() {

  }

  public function newQueryVars($vars) {
    foreach($this->queryVars as $var=>$exp) {
      $vars[] = $var;
    }
    return $vars;
  }
}