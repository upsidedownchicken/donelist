<?php

$app = getenv('DONELIST_URL');

if(empty($app)){
  $app = 'http://localhost/donelist/';
}

if(empty($argv[1])){
  $json = shell_exec("curl -s -H 'Content-Type: application/json' $app");
  $items = json_decode($json);
  foreach($items as $item){
    echo $item->created_at." ".$item->subject."\n";
  }
}
else {
  $subject = $argv[1];
  shell_exec(sprintf('curl -s -X POST -d s="%s" %s', $subject, $app));
}
?>
