<?php

  session_start();
  require_once("../pdo.php");
  require_once("leads/admin.php");

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BBS | Admin Center</title>
    <link rel="stylesheet" type="text/css" href="../style/admin/admin.css" />
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
    <script src="main.js"></script>
  </head>
  <body>
    <div class="menuTop">
      <?php
        if ($_SESSION['adminType'] == "counselor") {
          echo("<div style='background-color:blue'>STATUS: COUNSELOR</div>");
        } else {
          echo("<div style='background-color:green'>STATUS: DELEGATE</div>");
        };
      ?>
      <form method="POST">
        <input style="border:1px solid black" type="submit" name="logout" value="LOGOUT" />
      </form>
    </div>
    <div class="titleTop">BUCKEYE BOYS STATE</div>
    <div class="titleBottom">Administrative Center</div>
    <?php
      if (isset($_SESSION['message']) && $_SERVER['REQUEST_METHOD'] == "GET") {
        echo("<div class='message'>".$_SESSION['message']."</div>");
        unset($_SESSION['message']);
      };
    ?>
    <div class="sectionName">
      <?php
        // Adds these to the city or county titles for the page
        if ($secInfo['is_city'] == "1") {
          $titleSuffix = " City";
        } elseif ($secInfo['is_county'] == "1") {
          $titleSuffix = " County";
        } else {
          $titleSuffix = "";
        };
        // Displays which section you are working on
        echo($secInfo['section_name'].$titleSuffix);
      ?>
    </div>
    <div style="display:flex">
      <div class="belowTab"></div>
      <div class="belowTab"></div>
    </div>
    <div class="mainBox">
      <?php
        $listTypeStmt = $pdo->prepare("SELECT * FROM Type WHERE section_id=:sid");
        $listTypeStmt->execute(array(
          ':sid'=>$secInfo['section_id']
        ));
          while ($oneType = $listTypeStmt->fetch(PDO::FETCH_ASSOC)) {
            echo("
            <div class='postTypeRow'>
              <div class='postType'>".$oneType['type_name']."</div>
              <div id='addBttn".$oneType['type_id']."' class='addingPost' data-type='".$oneType['type_id']."'> + ADD POST</div>
            </div>
            <div style='display:none' id='addBox".$oneType['type_id']."' class='addBox postBox'>
              <form method='POST'>
                <div class='postTitle'>Title:</div>
                <input type='text' name='postTitle' placeholder='Enter your title here' />
                <div>Content:</div>
                <input type='text' name='postContent' placeholder='Enter your content here' />
                <div>Order #:</div>
                <input class='postOrder' type='number' name='orderNum' min='1' value='1' />
                <input type='hidden' name='approval' value='0' />
                <input type='hidden' name='typeId' value='".$oneType['type_id']."' />
                <input type='hidden' name='secId' value='".$_SESSION['secId']."' />
                <input class='addSubmit' type='submit' name='addPost' value='SUBMIT' />
              </form>
            </div>
            ");
            $listPostStmt = $pdo->prepare("SELECT DISTINCT * FROM Post WHERE type_id=:tid ORDER BY post_order ASC");
            $listPostStmt->execute(array(
              ':tid'=>$oneType['type_id']
            ));
            while ($onePost = $listPostStmt->fetch(PDO::FETCH_ASSOC)) {
              if ($onePost['approved'] == 1) {
                $approval = 1;
              } else {
                $approval = 0;
              };
              echo("
              <div class='postBox'>
                <form method='POST'>
                  <input type='hidden' name='postId' value='".$onePost['post_id']."'>
                  <div class='postTitle'>Title:</div>
                  <input type='text' name='postTitle' value='");
                    echo htmlspecialchars($onePost['title'], ENT_QUOTES);
                    echo("' />
                  <div>Content:</div>
                  <input type='text' name='postContent' value='");
                    echo htmlspecialchars($onePost['content'], ENT_QUOTES);
                    echo("' />
                  <div>Order #:</div>
                  <input class='postOrder' type='number' name='orderNum' min='1' value='".$onePost['post_order']."'/>
              ");
              if ($approval == 1) {
                $ifApproved = "checked";
                $ifPending = "";
                $status = "PUBLIC";
              } else {
                $ifApproved = "";
                $ifPending = "checked";
                $status = "PENDING";
              };
              echo("<div>Online Status: ".$status."</div>");
              if ($_SESSION['adminType'] == "counselor") {
                echo("
                  <div class='counsOnly'>
                    <div><u>COUNSELOR ONLY</u></div>
                    <input type='radio' id='yes' name='approval' value='1' ".$ifApproved." />
                    <label for='yes'>COMPLETE</label></br>
                    <input type='radio' id='no' name='approval' value='0' ".$ifPending." />
                    <label for='no'>PENDING</label></br>
                    <input type='submit' name='changeApproval' value='SUBMIT' />
                  </div>
                ");
              };
              echo("
                  <div class='changeBttns'>
                    <input type='submit' name='changePosts' value='CHANGE' />
                    <div id='delBttn".$onePost['post_id']."' data-post='".$onePost['post_id']."'>DELETE</div>
                  </div>
                  <div style='display:none' id='delBox".$onePost['post_id']."' class='delBox'>
                    ARE YOU SURE YOU WANT TO DELETE THIS POST?
                    <div class='delBttnRow'>
                      <div class='delBttn noDel' id='cancelDel".$onePost['post_id']."' data-post='".$onePost['post_id']."'>NO, keep it</div>
                      <input class='yesDel' type='submit' name='deletePost' value='YES, delete it' />
                    </div>
                  </div>
                </form>
              </div>");
            };
          };
        echo("</form>");

        // ** Below are COUNSELOR ONLY

        // For assigning/changing job assignments
        if ($_SESSION['adminType'] == 'counselor') {
          $jobListStmt = $pdo->prepare("SELECT Delegate.delegate_id,job_id,job_name,first_name,last_name,section_name FROM Job JOIN Delegate JOIN City WHERE Job.section_id=:scd AND Job.delegate_id=Delegate.delegate_id AND Delegate.city_id=City.city_id");
          $jobListStmt->execute(array(
            ':scd'=>htmlentities($secInfo['section_id'])
          ));
          echo("
            <div class='counsTitle'>
              COUNSELORS ONLY
            </div>
            <div id='listTitle' class='listTitle'>
              <span>
                Current Staff
              </span>
              <span>V</span>
            </div>
            <div id='listBox' class='listBox'>");
              while ($oneJob = $jobListStmt->fetch(PDO::FETCH_ASSOC)) {
                echo("
                  <div class='staffTitle'>".$oneJob['job_name']."</div>
                  <div class='staffContent'>
                    <div><span style='color:blue'>NAME:</span> ".$oneJob['first_name']." ".$oneJob['last_name']."</div>
                    <div><span style='color:blue'>CITY:</span> ".$oneJob['section_name']."</div>
                  </div>");
              };
        echo("
            </div>
            <div id='assignJobTitle' class='listTitle'>
              <span>Assign From Directory</span>
              <span>V</span>
            </div>
            <div id='assignJobBox' class='assignJobBox'>
              <form method='POST'>
                <span>I need to change the current... </span>
                <span>
                  <select name='jobId'>
                    <option value='-1'>Choose a job</option>");
                    $jobListStmt->execute(array(
                      ':scd'=>htmlentities($secInfo['section_id'])
                    ));
                    while ($singleJob = $jobListStmt->fetch(PDO::FETCH_ASSOC)) {
                      echo("<option value='".$singleJob['job_id']."'>".$singleJob['job_name']."</option>");
                    };
          echo("
                  </select>
                </span>
                <div>
                  Choose a delegate:
                </div>
                <div>");
                for ($delNum = 0; $delNum < count($allDelegate); $delNum++) {
                  echo("
                  <div>
                    <input type='radio' name='jobDel' value='".$allDelegate[$delNum]['delegate_id']."' />".$allDelegate[$delNum]['last_name'].", ".$allDelegate[$delNum]['first_name']."
                  </div>");
                };
          echo("
                  <div>
                    <input type='submit' name='changeJobDel' value='CHANGE' />
                  </div>
                </div>
              </form>
            </div>
          ");

          // For adding, changing, deleting a delegate from the database
          echo("
            <div id='updateDirTitle' class='listTitle'>
              <span>Update Directory</span>
              <span>V</span>
            </div>
            <div id='updateDirBox' class='updateDirBox'>
              <div id='addDirTitle' class='addDirTitle'>ADD DELEGATE</div>
              <div id='addDirBox' class='addDirBox'>
                <form method='POST'>
                  <div>
                    <input class='delInfoInput' type='text' name='newFirstN' placeholder='First name' />
                  </div>
                  <div>
                    <input class='delInfoInput' type='text' name='newLastN' placeholder='Last name' />
                  </div>
                  <div>
                    <input class='delInfoInput' type='text' name='newHome' placeholder='Hometown' />
                  </div>
                  <div>
                    <input class='delInfoInput' type='text' name='newEmail' placeholder='Email' />
                  </div>
                  <select class='selectBttn' name='delCity'>
                    <option value='-1'>Choose a city...</option>");
                    for ($cityNum = 0; $cityNum < count($allCity); $cityNum++) {
                      echo("<option value='".$allCity[$cityNum]['city_id']."'>".$allCity[$cityNum]['section_name']."</option>");
                    };
            echo("
                  </select>
                  <div>
                    <input class='addBttn' type='submit' name='addDelegate' value='ADD' />
                  </div>
                </form>
              </div>
              <table class='updateTable'>");
            for ($delNum = 0; $delNum < count($allDelegate); $delNum++) {
              echo("
                <form method='POST'>
                  <input type='hidden' name='delId' value='".$allDelegate[$delNum]['delegate_id']."'>
                  <tr class='updateRow'>
                    <td class='tableName'>".
                    $allDelegate[$delNum]['last_name'].", ".$allDelegate[$delNum]['first_name']."
                    </td>
                    <td data-delId='".$allDelegate[$delNum]['delegate_id']."' data-act='chgBttn' class='tableChange'>
                      CHANGE
                    </td>
                    <td data-delId='".$allDelegate[$delNum]['delegate_id']."' data-act='delBttn' class='tableDelete'>
                      DELETE
                    </td>
                  </tr>
                  <tr id='chgBox".$allDelegate[$delNum]['delegate_id']."' class='changeBox updateRow' data-delId='".$allDelegate[$delNum]['delegate_id']."' data-act='chgBox'>
                    <td colspan='3' style='border:1px solid black'>
                      <div>Change any info below and click 'SUBMIT'</div>
                      <div>
                        <span>First Name</span>
                        <span>
                          <input type='text' name='updateFstNm' value='".$allDelegate[$delNum]['first_name']."' />
                        </span></br>
                        <span>Last Name</span>
                        <span>
                          <input type='text' name='updateLstNm' value='".$allDelegate[$delNum]['last_name']."' />
                        </span></br>
                        <input type='submit' name='updateDelInfo' value='SUBMIT' />
                      </div>
                    </td>
                  </tr>
                  <tr id='delBox".$allDelegate[$delNum]['delegate_id']."' class='deleteBox udpateRow' data-delId='".$allDelegate[$delNum]['delegate_id']."' data-act='delBox'>
                    <td colspan='3' style='border:1px solid black'>
                      Delete Box
                    </td>
                  </tr>
                </form>
              ");
            };
          echo("
              </table>
            </div>
          ");
        };
      ?>
    </div>
  </body>
</html>
<!-- section_id,section_name,description,full_time,is_city,is_county -->
