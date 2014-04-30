<?php

require_once "includes/core.php";
require_once "includes/check_login.php";

$dataset = isset($_GET['dataset']) ? htmlspecialchars($_GET['dataset']) : null;
$datasetInfo = $rainhawk->fetchDataset($dataset);
$fields = $datasetInfo['fields'];
$constraints = $datasetInfo['constraints'];
$readList = $datasetInfo['read_access'];
$writeList = $datasetInfo['write_access'];
$accessList = array_unique(array_merge($readList, $writeList));

?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>Project Rainhawk - Properties</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once "includes/meta.php"; ?>
        <script src="js/rainhawk.js" type="text/javascript"></script>
        <script>
            var dataset = "<?php echo $dataset; ?>";
            var numUserCount = 0;

            rainhawk.apiKey = "<?php echo $mashape_key; ?>";

            $(function() {
                $(".confirm").confirm({
                    text: "Are you sure you wish to revoke this user's permission?",
                    title: "Really revoke?",
                    confirmButton: "Revoke",
                    confirm: function(btn) {
                        var username = $(btn).data("user");

                        rainhawk.access.remove(dataset, username, null, function() {
                            $("[data-row-user='" + username + "']").remove();
                        }, function(msg) {
                            $("#accessBody").prepend("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Error revoking.</strong> "+msg+"</div>");
                        });
                    }
                });

                $(document).on("change", ".writecheck", function(e) {
                    var $write = $(this);
                    var $read = $write.parents("tr").find(".readcheck");

                    if($write.is(":checked")) {
                        $read.prop("checked", true);
                    }
                });
            });

            function addUser() {
                window.newUserCount++;

                $("#tblPermissions").append(
                  "<tr data-row-user-num=" + window.newUserCount + ">" +
                    "<td><input type='text' name='newUser[" + window.newUserCount + "][user]' class='form-control'></td>" +
                    "<td class='text-center'><input type='checkbox' class='readcheck'  name='newUser[" + window.newUserCount + "][read]' value='read' checked></td>" +
                    "<td class='text-center'><input type='checkbox' class='writecheck'  name='newUser[" + window.newUserCount + "][write]' value='write'></td>" +
                    "<td><button type='button' data-user-num=" + window.newUserCount + " onclick='cancelNewUser(this);' class='btn btn-warning btn-sm'>Cancel</button></td>" +
                  "</tr>");
            }

            function cancelNewUser(btn) {
                $("[data-row-user-num=" + $(btn).data("user-num") + "]").remove();
            }
        </script>
    </head>

    <body>
        <?php require_once "includes/nav.php"; ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h1><?php echo $dataset; ?></h1>
                            <p>Update the structure of your dataset...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Fields</h4>
                        </div>
                        <div class="panel-body">
                            <form id="fieldForm" action="/proxy/dataset_constraints.php?dataset=<?php echo $dataset; ?>" method="post">
                                <table class='table'>
                                    <thead>
                                        <tr>
                                            <th class="col-md-7">Name</th>
                                            <th class="col-md-5">Constraint</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($fields as $field) { ?>
                                            <?php if($field == "_id") continue; ?>
                                            <?php $type = count($constraints) > 0 && in_array($field, array_keys($constraints)) ? $constraints[$field]['type'] : "none"; ?>
                                            <?php if(in_array($user, $writeList)) { ?>
                                                <tr>
                                                    <td><?php echo $field; ?></td>
                                                    <td>
                                                        <select id="<?php echo $field; ?>" name="constraint[<?php echo $field; ?>]" class="form-control">
                                                            <?php foreach(array("none", "integer", "string", "latitude", "longitude", "float", "timestamp") as $possible) { ?>
                                                                <option value="<?php echo $possible; ?>" <?php echo $type == $possible ? "selected" : null; ?>><?php echo ucwords($possible); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php } else { ?>
                                                <tr>
                                                    <td><?php echo $field; ?></td>
                                                    <td><?php echo $type; ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <?php if(in_array($user, $writeList)) { ?>
                            <div class="panel-footer text-right">
                                <button type="submit" form="fieldForm" class="btn btn-success" formaction="/proxy/dataset_constraints.php?dataset=<?php echo $dataset; ?>&autoapply" formnovalidate>Auto Apply</button>
                                <button type="submit" form="fieldForm" class="btn btn-default">Apply</button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Permissions</h4>
                        </div>
                        <div id="accessBody" class="panel-body">
                            <form id="accessForm" action="/proxy/dataset_permissions.php?dataset=<?php echo $dataset; ?>" method="post">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="col-md-9">User</th>
                                            <th class="col-md-1 text-center">Read</th>
                                            <th class="col-md-1 text-center">Write</th>
                                            <th class="col-md-1 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tblPermissions">
                                        <?php foreach($accessList as $key => $username) { ?>
                                            <?php $isWrite = in_array($username, $writeList); ?>
                                            <?php $isRead = in_array($username, $readList); ?>
                                            <?php if(in_array($user, $writeList)) { ?>
                                                <tr data-row-user="<?php echo $username; ?>">
                                                    <td><?php echo $username; ?></td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="readcheck" name="currentUser[<?php echo $username; ?>][read]" value="read" <?php echo $isRead ? "checked" : null; ?>>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="writecheck" name="currentUser[<?php echo $username; ?>][write]" value="write" <?php echo $isWrite ? "checked" : null; ?>>
                                                    </td>
                                                    <td>
                                                        <button type="button" data-user="<?php echo $username; ?>" class="btn btn-sm btn-danger confirm">Revoke</a>
                                                    </td>
                                                </tr>
                                            <?php } else { ?>
                                                <tr data-row-user="<?php echo $username; ?>">
                                                    <td><?php echo $username; ?></td>
                                                    <td class="text-center">
                                                        <input type="checkbox" value="read" disabled <?php echo $isRead ? "checked" : null; ?>>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" value="write" disabled <?php echo $isWrite ? "checked" : null; ?>>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="text-center" colspan="100%">
                                                <?php if(in_array($user, $writeList)) { ?>
                                                    <button type="button" class="btn btn-sm btn-success" onclick="addUser()">Add User</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                        <?php if(in_array($user, $writeList)) { ?>
                            <div class="panel-footer text-right">
                                <button type="submit" form="accessForm" class="btn btn-default">Apply</button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>