 <?php
    #require_once("./reply.php");
    #require_once("./connection.php");
    ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Mobitel | Auto Reply</title>
     <link rel="shortcut icon" href="./resources/img/SLT-2.jpg" type="image/x-icon">
     <link rel="stylesheet" href="./resources/css/bootstrap.css">
     <link rel="stylesheet" href="./resources/css/style.css">
 </head>

 <body>

     <?php

        if (isset($_GET["recp"])) {

            $recp = $_GET["recp"];
            $msg = AutoReply::$msg;

            if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
                $origin = $_SERVER['HTTP_ORIGIN'];
            } else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
                $origin = $_SERVER['HTTP_REFERER'];
            } else {
                $origin = $_SERVER['REMOTE_ADDR'];
            }

            $server = $origin;

            $date = new DateTime();
            $zone = new DateTimeZone("Asia/Colombo");
            $date->setTimezone($zone);
            $date = $date->format("Y-m-d H:i:s");
            $expiration = date("Y-m-d", strtotime('+1 year'));
            $msg = AutoReply::$msg . $expiration . "\n\nThank you for using our Service!";

            $query = "INSERT INTO `auto_reply` (`recp`,`msg`,`time`,`server`,`expiration`) VALUES ('$recp','$msg','$date','$server','$expiration')";

            try {
                Database::iud($query);
                // echo 1;
            } catch (\Throwable $th) {
                echo 0;
            }

            $recp = $_GET["recp"];
            AutoReply::reply($recp, $expiration);
            header("Location: autoreply.php");
        } else {

            $page_number = 1;
            $offset = 0;
            $results_per_page = 6;
            if (isset($_GET["page"])) {
                $page_number = $_GET["page"];
                $offset = ($page_number - 1) * $results_per_page;
            }

            $query1 = "SELECT * FROM `auto_reply`";

            #$res = Database::search($query1);
            $rows1 = $res->num_rows;
            $number_of_pages = ceil($rows1 / $results_per_page);

            $query2 = "SELECT * FROM `auto_reply` ORDER BY `id` DESC LIMIT $results_per_page OFFSET $offset";
            $res = Database::search($query2);
            $rows = $res->num_rows;
        ?>

         <div class="container-fluid">

             <!-- header -->

             <div class="row header p-3">
                 <div class="col">

                     <img src="./resources/img/SLT-2.jpg" alt="logo" style="max-width: 160px;">
                 </div>
                 <div class="col align-self-center text-light">
                     <h1 class="m-0">
                         Mobitel AutoReply Service
                     </h1>
                 </div>
                 <div class="col">
                 </div>
             </div>
             <div class="row justify-content-center mt-3">
                 <div class="col-10">

                     <!-- Auto Replied list -->

                     <table class="table table-hover text-center">
                         <thead>
                             <tr>
                                 <th scope="col">#</th>
                                 <th scope="col">Recepient</th>
                                 <th scope="col">Message</th>
                                 <th scope="col">Time</th>
                                 <th scope="col">Expiration</th>
                             </tr>
                         </thead>
                         <tbody>
                             <?php
                                for ($i = 1; $i <=  $rows; $i++) {
                                    $msg = $res->fetch_assoc();
                                ?>
                                 <tr>
                                     <th scope="row"><?php echo ($page_number - 1) * $results_per_page + $i; ?></th>
                                     <td><?php echo $msg['recp']; ?></td>
                                     <td><?php echo $msg['msg']; ?></td>
                                     <td><?php echo $msg['time']; ?></td>
                                     <td><?php echo $msg['expiration']; ?></td>
                                 </tr>
                             <?php
                                }
                                ?>
                         </tbody>
                     </table>

                     <!-- pagination -->

                     <div class="row <?php if ($number_of_pages <= 1) {
                                            echo "d-none";
                                        } ?> ">
                         <div class="col">
                             <nav aria-label="Page navigation example" class="d-flex">
                                 <ul class="pagination mx-auto my-4">
                                     <li class="page-item">
                                         <a class="page-link" href="?page=1" aria-label="Previous">
                                             <span aria-hidden="true">&laquo;</span>
                                         </a>
                                     </li>
                                     <?php
                                        for ($j = 0; $j < $number_of_pages; $j++) {

                                        ?>
                                         <li class="page-item <?php if ($page_number == $j + 1) {
                                                                    echo "active";
                                                                } ?> "><a class="page-link" href="?page=<?php echo $j + 1; ?>"><?php echo $j + 1; ?></a></li>
                                     <?php
                                        }
                                        ?>
                                     <li class="page-item">
                                         <a class="page-link" href="?page=<?php echo $number_of_pages; ?>" aria-label="Next">
                                             <span aria-hidden="true">&raquo;</span>
                                         </a>
                                     </li>
                                 </ul>
                             </nav>
                         </div>
                     </div>
                 </div>

             </div>
         </div>


     <?php
        }
        ?>

     <script src="./resources/js/bootstrap.bundle.js"></script>
 </body>

 </html>