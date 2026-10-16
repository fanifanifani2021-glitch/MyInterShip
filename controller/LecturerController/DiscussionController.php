<?php
defined('BASE_DIR') or die('Direct script access is Not Allowed');
switch ($method) :
    case 'index':
        try {
            $student = StudentInternship::find($nim);
            $internship = Internship::find($id_internship);

            if (!$student || !$internship) {
                Notification::swal2('Error', 'Data not found.', 'error');
                Redirect::back(); exit;
            }

            // --- LOGIKA PAGINASI ---
            $page = (int) (Request::get('page') ?? 1);
            $limit = 10; 
            $offset = ($page - 1) * $limit;

            // Ambil data diskusi
            $dataset = Discussion::where('id_internship', $id_internship)
                        ->orderBy('timestamp_add', 'DESC')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();

            // --- TAMBAHKAN URL DYNAMICS KE DATASET ---
            foreach ($dataset as $row) {
                $id_disc_enc = Encrypter::encrypt($row->id_discussion);
                
                // Kita inject properti baru ke dalam object
                $row->update_url = router('lecturer/my/student.internship.discussion.update', [
                    'nim' => $nim, 
                    'id_internship' => $id_internship, 
                    'id_discussion' => $id_disc_enc
                ]);

                $row->delete_url = router('lecturer/my/student.internship.discussion.destroy', [
                    'nim' => $nim, 
                    'id_internship' => $id_internship, 
                    'id_discussion' => $id_disc_enc
                ]);

                $row->join_url = '';
            }

            // Hitung total untuk link paginasi
            $totalRows = Discussion::where('id_internship', $id_internship)->count();
            $totalPages = ceil($totalRows / $limit);

            renderView('lecturer.internship_management.discussion.index', [
                'title'         => 'Discussion Forum',
                'student'       => $student,
                'internship'    => $internship,
                'dataset'       => $dataset, 
                'currentPage'   => $page,
                'totalPages'    => $totalPages,
                'totalRows'     => $totalRows,
                'nim'           => $nim,
                'id_internship' => $id_internship
            ]);

        } catch (Exception $e) {
            die("System Error: " . $e->getMessage());
        }
    break;

    case 'store':
        try {
            // 1. Ambil ID Dosen dari Session
            $myNik = Session::get('nim_nik_unit');
            $lecturerName = Session::get('name');

            // 2. VERIFIKASI KEPEMILIKAN (Anti-IDOR)
            // Cek apakah data Internship ada dan sesuai dengan NIM di URL
            $internship = Internship::where('id_internship', $id_internship)
                                    ->where('nim', $nim)
                                    ->first();

            if (!$internship) {
                Notification::swal2('Error', 'Internship record not found.', 'error');
                Redirect::back(); exit;
            }

            // Cek apakah mahasiswa tersebut adalah bimbingan dosen yang login
            // Kita ambil object mahasiswanya dari Internship (Lazy Loading)
            $student = $internship->student; 

            if (!$student || $student->nik_dospem !== $myNik) {
                // Jika NIK Dospem di data Mahasiswa tidak sama dengan NIK Dosen di Session
                Notification::swal2('Unauthorized', 'You are not authorized to post for this student.', 'error');
                Redirect::back(); exit;
            }

            // 3. Ambil data dari POST
            $title = Request::post('discussion_title');
            $tag   = Request::post('discussion_tag');
            $topic = Request::post('discussion_topic');

            // 4. Validasi input
            if (empty($title) || empty($topic)) {
                Notification::swal2('Warning', 'Subject and Message are required.', 'warning');
                Redirect::back(); exit;
            }

            // 5. Siapkan data untuk insert
            $data = [
                'id_internship'     => $id_internship,
                'user_type'         => 'Lecturer',
                'user_id'           => $myNik,
                'user_name'         => $lecturerName,
                'discussion_title'  => $title,
                'discussion_topic'  => $topic,
                'discussion_tag'    => $tag,
                'discussion_status' => 'Open',
                'timestamp_add'     => date('Y-m-d H:i:s'),
                'timestamp_update'  => date('Y-m-d H:i:s'), // Inisialisasi dengan waktu sekarang
            ];

            // 6. Simpan menggunakan Model Discussion
            $create = Discussion::create($data);

            if ($create) {
                Notification::swal2('Success', 'New discussion topic has been posted.', 'success');
            } else {
                Notification::swal2('Error', 'Failed to post topic. Please try again.', 'error');
            }

            // 7. Kembali ke halaman sebelumnya
            Response::redirect($_SERVER['HTTP_REFERER']); exit;

        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(); exit;
        }
    break;

    case 'destroy':


        try {
            // Logika verifikasi tetap sama...
            $id_discussion = Encrypter::decrypt($id_discussion);
            $discussion = Discussion::find($id_discussion);

            if ($discussion && $discussion->user_id === Session::get('nim_nik_unit')) {
                $discussion->delete();
                Notification::swal2('Deleted!', 'The topic has been removed successfully.', 'success');
            } else {
                Notification::swal2('Error', 'Unauthorized or topic not found.', 'error');
            }

            Response::redirect($_SERVER['HTTP_REFERER']); exit;

            // Redirect::back(); exit;


        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(); exit;
        }
    break;

    case 'update':
        try {
            $id_discussion = Encrypter::decrypt($id_discussion);
            $myNik = Session::get('nim_nik_unit');

            // Cari diskusinya
            $discussion = Discussion::find($id_discussion);

            // Keamanan: Cek keberadaan & kepemilikan
            if (!$discussion || $discussion->user_id !== $myNik) {
                Notification::swal2('Unauthorized', 'You cannot edit this topic.', 'error');
                Redirect::back(); exit;
            }

            // Ambil input
            $dataUpdate = [
                'discussion_title' => Request::post('discussion_title'),
                'discussion_tag'   => Request::post('discussion_tag'),
                'discussion_topic' => Request::post('discussion_topic'),
                'timestamp_update' => date('Y-m-d H:i:s')
            ];

            if ($discussion->update($dataUpdate)) {
                Notification::swal2('Success', 'Topic updated successfully.', 'success');
            }

            Response::redirect($_SERVER['HTTP_REFERER']); exit;

        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(100); exit;
        }
    break;


    case 'join':
        try {

            $student = StudentInternship::find($nim);
            $internship = Internship::find($id_internship);

            if (!$student || !$internship) {
                Notification::swal2('Error', 'Data not found.', 'error');
                Redirect::back(); exit;
            }


            $id_discussion = Encrypter::decrypt($id_discussion);
            $topic = Discussion::find($id_discussion);

            // Anti-IDOR: Pastikan topik ini milik internship yang sedang diakses
            if (!$topic || $topic->id_internship != $id_internship) {
                Notification::swal2('Error', 'Discussion not found.', 'error');
                Redirect::back(); exit;
            }

            // Ambil komentar (Eager Loading via helper model yang kita buat sebelumnya)
            $comments = $topic->comments(); 

            renderView('lecturer.internship_management.discussion.detail', [
                'title'      => 'Join Discussion',
                'student'       => $student,
                'internship'    => $internship,
                'topic'      => $topic,
                'comments'   => $comments,
                'nim'        => $nim,
                'id_internship' => $id_internship
            ]);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    break;

    case 'comment_store':
        try {
            $commentText = Request::post('comment');

            if (empty($commentText)) {
                Notification::swal2('Warning', 'Comment cannot be empty.', 'warning');
                Redirect::back(); exit;
            }

            $data = [
                'id_discussion' => Encrypter::decrypt($id_discussion),
                'user_id'       => Session::get('nim_nik_unit'),
                'user_name'     => Session::get('name'),
                'user_type'     => 'Lecturer',
                'comment'       => $commentText,
                'timestamp_add' => date('Y-m-d H:i:s'),
                'timestamp_update' => ''
            ];

            if (DiscussionComment::create($data)) {
                Notification::swal2('Success', 'Comment posted successfully.', 'success');
            } else {
                Notification::swal2('Error', 'Failed to post comment.', 'error');
            }

            // Redirect kembali ke halaman join diskusi
            Response::redirect($_SERVER['HTTP_REFERER']); exit;

        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(); exit;
        }
    break;


    case 'comment_destroy':
        try {
            $id_comm_real = Encrypter::decrypt($id_comment);
            $myNik = Session::get('nim_nik_unit');

            $comment = DiscussionComment::find($id_comm_real);

            if (!$comment) {
                Notification::swal2('Error', 'Comment not found.', 'error');
                Redirect::back(); exit;
            }

            // ANTI-IDOR: Cek apakah NIK dosen di session sama dengan user_id di komentar
            if ($comment->user_id !== $myNik) {
                Notification::swal2('Unauthorized', 'You can only delete your own comments.', 'error');
                Redirect::back(); exit;
            }

            if ($comment->delete()) {
                Notification::swal2('Deleted', 'Comment has been removed.', 'success');
            }

            Response::redirect($_SERVER['HTTP_REFERER']); exit;

        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(); exit;
        }
    break;


    case 'comment_update':
        try {
            $id_comm_real = Encrypter::decrypt($id_comment);
            $newComment   = Request::post('comment');
            $myNik        = Session::get('nim_nik_unit');

            // 1. Cari data komentar
            $comment = DiscussionComment::find($id_comm_real);

            // 2. Verifikasi Kepemilikan (Anti-IDOR)
            if (!$comment || $comment->user_id !== $myNik) {
                Notification::swal2('Error', 'Unauthorized action.', 'error');
                Redirect::back(100); exit;
            }

            // 3. Validasi input kosong
            if (empty($newComment)) {
                Notification::swal2('Warning', 'Message cannot be empty.', 'warning');
                Redirect::back(100); exit;
            }

            // 4. Update
            $comment->update([
                'comment' => $newComment,
                'timestamp_update' => date('Y-m-d H:i:s')
            ]);

            Notification::swal2('Updated', 'Response has been updated.', 'success');
            Response::redirect($_SERVER['HTTP_REFERER']); exit;

        } catch (Exception $e) {
            Notification::swal2('Error', $e->getMessage(), 'error');
            Redirect::back(100); exit;
        }
    break;
    
endswitch;