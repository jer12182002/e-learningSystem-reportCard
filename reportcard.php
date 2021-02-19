<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php

require_once('../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once $CFG->dirroot . '/grade/report/overview/lib.php';
require_once $CFG->dirroot . '/grade/lib.php';

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('base');
$PAGE->set_title("Official Transcript");
$PAGE->set_heading("Official Transcript");
$PAGE->set_url($CFG->wwwroot . '/reportcard.php');
$PAGE->requires->css('/reportcard/reportcard.css');


global $DB, $USER, $COURSE, $CFG, $program;
$userid = $USER->id; 
$currentYear = '';
$totalBlocks = 0; 
$currentBlock = 0; 
$totalStudentHrs = 0;
$totalClinicHrs = 0; 


echo $OUTPUT->header(); ?> 

<div class="header">
    <a class="header-home"href="/student_login">Canadian College of Traditional Chinese Medicine</a>
  
    <?php 
    if(is_siteadmin() ){
       echo '<a class="header-back"href="/student_login/reportcard/reportcard.php">Go Back</a>'; 
    }
    ?>
</div>


<!-- 
 ***************************************************************
 *******************Display Students List **********************
 ***************************************************************
-->
<div class='main-content'> 

<?php  
 if(is_siteadmin()){ 
  $urlParameter = $_GET['studentid'];

  
  
  function displayFilter($name){
      return trim($name['searchName']);
  }

  
  function formatter ($htmlStr,$counter){
      if($counter % 2 == 1 ) {
          return '<div class="row">' . $htmlStr;
      }else {
          return $htmlStr . '</div>';
      }
  }
  
  if (isset($urlParameter)){
    $userid = $urlParameter; 
    
  } else {
    echo '<h1>Report Card System</h1>';

    echo '<form action="" method="POST">';
    echo '<input class="align-left" type ="text" name="searchName" placeholder="Full Name here"></input>';
    echo '<button type="submit" value="submit">Search</button>';
    echo '<button type="submit" value="reset">Reset</button>';
    echo '</form>';
    
    $allStudents = $DB->get_records('user');
    
    $filterName = displayFilter($_POST);
    
    
    echo '<div class="container-fluid">';
    
    $colCounter = 1;
    $html = '';
    
    foreach ($allStudents as $student){
      $html = '<div class="col-md-6">'.'<a href="/student_login/reportcard/reportcard.php?studentid=' .$student->id . '">' . $student->firstname . ' ' . $student->lastname . ' - studentid:' . $student->id . '</a>'.'</div>'; 
      
      if(strlen($filterName)){
        if(strpos(strtolower($student->firstname . $student->lastname),strtolower($filterName))!==false){
          echo formatter ($html, $colCounter);
          $colCounter++;
        }
      }else {
        echo formatter ($html, $colCounter);
        $colCounter++;
      }
    }
    echo '</div>';
  }
}
?> 
<!-- *************************************************************** -->



<!-- 
***************************************************************
***************Display Transcript Details**********************
***************************************************************
-->

<?php 
$user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0));
$courses = enrol_get_users_courses($userid);
$newblock = false;
$totalBlocks = 0;
$cachedDate = '';
$totalCourse = 0; 
$currentCourseNum = 0;
$program = $DB -> get_record('user_info_data',array('userid' => $userid));

