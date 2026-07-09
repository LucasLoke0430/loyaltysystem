<?php
$html = file_get_contents('index.php');
preg_match_all('/document\.getElementById\(\'([a-zA-Z0-9_-]+)\'\)/', $html, $matches);
$ids = array_unique($matches[1]);
foreach($ids as $id) {
    if(strpos($html, 'id="'.$id.'"') === false && strpos($html, "id='".$id."'") === false) {
        echo "MISSING: $id\n";
    }
}
?>
