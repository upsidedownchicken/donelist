<?php
require_once 'lib/db.php';

class DoneList {
  public static function create($opts){
    $done = new DoneList($opts);
    return $done->save();
  }

  public static function find_all(){
    DB::connect();

    $done = array();
    $q = mysql_query("select * from done_items order by created_at desc");

    if(!$q){
      throw new Exception(mysql_error());
    }

    while($row = mysql_fetch_assoc($q)){
      array_push($done, $row);
    }

    mysql_free_result($q);
    return $done;
  }

  public static function find_by_user_id($id){
    if(is_null($id)){
      throw new Exception('NULL value given to DoneList::find_by_user_id');
    }

    $done = array();
    $q = 'select * from done_items where user_id = %s order by created_at desc';
    $rs = mysql_query(sprintf($q, $id));

    if(!$rs){
      throw new Exception(mysql_error());
    }

    while($row = mysql_fetch_assoc($rs)){
      array_push($done, $row);
    }

    mysql_free_result($rs);

    return $done;
  }

  # yes, i mean for this to be public
  public $subject;

  function __construct($opts){
    if(isset($opts['subject'])){
      $this->subject = $opts['subject'];
    }
  }

  public function save(){
    DB::connect();

    $sql = sprintf("insert into done_items (created_at, subject) values(now(), '%s')",
      mysql_real_escape_string($this->subject));
    if(!mysql_query($sql)){
      throw new Exception(mysql_error());
    }
    return $this;
  }

}
?>
