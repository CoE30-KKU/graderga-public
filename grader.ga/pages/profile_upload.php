<?php

//upload.php

function generateRandom($length = 16) {
	$characters = md5(time());
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

if(isset($_POST["image"]))
{
	$data = $_POST["image"];
	$image_array_1 = explode(";", $data);
	$image_array_2 = explode(",", $image_array_1[1]);
	$data = base64_decode($image_array_2[1]);
	if (!file_exists('../file/profile/'. $_POST['userID'] .'/')) {
		mkdir('../file/profile/'. $_POST['userID'] .'/');
	}
	$imageName = "../file/profile/" . $_POST['userID'] . "/Avatar_" . generateRandom(32) . '.png';
	file_put_contents($imageName, $data);
	echo $imageName;
}

?>