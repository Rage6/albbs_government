<?php
  session_start();
  require_once("../../pdo.php");
  require_once("../../lockdown.php");
  require_once("supreme_court_lead.php");

  // Redirects to 'default.html' if lockdown in place
  if ($checkLock > 0) {
    header('Location: ../../default.html');
    return true;
  };

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BBS | State Supreme Court</title>
    <!-- Width: 0px to 360px (Default CSS) -->
    <link rel="stylesheet" type="text/css" href="style/court_360.css" />
    <link href="https://fonts.googleapis.com/css?family=Ibarra+Real+Nova:600&display=swap" rel="stylesheet">
  <script src=<?php
    if ($isLocal == true) {
      echo("../../".$jquery);
    } else {
      echo($jquery);
    };?>></script>
    <script src="main.js"></script>
    <script src="case_library/cases.js"></script>
  </head>
  <body>
    <div class="mainTitle">
      <div>
        <div class="topMain">BUCKEYE BOYS STATE</div>
        <div class="titleDivide"></div>
        <div class="bottomMain">
          <div>SUPREME</div>
          <div>COURT</div>
        </div>
      </div>
      <div class="courtLogo"></div>
    </div>
    <div class="menuContent">
      <div class="menuBttn" id="mainMenu">MENU</div>
      <div class="allOptions">
        <div id="justiceLink" class="optionBttn">JUSTICES</div>
        <div id="caseLink" class="optionBttn">COURT CASES</div>
        <div id="minutesLink" class="optionBttn">BAR ASSOCIATION MINUTES</div>
        <div id="resultsLink" class="optionBttn">BAR EXAM RESULTS</div>
        <a href="../../index.php">
          <div class="optionBttn returnBttn"><< BACK</div>
        </a>
      </div>
    </div>
    <div class="mainContent">
      <div class="welcome">
        <?php echo($intro); ?>
      </div>
      <!-- The Justice bios start here -->
      <a id="justiceTop">
        <div class="tagTitle">Supreme Court Justices</div>
        <div class="allJustices">
          <div>
            <?php
            while($oneJustice = $justiceInfoStmt->fetch(PDO::FETCH_ASSOC)) {
              echo("
              <div class='justiceBox'>
                <div class='justiceTitle'>
                  <div>".$oneJustice['first_name']." ".$oneJustice['last_name']."</div>
                  <div>".$oneJustice['job_name']."</div>
                </div>
                <img class='justiceImg' src='".$oneJustice['section_path']."crop_".$oneJustice['filename'].".".$oneJustice['extension']."' />
                <div class='justiceBio'>
                  <div>BBS City: ".$oneJustice['section_name']."</div>
                  <div>Hometown: ".$oneJustice['hometown'].", OH</div>
                </div>
              </div>");
            };
            ?>
          </div>
          <div>
            <!-- This will be a box that will fill the box of the selected Justice ONLY when the screen is wider. -->
          </div>
        </div>
      </a>

      <div class="upArrow">
        <div class="goTop">
          - TOP -
        </div>
      </div>

      <!-- The directory of cases -->
      <a id="caseTop">
        <div>
          <div>
            <!-- This is where you select the type of case -->
            <!-- Then, they can see all, dismissed, or pending cases -->
            <!-- The titles of the selected cases are shown here -->
            <div id="caseBttnList">
              <!-- The selected title will show its content -->
            </div>
          </div>
        </div>
      </a>

      <div class="upArrow">
        <div class="goTop">
          - TOP -
        </div>
      </div>

      <!-- The "Bar Association Minutes" start here -->
      <a id="minutesTop">
        <div class="tagTitle">Bar Association Minutes</div>
        <div class="minuteBox">
          <div class="allMinuteBttns">
            <div id="minute1" data-day="1">1</div>
            <div id="minute2" data-day="2">2</div>
            <div id="minute3" data-day="3">3</div>
            <div id="minute4" data-day="4">4</div>
            <div id="minute5" data-day="5">5</div>
          </div>
          <div class="minuteCnt">
            <?php
              $cntNum = 1;
              while ($oneMinute = $minuteStmt->fetch(PDO::FETCH_ASSOC)) {
                $month = substr($oneMinute['timestamp'],5,2);
                $day = substr($oneMinute['timestamp'],8,2);
                $year = substr($oneMinute['timestamp'],0,4);
                echo("
                <div id='minuteCnt".$cntNum."' class='allMinuteCnt'>
                  <div class='minuteDate'>".$month."/".$day."/".$year."</div>
                  <div class='minuteTitle'>".$oneMinute['title']."</div>
                  <div class='minuteMain'>".$oneMinute['content']."</div>
                </div>");
                $cntNum++;
              };
            ?>
          </div>
        </div>
      </a>

      <div class="upArrow">
        <div class="goTop">
          - TOP -
        </div>
      </div>

      <!-- The Bar Exam Results -->
      <a id="resultsTop">
        <div>For the Bar Exam Results</div>
      </a>

      <div class="upArrow">
        <div class="goTop">
          - TOP -
        </div>
      </div>

    </div>
  </body>
</html>
