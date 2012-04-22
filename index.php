<?php

/**
 * Step 1: Require the Slim PHP 5 Framework
 *
 * If using the default file layout, the `Slim/` directory
 * will already be on your include path. If you move the `Slim/`
 * directory elsewhere, ensure that it is added to your include path
 * or update this file path as needed.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require('vendor/openid.php');
require 'Slim/Slim.php';
require 'lib/done_list.php';
require 'lib/user.php';

/**
 * Step 2: Instantiate the Slim application
 *
 * Here we instantiate the Slim application with its default settings.
 * However, we could also pass a key-value array of settings.
 * Refer to the online documentation for available settings.
 */
$app = new Slim();

$app->add(new Slim_Middleware_SessionCookie( array(
  'secret' => 'FDq8PMCb2GUzuHNBEsGpFRTFgEcyHKUs',
)));

function current_user() {
  $data = get_login_data();

  if(is_null($data)){
    return NULL;
  }

  return User::find_by_email($data['contact/email']);
}

function get_login_data() {
  if(array_key_exists('u', $_SESSION)) {
    return $_SESSION['u'];
  }
}

function forget_login() {
  if(array_key_exists('u', $_SESSION)) {
    unset($_SESSION['u']);
  }
}

function save_login($data) {
  $_SESSION['u'] = $data;
}

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

$app->get('/login', function () use ($app) {
  try {
    # Change 'localhost' to your domain name.
    $openid = new LightOpenID($_SERVER['SERVER_NAME']);
    if(!$openid->mode) {
      if(isset($_GET['login'])) {
          $openid->identity = 'https://www.google.com/accounts/o8/id';
          $openid->required = array('contact/email');
          $app->response()->header('Location', $openid->authUrl());
      }
    } elseif($openid->mode == 'cancel') {
      $app->flash('error', 'Login cancelled.');
      $app->redirect('/');
    } else {
      #TODO create records for new users
      save_login($openid->getAttributes());
      $user = get_login_data();
      $app->flash('success', 'You are logged in as '.$user['contact/email']);
      $app->redirect('/');
    }
  } catch(ErrorException $e) {
    $app->flash('error', $e->getMessage());
    $app->redirect('/');
  }
});

$app->get('/logout', function() use ($app) {
  forget_login();
  $app->flash('success', 'You are no longer logged in.');
  $app->redirect('/');
});

//GET route
$app->get('/', function () use ($app) {
  $user = current_user();
  $done = array();

  if($user){
    $done = DoneList::find_by_user_id($user->id);
  }

  if('application/json' == $app->request()->getContentType()){
    $response = $app->response();
    $response->header('Content-Type', 'application/json');
    echo(json_encode($done));
  }
  else {
    $app->render('index.php', array(
      'done'      => $done,
      'user'      => $user,
    ));
  }
});

//POST route
$app->post('/', function () use ($app) {
  $user = current_user();

  if($user){
    $subject = $app->request()->post('s');

    if(is_null($subject)){
      $app->halt(400);
    }

    DoneList::create(array('subject' => $subject));

    $app->flash('success', 'Added donelist item');
    $app->redirect('/');
  }
  else {
    #TODO send WWW-Authenticate header
    #see http://php.net/manual/en/features.http-auth.php
    $app->halt(401);
  }
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
