<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>Hello</h1>
  <?=
    el::form('@method=post @action=/users?message=!yahya is good',
      el::input('@type=text @name=name @value=yahya').
      el::br().
      el::input('@type=text @name=last name @value=hosainy').
      el::br().
      el::button('@type=submit',
        'Submit'
      )
    );
  ?>
</body>
</html>


