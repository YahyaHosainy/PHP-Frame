<?php

foreach (scandir(get\_get_('dashboard/controllers/')) as $controller) {
  
  if (
    $controller !== '.' and
    $controller !== '..'
  ) {
    
    require_once get\_get_('dashboard/controllers/'.$controller) ;
    
  }

}
