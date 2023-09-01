<?php
namespace DV\core;

class ThemeBase {
    static function init() {
        self::AddScript( 'DV_coreScript', get_template_directory_uri().'/src/.dist/index.js', 'jquery', true );
        self::AddStyle( 'DV_coreStyle', get_template_directory_uri().'/src/.dist/index.css' );
        add_action( 'wp_enqueue_scripts', [__CLASS__, 'registerScript'] );
        add_action( 'wp_enqueue_scripts', [__CLASS__, 'registerStyle'] );
    }

    public function __construct() {
        self::addSupport( 'title-tag' );
        self::addSupport( 'custom-logo' );
        self::addSupport( 'post-thumbnails' );
        self::addSupport( 'customize-selective-refresh-widgets' );
        self::addSupport( 'html5', [
                 'search-form',
                 'comment-form',
                 'comment-list',
                 'gallery',
                 'caption'
             ] );
    }

    private static $_registeredStyle = [
      'db-garamond-fonts'=>[ 'https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&display=swap' ],
      'poppins-fonts'=>[ 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap' ],
      'inter-fonts'=>[ 'https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap'],
      'jquery-ui-css'=>[ 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css' ],
      'boot-css'=>[ 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css' ]
    ];
  
    private static $_registeredScript = [
      'boot2'=>['https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', 'jquery', true],
      'boot3'=>['https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js', 'jquery', true],
      'jquery-ui'=>[ 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', 'jquery', true ],
    //   'fontawesome-js'=>[ '../js/fontawesome/all.min.js', 'jquery', true ]
    ];

    private static $_registeredInlineStyle = [
    ];
  
    private static $_registeredInlineScript = [
    ];

    public static function registerScript() {
      foreach(self::$_registeredScript as $key=>$script) {
        wp_register_script($key, $script[0], array_key_exists(1,$script)?$script[1]:[], DEPLOY_VERSION, array_key_exists(2,$script)?$script[2]:false);
        wp_enqueue_script($key, $script[0], array_key_exists(1,$script)?$script[1]:[], DEPLOY_VERSION, array_key_exists(2,$script)?$script[2]:false);
      }      
      foreach(self::$_registeredInlineScript as $key=>$script) {
        wp_add_inline_script($key, $script[0], array_key_exists(1,$script)?$script[1]:'after');
      }
    }
  
    public static function registerStyle() {
      foreach(self::$_registeredStyle as $key=>$style) {
        wp_enqueue_style($key, $style[0], array_key_exists(1,$style)?$style[1]:[], DEPLOY_VERSION, array_key_exists(2,$style)?$style[2]:false);
      }
    }
  
    public static function AddStyle($key, $link, $dependency=[], $footer=false) {
      self::$_registeredStyle = array_merge(self::$_registeredStyle, [$key=>[$link, $dependency, $footer]]);
    }

    public static function AddInlineStyle($key, $link, $dependency=[], $footer=false) {
      self::$_registeredStyle = array_merge(self::$_registeredStyle, [$key=>[$link, $dependency, $footer]]);
    }
  
    public static function DelStyle($key) {
      if(isset(self::$_registeredStyle[$key])) {
        unset(self::$_registeredStyle[$key]);
      }
    }
  
    public static function AddScript($key, $link, $dependency=[], $footer=false) {
      self::$_registeredScript = array_merge(self::$_registeredScript, [$key=>[$link, $dependency, $footer]]);
    }

    public static function AddInlineScript($handle, $scriptText, $position='after') {
      self::$_registeredInlineScript = array_merge(self::$_registeredInlineScript, [$handle=>[$scriptText, $position]]);
    }
  
    public static function DelScript($key) {
      if(isset(self::$_registeredScript[$key])) {
        unset(self::$_registeredScript[$key]);
      }
    }

    private static function actionAfterSetup( $function ) {
        add_action( 'after_setup_theme', function() use ( $function ) {
            $function();
        } );
    }

    public static function addNavMenus( $locations = array() ) {
        self::actionAfterSetup( function() use ( $locations ){
            register_nav_menus( $locations );
        });
    }

    public static function addSupport( $feature, $options = null ) {
        self::actionAfterSetup( function() use ( $feature, $options ) {
            if ( $options ) {
                add_theme_support( $feature, $options );
            } else {
                add_theme_support( $feature );
            }
        });
        // return $this;
    }

    public static function addImageSize( $name, $width, $height = null ) {
        self::actionAfterSetup( function() use ( $name, $width, $height ) {
            if( $height ) {
                add_image_size( $name, $width, $height );
            } else {
                add_image_size( $name, $width );
            }
        });

        // return $this;
    }
}