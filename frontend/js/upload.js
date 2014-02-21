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

function uploadClick(evt) {
    var datasetName = document.getElementById("datasetNameInput").value;

    $.getJSON("http://api.spe.sneeza.me/datasets/" + datasetName + "/select", function(result){
        console.log("The result is " + result.data.rows);
        if(result.data.rows === 0){
            startRead();
        }
        else{
            $("#uploadConfirm").dialog("open");
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

    // Obtain the read file data
    var fileString = String(evt.target.result);
    var data = JSON.stringify($.csv.toObjects(fileString));
    $.post("http://api.spe.sneeza.me/datasets/" + datasetName + "/insert", {documents:data});
    alert("Done uploading!");
}

function postData() {
    // Obtain the read file data
    var datasetName = document.getElementById("datasetNameInput").value;
    var fileString = String(evt.target.result);
}

function errorHandler(evt) {
    if(evt.target.error.name == "NotReadableError") {
        // The file could not be read
    }
}
