<?php
const ext_test = "/(\\.[^.]+)$/";

function csvReader($filestream) {
    return fgetcsv($filestream);
}

function tsvReader($filestream) {
    return fgetcsv($filestream, separator: "\t");
}

function getReader($filename) {
    preg_match(ext_test, $filename, $matches);

    if (count($matches) == 0) {
        // If the filename has no extension, we'll just assume it's csv
        return "csvReader";
    }

    return match (strtolower($matches[0])) {
        ".csv" => "csvReader",
        ".tsv" => "tsvReader",
        default => throw new Exception("Unknown file extension: " . $matches[0])
    };
}
