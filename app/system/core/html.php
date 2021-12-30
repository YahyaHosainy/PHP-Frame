<?php
/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com> 
 * @Date: 2020-11-13
 * @Desc: html generator funcs
 */

/**
* Html print class
*/
class el {

  public static $after = [] ;
  public static $before = [] ;

  /**
   * Function for generate html code
   * this is an internal function don't use this function
   * 
   * @param string $name
   * @param mixed $attr
   * @param mixed $body
   * 
   * @return string html
   */
  public static function tag__(string $name,$attrs = [],$body = '',$open_tag = true)
  {
    $name = trim($name) ;
    
    if (
      !empty($name)
    ) {
      
      $html = '<'.$name ;

      if (
        is_array($attrs)
      ) {
        
        foreach ($attrs as $t_name => $value) {
          
          $html .= ' '.trim($t_name).'='.'"' . trim($value) . '"' ;
          
        }

      } elseif (
        is_string($attrs) and
        trim($attrs)[0] === '@'
      ) {
      
        $attrs = trim($attrs) ;
        
        $one = preg_split('/@(?!\!)/', $attrs, -1) ;

        $addinfront = false ;

        for ($i=1; $i < count($one); $i++) {

          $one_ = $one[$i] ;

          $one_ = str_replace('@!', '@', $one_);

          if (
            trim($one_) === ''
          ) {
            $addinfront = true ;
          } else {
            
            $two = preg_split('/=(?!\!)/', $one_, -1, PREG_SPLIT_NO_EMPTY) ;

            if (
              count($two) === 2
            ) {

              $two[0] = str_replace('=!', '=', $two[0]);
              $two[1] = str_replace('=!', '=', $two[1]);

              if ($addinfront) {
                $addinfront = false ;
                $html .= ' '.'@'.trim($two[0]).'="' . trim($two[1]) . '"' ;
              } else {
                $html .= ' '.trim($two[0]).'="' . trim($two[1]) . '"' ;
              }

            }

          }
          
        }
      
      } else {
        if (
          $open_tag
        ) {
          if (
            !is_string($attrs)
          ) {
            $attrs = '' ;
          }
          if (
            array_key_exists($name,self::$before) and
            is_string(self::$before[$name])
          ) {
            $attrs = self::$before[$name] . $attrs ;
          }
          if (
            is_callable($attrs)
          ) {
            $attrs = $attrs() ;
          }
          if (
            !is_string($attrs)
          ) {
            $attrs = '' ;
          }
          if (
            array_key_exists($name,self::$after) and
            is_string(self::$after[$name])
          ) {
            $attrs .= self::$after[$name] ;
          }
          $html .= sprintf('>%s</%s>',$attrs,$name) ;
        } else {
          $html .= ' />';
        }

        return $html ;
      }

      if (
        $open_tag
      ) {
        if (
          !is_string($body)
        ) {
          $body = '' ;
        }
        if (
          array_key_exists($name,self::$before) and
          is_string(self::$before[$name])
        ) {
          $body = self::$before[$name] . $body ;
        }
        if (
          is_callable($body)
        ) {
          $body .= $body() ;
        }
        if (
          !is_string($body)
        ) {
          $body = '' ;
        }
        if (
          array_key_exists($name,self::$after) and
          is_string(self::$after[$name])
        ) {
          $body .= self::$after[$name] ;
        }
        $html .= sprintf('>%s</%s>',$body,$name) ;
      } else {
        $html .= ' />';
      }

      return $html ;
    
    }
  }

  // https://www.w3.org/QA/2002/04/valid-dtd-list
  const DOCTYPES = [
    'xhtml11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" />',
    'xhtml1-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" />',
    'xhtml1-trans' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />',
    'xhtml1-frame' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd" />',
    'xhtml-basic11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd" />',
    
    'html5' => '<!DOCTYPE html />',
    
    'html4-strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd" />',
    'html4-trans' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd" />',
    'html4-frame' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd" />',
    'mathml1' => '<!DOCTYPE math SYSTEM "http://www.w3.org/Math/DTD/mathml1/mathml.dtd" />',
    'mathml2' => '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd" />',
    'svg10' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd" />',
    'svg11' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd" />',
    'svg11-basic' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Basic//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-basic.dtd" />',
    'svg11-tiny' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Tiny//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-tiny.dtd" />',
    'xhtml-math-svg-xh' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd" />',
    'xhtml-math-svg-sh' => '<!DOCTYPE svg:svg PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd" />',
    'xhtml-rdfa-1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd" />',
    'xhtml-rdfa-2' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-2.dtd" />'
  ];

