<?php
include "./config.php";
$succ = -1;
if($_GET['showsrc']) {
    show_source("step1.php");
    die;
}
if($_GET['searchkey']) {
    $succ = 0;
    $query = "select * from books where title like '%".$_GET['searchkey']."%'";
    $db = dbconnect("sqli102_step3");
    $result = mysqli_query($db,$query);
    mysqli_close($db);
    if($result) {
        $rows = mysqli_num_rows($result);
    }
}

?>

<!-- source: https://colorlib.com/wp/template/colorlib-search-23/ -->
<html>
    <head>
        <title>SQLi 102: Step 1</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="author" content="colorlib.com">
        <style id="" media="all">/* devanagari */
        @font-face {
          font-family: 'Poppins';
          font-style: normal;
          font-weight: 400;
          src: url(/fonts.gstatic.com/s/poppins/v15/pxiEyp8kv8JHgFVrJJbecmNE.woff2) format('woff2');
          unicode-range: U+0900-097F, U+1CD0-1CF6, U+1CF8-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FB;
        }
        </style>
        <link href="style.css" rel="stylesheet" />
    </head>
    <body>
        <div class="s130">
            <form>
                <table border="0">
                    <tr>
                        <td style="font-size: 28px; text-align: left"><strong>A book search service</strong></td>
                        <td style="text-align: right; vertical-align: bottom"><a href="?showsrc=True">[HINT]</a></td>
                    </tr>
                </table>
                <div class="inner-form">
                    <div class="input-field first-wrap">
                        <div class="svg-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                            </svg>
                        </div>
                        <input id="search" type="text" name="searchkey" placeholder="Enter keyword here" />
                    </div>
                    <div class="input-field second-wrap">
                        <button class="btn-search" type="submit">SEARCH</button>
                    </div>
                </div>

<?
    if ($rows > 0) {
?>
                <br>
                <h2>Search Result</h2>
                <table>
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="50%">Title</th>
                            <th width="30%">Author</th>
                            <th width="10%">Price</th>
                        </tr>
                    </thead>
                    <tbody>

<?
    for($idx = 1; $idx <= $rows; $idx++) {
        $row = mysqli_fetch_assoc($result);
        echo "<tr><th scope=\"row\">".$idx."</th>";
        echo "<td>".$row["title"]."</td>";
        echo "<td>".$row["author"]."</td>";
        echo "<td>".$row["price"]."</td></tr>";
    }
?>
                    </tbody>
                </table>
<?
    } else if ($succ === 0) {
?>
                <br>
                <h2>Sorry, no results for your request.</h2>
<?
    } else {
?>
                <br>
                <h2 style="padding-left:30px">Quiz#1: How many columns are in the `<strong>count_me</strong>` table?</h2>
<?
    }
?>

            </form>
        </div>
    </body>
</html>