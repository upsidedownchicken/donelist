<?php

class Config {
  public $session_secret;

  function __construct(){
    $this->session_secret = $this->load_var('session_secret');
  }

  private function load_var($name){
    if($value = getenv($name)){
      $this->{$name} = $value;
    }
    else {
      throw new Exception("No environment setting for $name");
    }
  }
}
