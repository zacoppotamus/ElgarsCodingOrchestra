var datasetName;
var fileString;

function initialiseDialogue(){
    $("#uploadConfirm").dialog({
        resizable: false,
        modal: true,
        autoOpen: false,
        buttons: {
            "Append": function(){
                postData();
                $(this).dialog("close");
            },
            "Abort": function(){
                $(this).dialog("close");
            }
        }
    });
}

function startRead(evt) {
    var file = document.getElementById('fileInput').files[0];
    if(file){
        getAsText(file);
    }
}

function getAsText(readFile) {
    var reader = new FileReader();

    // Read file into memory as UTF-8
    reader.readAsText(readFile, "UTF-8");

    // Handle progress, success, and errors
    reader.onprogress = updateProgress;
    reader.onload = loaded;
    reader.onerror = errorHandler;
}

function updateProgress(evt) {
    if (evt.lengthComputable) {
        // evt.loaded and evt.total are ProgressEvent properties
        var loaded = (evt.loaded / evt.total);

        if (loaded < 1) {
            // Increase the prog bar length
            // style.width = (loaded * 200) + "px";
        }
    }
}

function loaded(evt) {
    var datasetName = document.getElementById("datasetNameInput").value;
    var fileString = String(evt.target.result);
    $.getJSON("http://api.spe.sneeza.me/datasets/" + datasetName + "/select", function(result){
        if(result.data.rows === 0){
            postData(datasetName, fileString);
        }
        else{
            $("#uploadConfirm").dialog("open");
        }
    });
}

function postData() {
    // Obtain the read file data
    var data = JSON.stringify($.csv.toObjects(fileString));
    $.post("http://api.spe.sneeza.me/datasets/" + datasetName + "/insert", {documents:data});
    alert("Done uploading!");
}

function errorHandler(evt) {
    if(evt.target.error.name == "NotReadableError") {
        // The file could not be read
    }
}
