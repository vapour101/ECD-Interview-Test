<?php
require_once "./src/constants.php";
require_once "./src/fileReader.php";

function getOutputArray($inputHeadings, $inputArray) {
    $res = [];

    $in = array_combine($inputHeadings, $inputArray);

    foreach (output_headings_order as $heading) {
        $res[$heading] = strtolower($in[heading_mapping[$heading]]);
    }

    $res["count"] = 1;

    return $res;
}

function checkRequiredFields($inputHeadings, $inputArray) {
    $in = array_combine($inputHeadings, $inputArray);

    foreach (required_fields as $heading) {
        if ($in[heading_mapping[$heading]] === "") {
            throw new Exception("Missing required field: $heading");
        }
    }
}

$args = getopt("", ["file:", "unique-combinations:"]);

if (!file_exists($args["file"])) {
    die("The file {$args["file"]} does not exist.");
}

$reader = getReader($args["file"]);
$input = fopen($args["file"], "r");

if ($input === false) {
    die("{$args["file"]} could not be opened.");
}

$outputs = [];
$headings = $reader($input);

while (($row = $reader($input)) !== false) {
    $key = strtolower(implode("," , $row));

    if (key_exists($key, $outputs)) {
        $outputs[$key]["count"] += 1;
    } else {
        checkRequiredFields($headings, $row);
        $outputs[$key] = getOutputArray($headings, $row);
    }
}

fclose($input);

$output = fopen($args["unique-combinations"], 'c');

if ($output === false) {
    die("{$args["unique-combinations"]} could not be opened.");
}

foreach ($outputs as $row) {
    fputcsv($output, $row);
}

fclose($output);
