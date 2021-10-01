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
 * Bulk Activity Creation
 * @package    block_enrollforms
 * @copyright  2019 Queen Mary University of London
 * @author     Shubhendra R Doiphode <doiphode.sunny@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Student Enrollment';
$string['email'] = 'Email';
$string['firstname'] = 'Firstname';
$string['enrollforms'] = 'Student Enrollment';
$string['lastname'] = 'Lastname';
$string['totalallottedstudents'] = 'Total allotted students';
$string['teacherenrolledcourses'] = 'Teacher enrolled courses';
$string['addteacher'] = 'Add teacher';
$string['chooseusernamepassword'] = 'Choose a username and password';
$string['teacherslastname'] = 'Teacher’s last name';
$string['teachersfirstname'] = 'Teacher’s first name';
$string['teachersemail'] = 'Teacher’s email';
$string['username'] = 'Username';
$string['password'] = 'Password';
$string['thankyouforcreatingaccount'] = 'Congratulations your ED Basecamp account has been created.';
$string['choosebtn'] = 'Please choose one of the buttons below to continue:';
$string['addastudent'] = 'Add Student Users';
$string['gotoyouredbasecampcourses'] = 'Go to Your ED Basecamp Courses';
$string['thankyou'] = 'Thank you';
$string['description'] = 'Class description (optional)';
$string['noofuser'] = 'How many students do you want to add to this class?';
$string['selectcourse'] = 'Select a Course/Section Number';
$string['continue'] = 'Continue';
$string['stufirstname'] = 'Student’s first name';
$string['stulastname'] = 'Student’s last name';
$string['studentinfo'] = 'Review student information';
$string['addstudent'] = 'Add Student';
$string['studentaddedsuccessfully'] = 'Students added Successfully';
$string['csvupload'] = 'CSV Upload';
$string['email_help'] = 'Emal';
$string['firstname_help'] = 'Firstname';
$string['lastname_help'] = 'Lastname';
$string['teacheraddedsuccessfully'] = 'Teacher added successfully';
$string['totalallottedstudents_help'] = 'Enter total allotted students';
$string['availabletoseats'] = 'You have {$a->noallotment} user seats available to add to this or other classes';
$string['teacheraccountsubject'] = 'Activate Account';
$string['totalenrolledstudents'] = 'Total enrolled Students {$a->count}';
//$string['teachermailtemplate'] = 'Dear {$a->firstname},We are elated that your school has subscribed to the ED Basecamp eLearning Platform to help you navigate thru the summer school sessions this year. This digital platform we have created is a win-win for both students and teachers.Please click the link below to activate your account and complete the Class Roster(s), by entering the names of your students.We look forward to working with you and stay well,';
$string['teachermailtemplate'] = '<div style="font-size: 16px;font-family: Calibri;"><p dir="ltr" style="text-align: left;"></p>
<div>Dear {$a->firstname},</div><br>

<div>We are elated that your school has subscribed to the&nbsp;<span style="">ED Basecamp&nbsp;</span><span style="">eLearning</span></div>
<div>Platform to help you navigate thru the summer school sessions this year. This </div>
<div>digital platform we have created is a win-win for both students and teachers.</div>
<div><br></div>
<div>Please click the link below to activate your account and complete the Class </div>
<div>Roster(s), by entering the names of your students.</div>
<div><br></div>
<div>We look forward to working with you and stay well,</div><br>
{$a->link}
<p></p>
If you wish to contact us for any reason, please respond to us at:  <a href = "mailto: subscription@educationalbootcamp.com"> subscription@educationalbootcamp.com</a></div>
<p></p>
</div>

<br><footer>
    <div>Herwins Noe</div>
    <div>Learning Management Systems Technician</div>
    <div>(305) 423-1999 ext.3860</div>
<div>(office)</div>
    <div>(305) 423-1132 (fax)</div>
   <div> <a href = "mailto: herwins@educationalbootcamp.net">herwins@educationalbootcamp.net</a></div>
     <div>   <a href = "mailto: educationalbootcamp.net"> educationalbootcamp.net</a></div>


</footer>';


$string['adduserpateheader'] = 'Please select a course and enter the number of students for that course. Your total allotment
of students is {$a->tallotment}. You cannot add more than {$a->noallotment} total students.';

