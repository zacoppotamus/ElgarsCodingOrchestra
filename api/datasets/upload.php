<?php

include("includes/kernel.php");
include("includes/api/core.php");

/*!
 * Check if the user provided enough information to upload data to
 * the dataset, which requires the dataset name and prefix.
 */

if(empty($data->prefix) || empty($data->name)) {
    echo json_beautify(json_render_error(401, "You didn't pass one or more of the required parameters."));
    exit;
}

/*!
 * Check to see if the dataset exists, and that we have access to it.
 * We need to use the prefix and the name of the dataset to get a
 * reference to it.
 */

// Create a new dataset object.
$dataset = new rainhawk\dataset($data->prefix, $data->name);

// Check that the dataset exists.
if(!$dataset->exists) {
    echo json_beautify(json_render_error(402, "The dataset you specified does not exist."));
    exit;
}

// Check that we can read from the dataset.
if(!$dataset->have_write_access(app::$username)) {
    echo json_beautify(json_render_error(403, "You don't have access to write to this dataset."));
    exit;
}

/*!
 * Fetch the input file, 8kB at a time, saving it to a temporary file so that
 * we can run it through the parser.
 */

// Check that the type has been set.
if(empty($data->type)) {
	echo json_beautify(json_render_error(404, "You specified a type that we don't support. Currently we support: csv, xlsx, ods."));
    exit;
}

// Set a temporary location for the file.
$file = "/tmp/upload_" . sprintf("%07d", rand(1, 1000000)) . "." . $data->type;
$fp = fopen($file, "w");

// Check if the user sent a file or not.
if(!$input = fopen("php://input", "r")) {
    echo json_beautify(json_render_error(405, "You didn't send any file contents to process."));
    exit;
}

// Read the data and write it to the file, 8kB at a time.
while($data = fread($input, 1024 * 8)) {
    fwrite($fp, $data);
}

// Close the file pointers.
fclose($fp);
fclose($input);

// Check if the file is empty or not.
if(filesize($file) == 0) {
	unlink($file);

	echo json_beautify(json_render_error(406, "The contents of the file you uploaded is empty."));
    exit;
}

/*!
 * Once we've uploaded the file, send it to the parser for processing.
 */

// Run the parser command.
exec("cd ../parser/ && ./sadparser '" . $file . "' 2>&1", $result);

// Check for any errors.
if(!empty($result)) {
	foreach($result as $line) {
		if(stripos($line, "invalid") !== false) {
			echo json_beautify(json_render_error(407, "There was a problem while processing your data - your data could not be read. Currently we only support: csv, xlsx."));
		    exit;
		} else if(stripos($line, "could not") !== false) {
			echo json_beautify(json_render_error(408, "There was a problem while processing your data - we seem to be having technical difficulties with our parser. Please try again later."));
		    exit;
		}
	}
}

// Remove the temporary file.
unlink($file);

// Find the generated JSON files.
$files = scandir("/tmp");
$file = pathinfo($file, PATHINFO_FILENAME);

// Iterate through the /tmp directory to find them.
foreach($files as $tmp_file) {
	if(stripos($tmp_file, $file) !== false && pathinfo($tmp_file, PATHINFO_EXTENSION) == "json") {
		$tmp_file = "/tmp/" . $tmp_file;
		$rows = json_decode(file_get_contents($tmp_file), true);

		// Insert the rows of data into the dataset.
		$rows = $dataset->insert_multi($rows);

		// Check that it was a success.
		if(!$rows) {
		    echo json_beautify(json_render_error(409, "An unknown error occured while inserting your data into the dataset."));
		    exit;
		}

		// Set the JSON output.
		foreach($rows as $row) {
		    if(isset($row['_id'])) {
		        $row['_id'] = (string)$row['_id'];
		    }

		    foreach($row as $field => $value) {
		        if(isset($dataset->constraints[$field])) {
		            $row[$field] = \rainhawk\data::check($dataset->constraints[$field]['type'], $value);
		        }
		    }

		    $json['rows'][] = $row;
		}

		// Delete JSON.
		unlink($tmp_file);
	}
}

// Check if the number of rows inserted was zero.
if(!isset($json['rows']) || empty($json['rows'])) {
	echo json_beautify(json_render_error(410, "After parsing the uploaded file we couldn't find any data."));
    exit;
}

/*!
 * Output our JSON payload for use in whatever needs to be using
 * it.
 */

echo json_beautify(json_render(200, $json));
exit;

?>