// Initialises the dialogue that appears
// in the event of a dataset name being
// entered that is already in use
function initialiseDialogue(){
    $("#uploadConfirm").dialog({
        resizable: false,
        modal: true,
        autoOpen: false,
        buttons: {
            "Append": function(){
                startRead();
                $(this).dialog("close");
            },
            "Abort": function(){
                $(this).dialog("close");
            }
        }
    });
}

// Is called when the upload button is clicked
function uploadClick(evt) {
    var datasetName = document.getElementById("datasetNameInput").value;

    // Checks that the dataset doesn't already exist
    $.getJSON("http://api.spe.sneeza.me/datasets/" + datasetName + "/select", function(result){
        if(result.data.rows === 0){
            startRead();
        }
        else{
            $("#uploadConfirm").dialog("open");
        }
    });
}

// Commences reading the selected file
function startRead(evt) {
    var file = document.getElementById('fileInput').files[0];
    if(file){
        getAsText(file);
    }
}

// Reads the passed file
// Calls the loaded function upon completion
function getAsText(readFile) {
    var reader = new FileReader();

    // Read file into memory as UTF-8
    reader.readAsText(readFile, "UTF-8");

    // Handle progress, success, and errors
    //reader.onprogress = updateProgress;
    reader.onerror = errorHandler;
    reader.onload = loaded;
}


// Called once the file has been successfully read
function loaded(evt) {
    var datasetName = document.getElementById("datasetNameInput").value;

    // Parse the csv to a JSON string
    var fileString = String(evt.target.result);
    var data = JSON.stringify($.csv.toObjects(fileString));

    // Post the data to the database
    $.post("http://api.spe.sneeza.me/datasets/" + datasetName + "/insert", {documents:data});
}

function errorHandler(evt) {
    if(evt.target.error.name == "NotReadableError") {
        // The file could not be read
        alert("Error: the selected file could not be read");
    }
}