  const BOOTSTRAP_CSS = <<<HTML
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous" />
  HTML;

  const BOOTSTRAP_JS =<<<HTML
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
  HTML;

  // close tags
  public static function DOCTYPE(string $type = '')
  {
    if (
      array_key_exists($type,self::DOCTYPES)
    ) {
      return self::DOCTYPES[$type] ;
    } else {
      return self::DOCTYPES['html5'];
    }
  }

  public static function meta_charset($char = 'UTF-8')
  {
    return '<meta charset="'.$char.'" />' ;
  }

  public static function meta_viewport($width = 'device-width' , $scale = '1.0')
  {
    return "<meta name=\"viewport\" content=\"width={$width}, initial-scale={$scale}\" />" ;
  }

  public static function meta_description(string $description)
  {
    return '<meta name="description" content="'.$description.'" />' ;
  }

  public static function meta_keywords(string $keys)
  {
    return '<meta name="keywords" content="'.$keys.'" />' ;
  }

  public static function mate_author(string $name)
  {
    return '<meta name="author" content="'.$name.'" />' ;
  }

  public static function meta_refresh(int $seconds)
  {
    return '<meta http-equiv="refresh" content="'.$seconds.'" />' ;
  }

  public static function input ($attrs=[]) {
    return self::tag__('input', $attrs, '', false) ;
  }
  public static function area ($attrs=[]) {
    return self::tag__('area', $attrs, '', false) ;
  }
  public static function base ($attrs=[]) {
    return self::tag__('base', $attrs, '', false) ;
  }
  public static function br ($attrs=[]) {
    return self::tag__('br', $attrs, '', false) ;
  }
  public static function col ($attrs=[]) {
    return self::tag__('col', $attrs, '', false) ;
  }
  public static function embed ($attrs=[]) {
    return self::tag__('embed', $attrs, '', false) ;
  }
  public static function hr ($attrs=[]) {
    return self::tag__('hr', $attrs, '', false) ;
  }
  public static function img ($attrs=[]) {
    return self::tag__('img', $attrs, '', false) ;
  }
  public static function link ($attrs=[]) {
    return self::tag__('link', $attrs, '', false) ;
  }
  public static function meta ($attrs=[]) {
    return self::tag__('meta', $attrs, '', false) ;
  }
  public static function param ($attrs=[]) {
    return self::tag__('param', $attrs, '', false) ;
  }
  public static function source ($attrs=[]) {
    return self::tag__('source', $attrs, '', false) ;
  }
  public static function track ($attrs=[]) {
    return self::tag__('track', $attrs, '', false) ;
  }
  public static function wbr ($attrs=[]) {
    return self::tag__('wbr', $attrs, '', false) ;
  }
  public static function command ($attrs=[]) {
    return self::tag__('command', $attrs, '', false) ;
  }
  public static function keygen ($attrs=[]) {
    return self::tag__('keygen', $attrs, '', false) ;
  }
  public static function menuitem ($attrs=[]) {
    return self::tag__('menuitem', $attrs, '', false) ;
  }