/********************************Header************************/    
if($userid > 2){
    echo '<div class="transcript-container">';
    echo    '<div class="header-section align-center">';
    echo        '<h1 class="c-title">加拿大中醫學院</h1>';
    echo        '<img class="ctmlogo" src="CMTMlogo.png" />';
    echo        '<div class="header-contact align-center">';
    
    ?>  
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 align-left"><h5>1048 Matheson Blvd E, Mississauga, ON L4W 2V2</h5></div>
            <div class="col-12 col-md-3"><h5>Tel: (905)-606-0062</h5></div>
            <div class="col-12 col-md-3 align-right"><h5>E-mail: info@cctcm.ca</h5></div>
        </div>
    </div>
    <?php
   
    echo        '</div>';
    echo    '</div>';
    
    
    
   if(is_siteadmin()){
        echo    '<h3 class="transcript-title align-center">Official Transcript</h3>';
        echo    '<div class ="header-content">';
        echo        '<div class="row">';
        echo            '<div class="col-md-6">';
        echo                '<p><span> Student Name: <input class="align-left input-shorter" type="text" value="'.$user->firstname . $user->lastname . '"/></span></p>';
        echo                '<p><span> Student ID: <input class="align-left input-shorter" type="text" value="'.$userid. '"/></span></p>';
        echo                '<p><span> Program: <input class="align-left input-longer" type="text" value="'.$program ->data.'"/></span></p>';
        echo            '</div>';

        echo            '<div class="col-md-5">';
        echo                '<p><span> Date of Issue: <input class="align-left input-shorter" type="text" value="' . date('M-d-Y') . '"/></span></p>';
        echo                '<p><span> Date Enrolled: <input class="align-left input-shorter" type="text" value="' . date('M-d-Y',$user->timecreated).'"/></span></p>';
        echo            '</div>';
        echo        '</div>';
        echo    '</div>';
    }
    else {
        echo    '<h3 class="transcript-title align-center">Non-official Transcript</h3>';
        echo    '<div class ="header-content">';
        echo        '<div class="row">';
        echo            '<div class="col-md-6">';
        echo                '<p><span> Student Name: '.$user->firstname . $user->lastname . '</span></p>';
        echo                '<p><span> Student ID: '.$userid. '</span></p>';
        echo                '<p><span> Program: '.$program ->data.'</span></p>';
        echo            '</div>';
    
        echo            '<div class="col-md-5">';
        echo                '<p><span> Date of Issue: ' . date('M-d-Y') . '</span></p>';
        echo                '<p><span> Date Enrolled: ' . date('M-d-Y',$user->timecreated).'</span></p>';
        echo            '</div>';
        echo        '</div>';
        echo    '</div>';
    }

    
    echo '</div>';
}
?>


    <?php foreach ($courses as $course) {
      $totalCourse = $totalCourse + 1; 
    }
    ?>


<!-- *************************************************************************
*****************************Grade Items *************************************
****************************************************************************-->
<?php 

usort($courses, function($a,$b){
   return !strtotime(date('ym',$a->startdate)-date('ym',$b->startdate));
});



$reIndexedCourses = array_values($courses);


/************************Get Table Row Cutter**********************************/
$halfRow = round(sizeof($reIndexedCourses)/2);
$tableCutter ;
$left = 0;
$right = 0;
for($i = $halfRow; $i>0; $i--) {
    if(date('ym',$reIndexedCourses[$i] -> startdate) == date('ym',$reIndexedCourses[$i-1] -> startdate)) {
        $left++;
    }else {
        break;
    }
}

for($i = $halfRow; $i>0; $i++) {
    if(date('ym',$reIndexedCourses[$i] -> startdate) == date('ym',$reIndexedCourses[$i-1] -> startdate)) {
        $right++;
    }else {
        break;
    }
}

$tableCutter += $halfRow;

