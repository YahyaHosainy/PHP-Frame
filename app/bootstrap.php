<?php
/** 
 * @Author:  Yahya Hosainy <yahyayakta@gmail.com>
 * @Date:    2020-11-09
 * @Desc:    main boorstrap file
 */


ROOT || die() ;

// set constant route to this file route
define('_root_', __DIR__);


// set constant core
define('_core_', __DIR__ . '/system/core');


// set constant config
define('_config_', __DIR__ . '/system/config');


// set constant pages views
define('_pages_', __DIR__ . '/dashboard/views');


// require get file to use
require_once _root_ . '/system/core/get.php' ;


// get interface
require_once get\get_core('interfaces.php');


// get auto loader of composer
require_once get\_get_('vendor/autoload');


// get tools class
require_once get\get_core('tools');


// get browser class
require_once get\get_core('browser');


// get security class
require_once get\get_core('security');


// get databse engine
require_once get\get_core('PDO') ;


// require html generator
require_once get\get_core('html');


// get sesiosn class
require_once get\get_core('session');


// get auth namespace
require_once get\get_core('auth.php');


// setup
require_once get\get_core('setup');


// get image handler class
require_once get\get_core('images');


// get main controller
require_once get\get_core('controller');


// get functions
require_once get\get_core('functions');


// get user functions
require_once get\_get_('dashboard/functions');


// get user controllers
require_once get\get_core('controllers');


// get route handler class
require_once get\get_core('route');


// get user routes
require_once get\_get_('dashboard/routes');


// default 404 route
// you can override this in your route pages
route::get_route('$404',function(){
  get_404();
});

