<?php

$data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

$json['status'] = 1;
$json['dados'] = [$data, $_FILES];

echo json_encode($json);
