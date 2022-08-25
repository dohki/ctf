<?php

if($_GET['showsrc']) {
    show_source("help.php");
    die;
}

if ($_POST['email'] and $_POST['desc']){
    include "./config.php";

    $db = dbconnect();
    insert_data_with_prepared_statements($db, $_POST['email'], $_POST['desc']);
    mysqli_close($db);

    $sent = true;
}

?>

<html>
    <head>
        <title>XSS 101</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <style>
            body {
                margin:4rem;
                background: #cbdce4;
            }

            table {
                width:100%; 
            }

            .form-rounded {
            border-collapse:separate;
            border:solid black 1px;
            border-radius: 1rem;
            }
        </style>
    </head>

    <body>
        <? if (isset($sent)) { ?>
            <div class="container">
                <h4>Your case has been successfully received.<br>We'll check that out soon.</h4><br>
                <button type="button" class="btn btn-primary" onclick="window.close();">Close</button>
            </div>
        <? } else { ?>
        <form class="container" action = "/help.php" method="POST">
            <table border="0">
                <tr>
                    <td style="font-size: 28px; text-align: left"><h1>Create a support case</h1></td>
                    <td style="text-align: right; vertical-align: bottom"><a href="?showsrc=True">[HINT]</a></td>
                </tr>
            </table>
            <h6>Admin will check your message soon.</h6><br>

            <div class="form-group">
                <label for="email">Email:</label>
                <input name="email" type="email" class="form-control" placeholder="Enter email address" id="email">
            </div>
            <div class="form-group">
                <label for="desc">Description:</label>
                <textarea name="desc" type="text" class="form-control" placeholder="Enter description" id="desc" rows="10"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary float-right ml-1">Submit</button>
                <button type="button" class="btn btn-primary float-right ml-1" onclick="window.close();">Cancel</button>
            </div>
        </form>
        <? } ?>
    </body>
</html>