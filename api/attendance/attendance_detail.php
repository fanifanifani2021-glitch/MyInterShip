<?php

// Fungsi untuk mendapatkan data logbook, internship, student, supervisor, dan company berdasarkan id_logbook
function getDataByInternship($id_internship, $filter = '')
{
    $result = array();

    $query = $_SERVER['PDO']->prepare("SELECT * FROM internship WHERE id_internship = :id_internship LIMIT 1");
    $query->execute(['id_internship' => $id_internship]);
    $internship = $query->fetch(PDO::FETCH_OBJ);

    if (!$internship) {
        return null;
    }

    $result['internship'] = $internship;


    $nim = $internship->nim;

    $query = $_SERVER['PDO']->prepare("SELECT * FROM student_internship WHERE nim = :nim LIMIT 1");
    $query->execute(['nim' => $nim]);
    $student = $query->fetch(PDO::FETCH_OBJ);

    if (!$student) {
        return null;
    } else {
        unset($student->password);
    }

    $result['student'] = $student;


    $id_supervisor = $internship->id_user_company;

    $query = $_SERVER['PDO']->prepare("SELECT * FROM `user_company` WHERE id_user_company = :id_supervisor LIMIT 1");
    $query->execute(['id_supervisor' => $id_supervisor]);
    $supervisor = $query->fetch(PDO::FETCH_OBJ);

    if ($supervisor) {
        unset($supervisor->password);
    }
    $result['supervisor'] = $supervisor;


    $id_company = $internship->id_company;

    $query = $_SERVER['PDO']->prepare("SELECT * FROM company WHERE id_company = :id_company LIMIT 1");
    $query->execute(['id_company' => $id_company]);
    $company = $query->fetch(PDO::FETCH_OBJ);

    $result['company'] = $company;


    if ($filter != '') {


        $filter2 = date('Y-m', strtotime($filter . '-01'));

        $query = $_SERVER['PDO']->prepare("SELECT * FROM attendance WHERE id_internship = :id_internship AND `date` LIKE :filter_value ORDER BY `date` ASC");
        $query->execute(['id_internship' => $id_internship, 'filter_value' =>  '%' . $filter2 .  '%']);
        $attendance = $query->fetchAll(PDO::FETCH_OBJ);

        if (!$attendance) {
            return null;
        }
    } else {
        $query = $_SERVER['PDO']->prepare("SELECT * FROM attendance WHERE id_internship = :id_internship ORDER BY `date` ASC");
        $query->execute(['id_internship' => $id_internship]);
        $attendance = $query->fetchAll(PDO::FETCH_OBJ);

        if (!$attendance) {
            return null;
        }
    }
    
    $result['attendance'] = $attendance;


    if ($filter != '') {

        $startDate = new DateTime("$filter-01");

        // Create the ending date as the last day of the month
        $endDate = new DateTime("$filter-01");
        $endDate->modify('last day of this month');

        // Find the first day of the month
        $firstDayOfMonth = $startDate->format('N');

        // Determine the offset to shift the weekdays to start from week 1
        // $offset = $firstDayOfMonth - 1;
        // $offset = ($firstDayOfMonth > 1) ? $firstDayOfMonth - 1 : 0;
        $offset = !in_array($firstDayOfMonth, [1, 2, 3, 4, 5]) ? 1 : 0;

        // Initialize an array to store the dates grouped by weeks
        $listOfWeeks = array();
        $new_listOfWeeks = array();
        $listOfWeeks_new = array();
        $new_new = array();

        // Initialize the week counter
        $weekNumber = 1;

        $number = 1;
        $num = 1;
        // Loop through each day within the given range
        while ($startDate <= $endDate) {



            $date = $startDate->format('Y-m-d');    // Get the date

            // Check if the current day is not a Saturday or Sunday (weekend)
            if ($startDate->format('N') < 6) {

                // Add the date to the corresponding week
                $listOfWeeks[$weekNumber][] = $date;

                $new_number = $number++;
                $new_listOfWeeks[$new_number] =  $date;

                $query = $_SERVER['PDO']->prepare("SELECT * FROM attendance WHERE id_internship = :id_internship AND `date` = :filter_value ORDER BY id_attendance DESC LIMIT 1");
                $query->execute(['id_internship' => $id_internship, 'filter_value' =>  $date]);
                $attendance = $query->fetch(PDO::FETCH_OBJ);

                $new_attendance[$date] = array('week_num' => (int) $weekNumber - $offset ?? 1, 'dateOrder' => $startDate->format('N'), 'data' => $attendance);
            }


            // Move to the next day
            $startDate->modify('+1 day');

            // Check if the next day is a Monday (start of a new week)
            if ($startDate->format('N') === '1') {
                $weekNumber++;
            }

            $num++;
        }
    } else {
        $new_listOfWeeks = null;
    }

    $result['weekdays'] = $new_listOfWeeks;
    $result['new_attendance'] = $new_attendance;
    // $result['weekdays'] = $listOfWeeks;



    return $result;
}

// Mendekodekan nilai id_logbook dari parameter GET atau POST
$id_internship = base64_decode($_GET['id_internship'] ?? $_POST['id_internship']);

// Jika nilai id_logbook belum didapatkan dari parameter GET atau POST, maka tetap menggunakan nilai asli
$id_internship = $id_internship ?: ($_GET['id_internship'] ?? $_POST['id_internship']);


// Jika nilai id_logbook belum didapatkan dari parameter GET atau POST, maka tetap menggunakan nilai asli
$filter = $_GET['filter'] ?? $_POST['filter'];


// Memanggil fungsi untuk mendapatkan data berdasarkan id_logbook
$result = getDataByInternship($id_internship, $filter);

if (!$result) {
    // Data tidak ditemukan, kembali dengan response error
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(array('message' => 'Attendance data not found.', 'error_code' => '400'));
    exit;
}

// Mengirimkan response dengan data yang berhasil ditemukan
header('Content-Type: application/json');
http_response_code(200);
echo json_encode($result);
