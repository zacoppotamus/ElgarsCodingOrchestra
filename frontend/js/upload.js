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

// Retrieves the username of the mashape user
var mashape_user;
function username(){
    if(typeof mashape_user === 'undefined') {
        getRequest("https://sneeza-eco.p.mashape.com/ping", function(result){
            window.mashape_user = result.data.mashape_user;
        });
    }
    alert("mashape user is " + window.mashape_user);
    return window.mashape_user;
}

// Is called when the upload button is clicked
function uploadClick(evt) {
    var datasetName = document.getElementById("datasetNameInput").value;

    // Checks that the dataset doesn't already exist
    getRequest("https://sneeza-eco.p.mashape.com/datasets/"+username() + "." + datasetName, function (result){
        alert(JSON.stringify(result));
        if(result.data.message === "The dataset you specified does not exist."){
            requestData = {
                "name": datasetName,
                "description": "placeholder description"
            };
            postRequest("https://sneeza-eco.p.mashape.com/datasets", requestData, function (result){
                alert("Post result: " + JSON.stringify(result));
            });
            startRead();
        }
        else{
            $("#uploadConfirm").dialog("open");
        }
    })
}

function getRequest(requestURL, fncSuccess) {
    $.ajax({
        url: requestURL,
        type: "GET",
        beforeSend: function (request){
            request.setRequestHeader("X-Mashape-Authorization", "EU6h9H8BUXELDmfO1Mbh0jLasSQxrAZd")
        },
        dataType: 'json',
        success: fncSuccess
    });
}

function postRequest(requestURL, requestData, fncSuccess) {
    $.ajax({
        url: requestURL,
        type: "POST",
        beforeSend: function (request){
            request.setRequestHeader("X-Mashape-Authorization", "EU6h9H8BUXELDmfO1Mbh0jLasSQxrAZd")
        },
        data: requestData,
        dataType: 'json',
        success: fncSuccess
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
    $.post("http://api.spe.sneeza.me/datasets/" + username() + "." + datasetName, {documents:data});
}

function errorHandler(evt) {
    if(evt.target.error.name == "NotReadableError") {
        // The file could not be read
        alert("Error: the selected file could not be read");
    }
}
