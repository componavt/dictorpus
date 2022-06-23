<?php 
/*$dh=fopen('wavs/test','w');
if (isset($_FILES)) {
    fwrite($dh,json_encode($_FILES['audio'])."\n");
} else {
    fwrite($dh,"empty\n");
} 

fwrite($dh,json_encode($_POST)."\n");*/
if ($_FILES['audio']['name']) {
    $filename="{$_POST['id']}_1.wav";
    if (is_uploaded_file($_FILES['audio']['tmp_name'])) {
	    move_uploaded_file($_FILES['audio']['tmp_name'], "wavs/".$filename);
    }
//    $file = base64_decode($_FILES['audio']);
//    file_put_contents("wavs/".$filename, $file);
}
?>