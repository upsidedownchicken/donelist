<?php
class DoneList {
  public static function find_all(){
    self::use_db();
    $done = array();
    $q = mysql_query("select * from done_items order by created_at desc");

    while($row = mysql_fetch_assoc($q)){
      array_push($done, $row);
    }

    mysql_free_result($q);
    return $done;
  }

  private static function use_db(){
    mysql_connect("mysql-shared-02.phpfog.com", "Slim-29435", "x3LFk8EyGmhNib");
    mysql_select_db("donelist_phpfogapp_com");
  }
}
?>
