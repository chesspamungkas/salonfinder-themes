<?php 
namespace DV\core\sidebars;

// use DV\core\SideBar;

class FooterTwo {
  public static function init() {
    return array(
      // SideBar::$commonArgs,
        'name'        => "Footer #2",
        'id'          => "footer-2",
        'description' => "One Column Footer #2",
    );
  }
}