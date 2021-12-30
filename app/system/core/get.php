<?php

namespace get;

/**
 * function to get simply files path form root or false if not exists
 *
 * @param string $path
 *
 * @return string file path
 */

function get_(string $path, bool $user = false)
{
  if (
    !empty($path)
  ) {
    if (
      file_exists($path) and
      is_file($path)
    ) {
      return $path;
    } else {
      if (file_exists($path . '.php')) {
        return $path . '.php';
      } elseif (file_exists($path . '.inc.php')) {
        return $path . '.inc.php';
      } elseif (file_exists($path . '.phtml')) {
        return $path . '.phtml';
      } elseif (file_exists($path . '.html')) {
        return $path . '.html';
      } elseif (file_exists($path . '.htm')) {
        return $path . '.htm';
      } elseif (file_exists($path . '.inc')) {
        return $path . '.inc';
      } else {
        if ($user) {
          return false;
        } else {
          return $path;
        }
      }
    }
  }

  if ($user) {
    return false;
  } else {
    return $path;
  }
}

function _get_(string $path, $user = false)
{
  $path = trim($path);

  $path = _root_ . '/' . $path;

  return get_($path, $user);
}

function get_core(string $path, $user = false)
{
  $path = trim($path);

  $path = _core_ . '/' . $path;

  return get_($path, $user);
}

function get_controller(string $path, $user = false)
{
  $path = trim($path);

  $path = _root_ . '/dashboard/controllers/' . $path;

  return get_($path, $user);
}

function has_controller(string $path, $user = false)
{
  $path = trim($path);

  $path = _root_ . '/dashboard/controllers/' . $path;

  return get_($path, $user);
}

function get_config(string $path, $user = false)
{
  $path = trim($path);

  $path = _config_ . '/' . $path;

  return get_($path, $user);
}

function get_view(string $path, $user = false)
{
  $path = trim($path);

  $path = _pages_ . '/' . $path;

  return get_($path, $user);
}

function get_css(string $path, string $attr = '')
{
  $path = trim($path);

  $path = _pages_ . '/' . $path;

  if (
    file_exists($path)
  ) {
    $text = file_get_contents($path);
    return <<<TEXT
<style {$attr}>
  {$text}
</style>

TEXT;
  } else {
    return '<!-- CSS file not found in views -->';
  }
}

function get_js(string $path, string $attr = '')
{
  $path = trim($path);

  $path = _pages_ . '/' . $path;

  if (
    file_exists($path)
  ) {
    $text = file_get_contents($path);
    return <<<TEXT
<script {$attr}>
  {$text}
</script>

TEXT;
  } else {
    return '<!-- JavaScript file not found in views -->';
  }
}

function array_dump(array $arr = [])
{
  if ($arr) {
    echo '<ol>';
    foreach ($arr as $key => $value) {
      echo '<li><span style="color:gray">' . $key . '</span>' . ' : ' . $value . '</li>';
    }
    echo '</ol>';
  }
}
