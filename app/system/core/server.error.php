<?php


if (
  !isset($details)
) {
  $details = 'no details' ;
}

$html = <<< HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Server Error</title>
  <style>
    body,html {
      margin: 0 ;
      padding: 0 20px ;
      background-color: rgb(34, 34, 34) ;
      color: rgb(216, 216, 216) ;
      font-size: 16px ;
      font-family: 'Courier New', Courier, monospace ;
    }
  </style>
</head>
<body>
  <h1>
    Error
  </h1>
  <p>
    PHP Frame error . <hr />
    <i>error details :</i>
    <i> <br />
      {$details}
    </i>
  </p>
</body>
</html>
HTML;

return $html ;