  // open tags
  public static function div ($attrs = [],$body='') {
    return self::tag__('div', $attrs, $body) ;
  }
  public static function a ($attrs = [],$body='') {
    return self::tag__('a' ,$attrs, $body) ;
  }
  public static function abbr ($attrs = [],$body='') {
    return self::tag__('abbr' ,$attrs, $body) ;
  }
  public static function acronym ($attrs = [],$body='') {
    return self::tag__('acronym' ,$attrs, $body) ;
  }
  public static function address ($attrs = [],$body='') {
    return self::tag__('address' ,$attrs, $body) ;
  }
  public static function applet ($attrs = [],$body='') {
    return self::tag__('applet' ,$attrs, $body) ;
  }
  public static function object ($attrs = [],$body='') {
    return self::tag__('object' ,$attrs, $body) ;
  }
  public static function article ($attrs = [],$body='') {
    return self::tag__('article' ,$attrs, $body) ;
  }
  public static function aside ($attrs = [],$body='') {
    return self::tag__('aside' ,$attrs, $body) ;
  }
  public static function audio ($attrs = [],$body='') {
    return self::tag__('audio' ,$attrs, $body) ;
  }
  public static function b ($attrs = [],$body='') {
    return self::tag__('b' ,$attrs, $body) ;
  }
  public static function basefont ($attrs = [],$body='') {
    return self::tag__('basefont' ,$attrs, $body) ;
  }
  public static function bdi ($attrs = [],$body='') {
    return self::tag__('bdi' ,$attrs, $body) ;
  }
  public static function bdo ($attrs = [],$body='') {
    return self::tag__('bdo' ,$attrs, $body) ;
  }
  public static function big ($attrs = [],$body='') {
    return self::tag__('big' ,$attrs, $body) ;
  }
  public static function blockquote ($attrs = [],$body='') {
    return self::tag__('blockquote' ,$attrs, $body) ;
  }
  public static function body ($attrs = [],$body='') {
    return self::tag__('body' ,$attrs, $body) ;
  }
  public static function button ($attrs = [],$body='') {
    return self::tag__('button' ,$attrs, $body) ;
  }
  public static function canvas ($attrs = [],$body='') {
    return self::tag__('canvas' ,$attrs, $body) ;
  }
  public static function caption ($attrs = [],$body='') {
    return self::tag__('caption' ,$attrs, $body) ;
  }
  public static function center ($attrs = [],$body='') {
    return self::tag__('center' ,$attrs, $body) ;
  }
  public static function cite ($attrs = [],$body='') {
    return self::tag__('cite' ,$attrs, $body) ;
  }
  public static function code ($attrs = [],$body='') {
    return self::tag__('code' ,$attrs, $body) ;
  }
  public static function colgroup ($attrs = [],$body='') {
    return self::tag__('colgroup' ,$attrs, $body) ;
  }
  public static function data ($attrs = [],$body='') {
    return self::tag__('data' ,$attrs, $body) ;
  }
  public static function datalist ($attrs = [],$body='') {
    return self::tag__('datalist' ,$attrs, $body) ;
  }
  public static function dd ($attrs = [],$body='') {
    return self::tag__('dd' ,$attrs, $body) ;
  }
  public static function del ($attrs = [],$body='') {
    return self::tag__('del' ,$attrs, $body) ;
  }
  public static function details ($attrs = [],$body='') {
    return self::tag__('details' ,$attrs, $body) ;
  }
  public static function dfn ($attrs = [],$body='') {
    return self::tag__('dfn' ,$attrs, $body) ;
  }
  public static function dialog ($attrs = [],$body='') {
    return self::tag__('dialog' ,$attrs, $body) ;
  }
  public static function dir ($attrs = [],$body='') {
    return self::tag__('dir' ,$attrs, $body) ;
  }
  public static function ul ($attrs = [],$body='') {
    return self::tag__('ul' ,$attrs, $body) ;
  }
  public static function dl ($attrs = [],$body='') {
    return self::tag__('dl' ,$attrs, $body) ;
  }
  public static function dt ($attrs = [],$body='') {
    return self::tag__('dt' ,$attrs, $body) ;
  }
  public static function em ($attrs = [],$body='') {
    return self::tag__('em' ,$attrs, $body) ;
  }
  public static function fieldset ($attrs = [],$body='') {
    return self::tag__('fieldset' ,$attrs, $body) ;
  }
  public static function figcaption ($attrs = [],$body='') {
    return self::tag__('figcaption' ,$attrs, $body) ;
  }
  public static function figure ($attrs = [],$body='') {
    return self::tag__('figure' ,$attrs, $body) ;
  }
  public static function font ($attrs = [],$body='') {
    return self::tag__('font' ,$attrs, $body) ;
  }
  public static function footer ($attrs = [],$body='') {
    return self::tag__('footer' ,$attrs, $body) ;
  }
  public static function form ($attrs = [],$body='') {
    return self::tag__('form' ,$attrs, $body) ;
  }
  public static function frame ($attrs = [],$body='') {
    return self::tag__('frame' ,$attrs, $body) ;
  }
  public static function frameset ($attrs = [],$body='') {
    return self::tag__('frameset' ,$attrs, $body) ;
  }
  public static function h1 ($attrs = [],$body='') {
    return self::tag__('h1' ,$attrs, $body) ;
  }
  public static function h2 ($attrs = [],$body='') {
    return self::tag__('h2' ,$attrs, $body) ;
  }
  public static function h3 ($attrs = [],$body='') {
    return self::tag__('h3' ,$attrs, $body) ;
  }
  public static function h4 ($attrs = [],$body='') {
    return self::tag__('h4' ,$attrs, $body) ;
  }
  public static function h5 ($attrs = [],$body='') {
    return self::tag__('h5' ,$attrs, $body) ;
  }
  public static function h6 ($attrs = [],$body='') {
    return self::tag__('h6' ,$attrs, $body) ;
  }
  public static function head ($attrs = [],$body='') {
    return self::tag__('head' ,$attrs, $body) ;
  }
  public static function header ($attrs = [],$body='') {
    return self::tag__('header' ,$attrs, $body) ;
  }
  public static function html ($attrs = [],$body='') {
    return self::tag__('html' ,$attrs, $body) ;
  }
  public static function i ($attrs = [],$body='') {
    return self::tag__('i' ,$attrs, $body) ;
  }
  public static function iframe ($attrs = [],$body='') {
    return self::tag__('iframe' ,$attrs, $body) ;
  }
  public static function ins ($attrs = [],$body='') {
    return self::tag__('ins' ,$attrs, $body) ;
  }
  public static function kbd ($attrs = [],$body='') {
    return self::tag__('kbd' ,$attrs, $body) ;
  }
  public static function label ($attrs = [],$body='') {
    return self::tag__('label' ,$attrs, $body) ;
  }
  public static function legend ($attrs = [],$body='') {
    return self::tag__('legend' ,$attrs, $body) ;
  }
  public static function li ($attrs = [],$body='') {
    return self::tag__('li' ,$attrs, $body) ;
  }
  public static function main ($attrs = [],$body='') {
    return self::tag__('main' ,$attrs, $body) ;
  }
  public static function map ($attrs = [],$body='') {
    return self::tag__('map' ,$attrs, $body) ;
  }
  public static function mark ($attrs = [],$body='') {
    return self::tag__('mark' ,$attrs, $body) ;
  }
  public static function meter ($attrs = [],$body='') {
    return self::tag__('meter' ,$attrs, $body) ;
  }
  public static function nav ($attrs = [],$body='') {
    return self::tag__('nav' ,$attrs, $body) ;
  }
  public static function noframes ($attrs = [],$body='') {
    return self::tag__('noframes' ,$attrs, $body) ;
  }
  public static function noscript ($attrs = [],$body='') {
    return self::tag__('noscript' ,$attrs, $body) ;
  }
  public static function ol ($attrs = [],$body='') {
    return self::tag__('ol' ,$attrs, $body) ;
  }
  public static function optgroup ($attrs = [],$body='') {
    return self::tag__('optgroup' ,$attrs, $body) ;
  }
  public static function option ($attrs = [],$body='') {
    return self::tag__('option' ,$attrs, $body) ;
  }
  public static function output ($attrs = [],$body='') {
    return self::tag__('output' ,$attrs, $body) ;
  }
  public static function p ($attrs = [],$body='') {
    return self::tag__('p' ,$attrs, $body) ;
  }
  public static function picture ($attrs = [],$body='') {
    return self::tag__('picture' ,$attrs, $body) ;
  }
  public static function pre ($attrs = [],$body='') {
    return self::tag__('pre' ,$attrs, $body) ;
  }
  public static function progress ($attrs = [],$body='') {
    return self::tag__('progress' ,$attrs, $body) ;
  }
  public static function q ($attrs = [],$body='') {
    return self::tag__('q' ,$attrs, $body) ;
  }
  public static function rp ($attrs = [],$body='') {
    return self::tag__('rp' ,$attrs, $body) ;
  }
  public static function rt ($attrs = [],$body='') {
    return self::tag__('rt' ,$attrs, $body) ;
  }
  public static function ruby ($attrs = [],$body='') {
    return self::tag__('ruby' ,$attrs, $body) ;
  }
  public static function s ($attrs = [],$body='') {
    return self::tag__('s' ,$attrs, $body) ;
  }
  public static function samp ($attrs = [],$body='') {
    return self::tag__('samp' ,$attrs, $body) ;
  }
  public static function script ($attrs = [],$body='') {
    return self::tag__('script' ,$attrs, $body) ;
  }
  public static function section ($attrs = [],$body='') {
    return self::tag__('section' ,$attrs, $body) ;
  }
  public static function select ($attrs = [],$body='') {
    return self::tag__('select' ,$attrs, $body) ;
  }
  public static function small ($attrs = [],$body='') {
    return self::tag__('small' ,$attrs, $body) ;
  }
  public static function video ($attrs = [],$body='') {
    return self::tag__('video' ,$attrs, $body) ;
  }
  public static function span ($attrs = [],$body='') {
    return self::tag__('span' ,$attrs, $body) ;
  }
  public static function strike ($attrs = [],$body='') {
    return self::tag__('strike' ,$attrs, $body) ;
  }
  public static function strong ($attrs = [],$body='') {
    return self::tag__('strong' ,$attrs, $body) ;
  }
  public static function style ($attrs = [],$body='') {
    return self::tag__('style' ,$attrs, $body) ;
  }
  public static function sub ($attrs = [],$body='') {
    return self::tag__('sub' ,$attrs, $body) ;
  }
  public static function summary ($attrs = [],$body='') {
    return self::tag__('summary' ,$attrs, $body) ;
  }
  public static function sup ($attrs = [],$body='') {
    return self::tag__('sup' ,$attrs, $body) ;
  }
  public static function svg ($attrs = [],$body='') {
    return self::tag__('svg' ,$attrs, $body) ;
  }
  public static function table ($attrs = [],$body='') {
    return self::tag__('table' ,$attrs, $body) ;
  }
  public static function tbody ($attrs = [],$body='') {
    return self::tag__('tbody' ,$attrs, $body) ;
  }
  public static function td ($attrs = [],$body='') {
    return self::tag__('td' ,$attrs, $body) ;
  }
  public static function template ($attrs = [],$body='') {
    return self::tag__('template' ,$attrs, $body) ;
  }
  public static function textarea ($attrs = [],$body='') {
    return self::tag__('textarea' ,$attrs, $body) ;
  }
  public static function tfoot ($attrs = [],$body='') {
    return self::tag__('tfoot' ,$attrs, $body) ;
  }
  public static function th ($attrs = [],$body='') {
    return self::tag__('th' ,$attrs, $body) ;
  }
  public static function thead ($attrs = [],$body='') {
    return self::tag__('thead' ,$attrs, $body) ;
  }
  public static function time ($attrs = [],$body='') {
    return self::tag__('time' ,$attrs, $body) ;
  }
  public static function title ($attrs = [],$body='') {
    return self::tag__('title' ,$attrs, $body) ;
  }
  public static function tr ($attrs = [],$body='') {
    return self::tag__('tr' ,$attrs, $body) ;
  }
  public static function tt ($attrs = [],$body='') {
    return self::tag__('tt' ,$attrs, $body) ;
  }
  public static function u ($attrs = [],$body='') {
    return self::tag__('u' ,$attrs, $body) ;
  }
  public static function var ($attrs = [],$body='') {
    return self::tag__('var' ,$attrs, $body) ;
  }

}
