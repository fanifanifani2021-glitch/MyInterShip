<?php
return array (
  'GET' => 
  array (
    0 => 
    array (
      'pattern' => '/',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'landing',
      'meta' => 
      array (
      ),
    ),
    1 => 
    array (
      'pattern' => '/home',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'home',
      'meta' => 
      array (
      ),
    ),
    2 => 
    array (
      'pattern' => 'student/identity',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'student.identity',
      'meta' => 
      array (
      ),
    ),
    3 => 
    array (
      'pattern' => 'login/',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'login.index',
      'meta' => 
      array (
      ),
    ),
    4 => 
    array (
      'pattern' => 'login/express',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'login.express',
      'meta' => 
      array (
      ),
    ),
    5 => 
    array (
      'pattern' => 'login/student',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'login.student',
      'meta' => 
      array (
      ),
    ),
    6 => 
    array (
      'pattern' => 'login/lecturer',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'login.lecturer',
      'meta' => 
      array (
      ),
    ),
    7 => 
    array (
      'pattern' => 'login/industry',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'login.industry',
      'meta' => 
      array (
      ),
    ),
    8 => 
    array (
      'pattern' => 'login/cdc',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'login.cdc',
      'meta' => 
      array (
      ),
    ),
    9 => 
    array (
      'pattern' => 'announcement/all',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'announcement.index',
      'meta' => 
      array (
      ),
    ),
    10 => 
    array (
      'pattern' => 'announcement/detail/{id}',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'announcement.detail',
      'meta' => 
      array (
      ),
    ),
    11 => 
    array (
      'pattern' => 'register/student',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'register.student',
      'meta' => 
      array (
      ),
    ),
    12 => 
    array (
      'pattern' => 'cdc/study_program/management',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'study_program.management',
      'meta' => 
      array (
      ),
    ),
    13 => 
    array (
      'pattern' => 'cdc/study_program/add',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'study_program.add',
      'meta' => 
      array (
      ),
    ),
    14 => 
    array (
      'pattern' => 'cdc/study_program/edit/{id}',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'study_program.edit',
      'meta' => 
      array (
      ),
    ),
    15 => 
    array (
      'pattern' => 'coordinator/internship_letter/',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'coordinator/internship-letter.management',
      'meta' => 
      array (
      ),
    ),
    16 => 
    array (
      'pattern' => 'coordinator/internship_letter/detail/{id}',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'coordinator/internship-letter.management',
      'meta' => 
      array (
      ),
    ),
    17 => 
    array (
      'pattern' => 'api/docs',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => '',
      'meta' => 
      array (
      ),
    ),
    18 => 
    array (
      'pattern' => 'api/ping',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.ping.get',
      'meta' => 
      array (
        'description' => 'Check if the API is alive',
      ),
    ),
    19 => 
    array (
      'pattern' => 'api/stats/myinternship/overview',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.stats.myinternship-overview',
      'meta' => 
      array (
      ),
    ),
    20 => 
    array (
      'pattern' => 'api/stats/company/count',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.stats.company-count',
      'meta' => 
      array (
      ),
    ),
    21 => 
    array (
      'pattern' => 'api/stats/internship/count',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.stats.internship-count',
      'meta' => 
      array (
      ),
    ),
    22 => 
    array (
      'pattern' => 'api/random/company',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.random.company',
      'meta' => 
      array (
      ),
    ),
    23 => 
    array (
      'pattern' => 'api/random/offer',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.random.offer',
      'meta' => 
      array (
      ),
    ),
    24 => 
    array (
      'pattern' => 'api/random/job',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.random.job',
      'meta' => 
      array (
      ),
    ),
  ),
  'POST' => 
  array (
    0 => 
    array (
      'pattern' => 'student/identity',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'student.identity.post',
      'meta' => 
      array (
      ),
    ),
    1 => 
    array (
      'pattern' => 'login/student',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
        0 => 'csrf',
      ),
      'name' => 'login.student.post',
      'meta' => 
      array (
      ),
    ),
    2 => 
    array (
      'pattern' => 'login/lecturer',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
        0 => 'csrf',
      ),
      'name' => 'login.lecturer.post',
      'meta' => 
      array (
      ),
    ),
    3 => 
    array (
      'pattern' => 'login/industry',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
        0 => 'csrf',
      ),
      'name' => 'login.industry.post',
      'meta' => 
      array (
      ),
    ),
    4 => 
    array (
      'pattern' => 'api/ping',
      'callback' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
      'name' => 'api.ping.post',
      'meta' => 
      array (
      ),
    ),
  ),
);