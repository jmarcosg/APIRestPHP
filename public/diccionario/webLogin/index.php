<?php

$people_json = file_get_contents('webLogin.json');

$decoded_json = json_decode($people_json, false);
echo json_encode($decoded_json);
exit;
