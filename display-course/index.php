<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * List content in content bank.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_login();
$context = get_context_instance (CONTEXT_SYSTEM);
$roles = get_user_roles($context, $USER->id, false);
$role = key($roles);
$roleid = $roles[$role]->roleid;
if(!$roleid){
    echo "error";
}
require_once("$CFG->libdir/formslib.php");

class fileBlock extends moodleform {
    //Add elements to form
    function definition() {
        global $CFG,$USER;
        $mform = $this->_form;
        $servername = "127.0.0.1";
        $username = $CFG->dbuser;
        $password = $CFG->dbpass;
        $dbname = $CFG->dbname;
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $colors = array();
        $color = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
        $colors[$color] = '#' . $color;
        $color = implode(PHP_EOL, $colors);
        $mustache = new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader('templates/css')
        ));
        $template = $mustache->loadTemplate('style');
        echo $template->render(array('color'=>$color));
        $coursename = "
            SELECT DISTINCT c.fullname
            FROM mdl_course c, mdl_enrol e, mdl_user_enrolments ue, mdl_user u,mdl_quiz q
            WHERE c.id = e.courseid
            AND e.id = ue.enrolid
            AND ue.userid = u.id
            AND u.auth = e.enrol
            AND u.id = $USER->id
            AND q.course = c.id";
        $quiz_num = array();
        echo "Find course name <br>";
        echo $coursename."<br>";
        $result = $conn->query($coursename);     
        $quiz_name = array();
    if($result->num_rows>0)
        {  
            $max = $result->num_rows;
            $fetch_coursename = array();
            for($i=0;$i<$max;$i++){
                $row = $result->fetch_assoc();
                $fetch_coursename[] = $row['fullname'];
            }
            echo "Find quiz name <br>";
            for($i=0;$i<$max;$i++){
                $sql_quizname = "
                SELECT q.name
                FROM mdl_quiz q, mdl_course c
                WHERE q.course = c.id
                AND c.fullname = '$fetch_coursename[$i]'
                ";
                echo $sql_quizname."<br>";
                $mustache = new Mustache_Engine(array(
                    'loader' => new Mustache_Loader_FilesystemLoader('templates')
                ));
                $template = $mustache->loadTemplate('courserender');
                echo $template->render(array('coursename'=>$fetch_coursename[$i]));
                $quiz_result = $conn->query($sql_quizname);
                $quiz_num = $quiz_result->num_rows;
                if($quiz_result->num_rows>0){
                    echo '<table>';
                    $mustache = new Mustache_Engine(array(
                        'loader' => new Mustache_Loader_FilesystemLoader('templates')
                    ));
                    $template = $mustache->loadTemplate('columnname');
                    echo $template->render(array());
                    echo "Find grades <br>";
                    while($quiz_row = $quiz_result->fetch_assoc())
                    {   $state = 'finished';                       
                        $out_row = $quiz_row['name'];
                        $sql_grade = "
                        select gg.rawgrademax,qa.sumgrades
                        from mdl_grade_grades gg, mdl_course c,
                        mdl_grade_items gi, mdl_user u,mdl_quiz q, mdl_quiz_attempts qa
                        where gg.usermodified = $USER->id
                        and gg.itemid = gi.id
                        and u.id = gg.userid
                        and u.id = qa.userid
                        and q.id = qa.quiz
                        and gi.itemname = q.name
                        and qa.state = '$state'
                        and q.name = '$out_row'
                        and c.id = q.course
                        and c.fullname = '$fetch_coursename[$i]'
                        order by qa.id
                        desc limit 1;";
                        echo $sql_grade."<br>";
                        $grade_result = $conn->query($sql_grade);
                        if($grade_result->num_rows>0){
                            $quiz_name = $quiz_row['name']; 
                            $mustache = new Mustache_Engine(array(
                                'loader' => new Mustache_Loader_FilesystemLoader('templates')
                            ));
                            while($grade_row=$grade_result->fetch_assoc())
                            {       
                                $total_grades = $total_grades + $grade_row['sumgrades'];
                                $mygrade = (100*$grade_row['sumgrades'])/$grade_row['rawgrademax'];                                                               
                                $max_percent = $max_percent + $grade_row['rawgrademax'];
                                $loss = 100 - $mygrade; 
                                $template = $mustache->loadTemplate('grade_page');
                                echo $template->render(array('rawgrademax' => $grade_row['rawgrademax'],
                                    'rawgrade' => $grade_row['sumgrades'],
                                    'mygrade' =>$mygrade,
                                    'loss' => $loss,
                                    'quiz_name' => $quiz_name
                                ));}
                                $bool = true;
                        }                    
                        else{
                            $bool = false;
                            $template = $mustache->loadTemplate('not_available');
                            echo $template->render(array(''));                            
                        }                    
                    }
                    if($bool){
                        $mustache = new Mustache_Engine(array(
                            'loader' => new Mustache_Loader_FilesystemLoader('templates/css')
                        ));
                        $template = $mustache->loadTemplate('piechart');
                        
                        $deg = $total_grades*3.6;
                        $myfile = fopen("deg.txt", "w") or die("Unable to open file!");
                        fwrite($myfile, $deg."deg");
                        fclose($myfile);
                        $myfile = fopen("deg.txt", "r") or die("Unable to open file!");
                        $mustache_deg = file_get_contents('./deg.txt', true);
                        fclose($myfile);
                        unlink("deg.txt");
                        $deg_loss = (100-$total_grades)*3.6;
                        $myfile = fopen("loss.txt", "w") or die("Unable to open file!");
                        fwrite($myfile, $deg_loss."deg");
                        fclose($myfile);                        
                        $myfile = fopen("loss.txt", "r") or die("Unable to open file!");
                        $mustache_loss = file_get_contents('./loss.txt', true);
                        fclose($myfile);
                        unlink("loss.txt");
                        echo $template->render(array('sum'=>$total_grades,
                            'max'=>$max_percent,
                            'loss'=>$mustache_loss,
                            'color'=>$color,
                            'mustache_deg'=>$mustache_deg
                        ));
                    }
                    else{
                        $template = $mustache->loadTemplate('not_available_chart');
                        echo $template->render(array(''));      
                    }
                    
                    
                    echo "</table>";                   
                }                
                else{
                    echo "This course has not been added with quizzes yet.<br>";
                }               
            }
        }       
    else
    {
        echo "Person has not enrolled a course yet.<br>";
    }
}
}
$PAGE->set_title('Tracking student grade');
$PAGE->set_heading('Tracking student grade');
// Get all contents managed by active plugins where the user has permission to render them.
echo $OUTPUT->header();
$mform = new fileBlock();
if ($mform->is_cancelled()) {
} else if ($fromform = $mform->get_data()) {
} else {
    $mform->set_data($toform);
    $mform->display();
}
echo $OUTPUT->footer();
