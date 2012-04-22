<?php
require_once 'lib/db.php';

class User {

  public $id = null;
  public $email = null;
  public $created_at = null;
  public $updated_at = null;

  public static function create($opts){
    $user = new User($opts);
    return $user->save();
  }

  public static function find_by_email($email){
    if(is_null($email)){
      throw new Exception('NULL value given to User::find_by_email()');
    }

    DB::connect();

    $q = "select id, email, created_at, updated_at from users where email = '%s'";
    $rs = mysql_query(sprintf($q, $email));

    if(!$rs){
      throw new Exception(mysql_error());
    }

    $row = mysql_fetch_assoc($rs);

    if(isset($row['id'])){
      return new User($row);
    }
    else {
      throw new Exception("No user found for $email");
    }

    mysql_free_result($rs);

    return $user;
  }

  function __construct($opts){
    $keys = array('id', 'email', 'created_at', 'updated_at');

    foreach( $keys as $key ){
      if(isset($opts[$key])){
        $this->{$key} = $opts[$key];
      }
    }
  }
}
?>