if($left >= $right ) {
    $tableCutter += $right;
}else {
    $tableCutter -= $left;
}
//***************************Row Cutter Ends********************************
if($userid > 2){

$practicalHr = 0;
$theoryHr = 0;
$clinicHr = 0;
$totalHr=0;


echo '<div class="table-wrapper">';

    if(!is_siteadmin()) {
        ?>
        <p id="waterMark-text">Non-Official Transcript</p>
       <?php
    }

foreach ($reIndexedCourses as $index => $course) {
    $coursePassed = false;

    //************************get Practial, Theory, Clinic Hours****************
    $sql = "select t.name from mood_tag t inner join mood_tag_instance i on t.id = i.tagid where i.itemid = " . $course -> id ;
    
    $tagType = $DB -> get_records_sql($sql);
    
    $tagArr = array();
    
    foreach($tagType as $tag) {
        array_push($tagArr, $tag -> name);
    }
    
    $courseHr = in_array("30 hours", $tagArr)? 30: 60;
    
    // if(in_array("practical", $tagArr)){
    //     $practicalHr += $courseHr;
    // }
    // else if(in_array("theory", $tagArr)){
    //     $theoryHr += $courseHr;
    // }
    // else if(in_array("clinical", $tagArr)){
    //     $clinicHr += $courseHr;
    // }
    
    
    if($index === 0 ){
        ?>
        <table class="table-left">
            <tr>
                <th class="course-title">Course Title</th>
                <th>Hours</th>
                <th>Grade</th>
                <th>Term</th>
            </tr>
        <?php
    }
    else if($index == $tableCutter) {
        ?>
            <table class="table-right">
                <th class="course-title">Course Title</th>
                <th>Hours</th>
                <th>Grade</th>
                <th>Term</th>
        <?php
    }
    
    ?>
    
        <?php 
            if(date('ym',$course -> startdate) === date('ym',$reIndexedCourses[$index-1] -> startdate)){
                echo '<tr>';
            }else {
                echo '<tr class="divider">';
            }
        ?>
        
        <?php 
            if(is_siteadmin()) {
              echo '<td><input class="align-left" type="text" value="'. $course -> fullname .'"/></td>';
              echo '<td><input type="text" value="'. $courseHr .'"/></td>';
            }
            else {
              echo '<td>' . $course -> fullname . '</td>';
              echo '<td>' . $courseHr . '</td>';     
            }
        
        ?>
           
            <td> 
            <?php 
            // Get course grade_item
            $course_item = grade_item::fetch_course_item($course->id);

            // Get the stored grade
            $course_grade = new grade_grade(array('itemid'=>$course_item->id, 'userid'=>$userid));
            $course_grade->grade_item =& $course_item;
            $finalgrade = $course_grade->finalgrade;
            $gradetype = $course_item->gradetype; 
            
            if($course_grade -> feedback === 'TC') {
                echo is_siteadmin()? '<input type="text" value="TC"/>' : 'TC';
                
                $coursePassed = true;
            }
            else {
                if (empty($finalgrade)){
                    echo is_siteadmin()? '<input type="text" value="N/A"':'N/A';
                }
                else {
                    if ($gradetype == 2){
                        if($finalgrade == 1){
                            echo is_siteadmin()? '<input type="text" value="PASS"':'PASS';
                            $coursePassed = true;
                        }else {
                            echo is_siteadmin()? '<input tpye="text" value="FAIL"':'FAIL';
                        }
                    } 
                    else {
                      echo is_siteadmin()? '<input type="text" value="'. round($finalgrade) .'"/>' : round($finalgrade);
                      $coursePassed = round($finalgrade) >= 60? true: false;
                    }
                } 
            }
            
            if($coursePassed) {
                if(in_array("practical", $tagArr)){
                    $practicalHr += $courseHr;
                }
                else if(in_array("theory", $tagArr)){
                    $theoryHr += $courseHr;
                }
                else if(in_array("clinical", $tagArr)){
                    $clinicHr += $courseHr;
                }
            }    
            
              $totalHr = $practicalHr + $theoryHr + $clinicHr;
    
            ?> 
            </td>
            
            <td> 
            <?php 
            $season;
            $startMonth = date('m', $course->startdate);
            if ($startMonth <= 4){
                $season = 'W-';
            } 
            else if (($startMonth >= 5) and ($startMonth <= 8)){
                $season = 'S-';
            }
            else if ($startMonth >= 9){
                $season = 'F-';
            }

            if (empty($currentYear)){
                $currentYear = date('Y', $course->startdate); 
            }
            else if ($currentYear !== date('Y', $course->startdate)){
                $currentYear = date('Y', $course->startdate); 
            
            }
            echo is_siteadmin()?  '<input type="text" value="'. $season . date('Y', $course->startdate) .'"/>' : $season . date('Y', $course->startdate); 
            ?> 
            </td> 
        </tr>
    <?php
    
    
    if($index == $tableCutter-1 || ($index + 1) === sizeof($reIndexedCourses) ) {
        ?>
            </table>
        <?php
    }
    
}
    echo '</div>';
}
?>

<?php if($userid > 2){  ?>
<div class="hrs-wrapper">
    <div class="calculated-hr">
        <p>Total Theory Hours: <? echo is_siteadmin()? '<input class="align-left input-shorter" type="text" value="'.$theoryHr ." hours".'"</>' : $theoryHr . " hours"?></p>
        <p>Total Practical Hours: <?php echo is_siteadmin()? '<input class="align-left input-shorter" type="text" value="'.$practicalHr ." hours".'"</>' : $practicalHr . "hours" ?></p>
        <p>Total Clinical Hours: <?php echo is_siteadmin()? '<input class="align-left input-shorter" type="text" value="'.$clinicHr ." hours".'"</>' : $clinicHr . " hours"?></p>
        <p>Total Hours:  <?php echo is_siteadmin()? '<input class="align-left input-shorter" type="text" value="'. $totalHr ." hours".'"</>' : $totalHr . " hours"?></p>
    </div>
    <div class="standarded-hr">
      
    </div>
</div>


<h5 class="warning">Transcript is valid only if bearing seal and register's signature.</h5>
<div class="signature-wrapper">
    <div class="register">
        <p>Register:</p>
        <p>Pierre Chen</p>
    </div>
    <div class="dean">
        <p>Dean:</p>
        <p>Lucian Yu</p>
    </div>
</div>
<div class="footer">
    <h5>Canadian College of Traditional Chinese Medicine</h5>
    <h5>www.cctcm.ca</h5>
</div>
<?php }?>
</body>
