<?php
namespace DV\core;

class InitCommand {
  static function init() {
      
  }
  private static $_registeredFilters = [
    
  ];

  private static $_registeredRemovingFilters = [
    
  ];


  private static $_registeredActions = [
    
  ];

  private static $_registeredRemovingActions = [
    
  ];

  public static function registerScript() {
    foreach(self::$_registeredActions as $action) {
      add_action()
    } 
  }

  public static function AddAction($hookName, $commandName, $prio=10) {
    self::$_registeredActions = array_merge(self::$_registeredActions, [[$hookName, $commandName, $prio]]);
  }

  public static function RemoveAction($hookName, $commandName, $prio=10) {
    self::$_registeredRemovingActions = array_merge(self::$_registeredRemovingActions, [[$hookName, $commandName, $prio]]);
  }


  public static function AddFilter($hookName, $commandName) {
    self::$_registeredFilters = array_merge(self::$_registeredFilters, [[$hookName, $commandName]]);
  }

  public static function RemoveFilter($hookName, $commandName) {
    self::$_registeredRemovingFilters = array_merge(self::$_registeredRemovingFilters, [[$hookName, $commandName]]);
  }
}