<?php

class DB {
  public static function connect(){
    if(!mysql_connect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'))){
      throw new Exception(mysql_error());
    }

    if(!mysql_select_db(getenv('DB_NAME'))){
      throw new Exception(mysql_error());
    }
  }
}
