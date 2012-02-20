<?php
class DoneList {
  public static function create($opts){
    $done = new DoneList($opts);
    return $done->save();
  }

  public static function find_all(){
    self::use_db();
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

  # yes, i mean for this to be public
  public $subject;

  function __construct($opts){
    if(isset($opts['subject'])){
      $this->subject = $opts['subject'];
    }
  }

  public function save(){
    self::use_db();
    $sql = sprintf("insert into done_items (created_at, subject) values(now(), '%s')",
      mysql_real_escape_string($this->subject));
    if(!mysql_query($sql)){
      throw new Exception(mysql_error());
    }
    return $this;
  }

  private static function use_db(){
    if(!mysql_connect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'))){
      throw new Exception(mysql_error());
    }

    if(!mysql_select_db(getenv('DB_NAME'))){
      throw new Exception(mysql_error());
    }
  }
}
?>
