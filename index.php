<?php

/**
 * Step 1: Require the Slim PHP 5 Framework
 *
 * If using the default file layout, the `Slim/` directory
 * will already be on your include path. If you move the `Slim/`
 * directory elsewhere, ensure that it is added to your include path
 * or update this file path as needed.
 */
require 'Slim/Slim.php';
require 'lib/done_list.php';

/**
 * Step 2: Instantiate the Slim application
 *
 * Here we instantiate the Slim application with its default settings.
 * However, we could also pass a key-value array of settings.
 * Refer to the online documentation for available settings.
 */
$app = new Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function. If you are using PHP < 5.3, the
 * second argument should be any variable that returns `true` for
 * `is_callable()`. An example GET route for PHP < 5.3 is:
 *
 * $app = new Slim();
 * $app->get('/hello/:name', 'myFunction');
 * function myFunction($name) { echo "Hello, $name"; }
 *
 * The routes below work with PHP >= 5.3.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

//GET route
$app->get('/', function () {
  $items = array();
  $done = DoneList::find_all();
  foreach($done as $item){
    array_push($items, '<li>'.$item['created_at'].' '.$item['subject'].'</li>');
  }
  $done_list = implode('', $items);
  $template = <<<EOT
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>Done List</title>
  </head>
  <body>
    <header>
      <a href="http://www.slimframework.com"><img src="logo.png" alt="Slim"/></a>
    </header>
    <h1>Done!</h1>
    <ol>
      $done_list
    </ol>
  </body>
</html>
EOT;
  echo $template;
});

//POST route
$app->post('/post', function () {
  $subject = $app->request->post('s');
  if(is_null($subject)){
    $app->halt(400);
  }
  DoneList::create('subject' => $subject);
});

//PUT route
$app->put('/put', function () {
    echo 'This is a PUT route';
});

//DELETE route
$app->delete('/delete', function () {
    echo 'This is a DELETE route';
});

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This is responsible for executing
 * the Slim application using the settings and routes defined above.
 */
$app->run();
