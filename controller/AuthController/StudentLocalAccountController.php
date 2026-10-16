<?php
session_start();
switch ($method):
    default:
        echo "Not Found";
        break;

    case 'index':
        renderView('auth.local-account-student', ['title' => 'Create Local Account']);
        break;

    
    case 'student_local_account_process':


        set_rules('username', 'Username', ['required', 'unique_except:student_internship.username|' .$_SESSION['nim']], ['required' => 'This field is required', 'is_unique' => 'Username has been used']);
        set_rules('no_whatsapp', 'Whatapp Number', ['required', 'except_unique:student_internship.no_whatsapp|' .$_SESSION['no_whatsapp']], ['required' => 'This field is required', 'except_unique' => 'Whatsapp Number has been used']);
        set_rules('password', 'Password', ['required', 'regex_match:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/'], ['required' => 'This field is required', 'regex_match' => 'Password did not meet requirement !']);
        set_rules('confirm_password', 'Password', ['required', 'matches:password'], ['required' => 'This field is required', 'matches' => 'Confirm Password did not match']);

        $cek_form = form_validation();


        if ($cek_form === false) {
            make_snackbar('Check your submitted data and try again later');
            redirect_back(1);
            exit;
        }
        
        // var_dump( $_SESSION['nim'] ); die;

        try {
            // Mendapatkan koneksi PDO
            $pdo = $_SERVER['PDO']; // Asumsi PDO sudah diset dalam server environment
            
            // Query untuk memeriksa apakah nim sudah ada di tabel student_internship
            $query = $pdo->prepare("SELECT * FROM student_internship WHERE nim = :nim AND username IS NULL LIMIT 1");
            $query->bindParam(':nim', $_SESSION['nim']);
            $query->execute();
            
            // Ambil data student
            $student = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($student) {
                // Jika student ditemukan dan username NULL, lakukan update
                if ($student['username'] === NULL) {
                    $updateQuery = $pdo->prepare("UPDATE student_internship SET local_account = 1, username = :username, password = :password, no_whatsapp = :no_whatsapp WHERE nim = :nim AND username IS NULL");
                    $updateQuery->bindParam(':username', $_SESSION['nim']);
                    $updateQuery->bindParam(':no_whatsapp', $_POST['no_whatsapp']);
                    $updateQuery->bindParam(':password', password_hash($_POST['password'],PASSWORD_DEFAULT));
                    $updateQuery->bindParam(':nim', $_SESSION['nim']);
                    $updateQuery->execute();

                    $_SESSION['username'] = $_SESSION['nim'];
                    
                    make_swal2('success','Account Created Successfully', 'Welcome to My Internship ' . $_SESSION['name']);

                    redirect('', 1); exit;

                } else {
                    echo "Username already exists, cannot update.";
                }
            } else {
                echo "Student with NIM " . $_POST['username'] . " not found.";
            }
        } catch (PDOException $e) {
            // Menangani error
            echo "Error: " . $e->getMessage();
        }
        

        break;
endswitch;
