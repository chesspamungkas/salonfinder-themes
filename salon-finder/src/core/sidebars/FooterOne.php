<?php 
namespace DV\core\sidebars;

// use DV\core\SideBar;

class FooterOne {
  public static function init() {
    return array(
      // SideBar::$commonArgs,
        'name'        => "Footer #1",
        'id'          => "footer-1",
        'description' => "One Column Footer #1",
    );
  }
}