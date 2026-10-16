<?php
// --- 1. KEAMANAN & DEKRIPSI ---
try {
    $id_internship = Encrypter::decrypt($id);
    $nim = $_SESSION['nim'];

    $internship = DB::table('internship')->where('id_internship', $id_internship)->where('nim', $nim)->first();
    if (!$internship) {
        Notification::swal2('Denied', 'Internship data not found!', 'error');
        Redirect::to( router('student.internship') ); exit;
    }
} catch (Exception $e) {
    Redirect::to( router('student.internship') ); exit;
}

// --- 2. SWITCH METHOD ---
switch ($method) :

    case 'index':
        $perPage = 20; // Default 20 data per halaman
        $page = (int)Request::get('page', 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $perPage;

        // Hitung total data untuk menentukan jumlah halaman dropdown
        $totalRows = DB::table('discussion')
            ->where('id_internship', $id_internship)
            ->count();
        
        $totalPages = ceil($totalRows / $perPage) ?: 1;

        $dataset = DB::table('discussion')
            ->where('id_internship', $id_internship)
            ->orderBy('timestamp_add', 'DESC')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        return renderView('student.my_internship.discussion.index', [
            'title'       => 'Discussion Forum',
            'id_enc'      => $id,
            'dataset'     => $dataset,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalRows'   => $totalRows
        ]);
    break;

    case 'store':
        $data = [
            'id_internship'     => $id_internship,
            'user_type'         => 'Student',
            'user_id'           => Session::get('nim'),
            'user_name'         => Session::get('name'),
            'discussion_title'  => Request::post('discussion_title'), // Kolom Baru
            'discussion_tag'    => Request::post('discussion_tag'),   // Kolom Baru
            'discussion_topic'  => Request::post('discussion_topic'),
            'discussion_status' => Request::post('discussion_status', 'Open'),
            'timestamp_add'     => date('Y-m-d H:i:s'),
            'timestamp_update'  => '-'
        ];

        if (DB::table('discussion')->insert($data)) {
            Notification::snackbar('Discussion Topic has been added');
            Redirect::back(1); exit;
        } else {
            Notification::snackbar('Failed to add topic. Please try again.');
            Redirect::back(1); exit;
        }
    break;

    case 'update':
        $disc_id = Request::post('id_discussion'); 
        
        $updateData = [
            'discussion_title'  => Request::post('discussion_title'),
            'discussion_tag'    => Request::post('discussion_tag'),
            'discussion_topic'  => Request::post('discussion_topic'),
            'discussion_status' => Request::post('discussion_status'),
            'timestamp_update'  => date('Y-m-d H:i:s') // Catat waktu perubahan
        ];

        if (DB::table('discussion')->where('id_discussion', $disc_id)->update($updateData)) {
            Notification::snackbar('Discussion updated successfully');
            Redirect::back(1); exit;
        } else {
            Notification::snackbar('No changes made or update failed.');
            Redirect::back(1); exit;
        }
    break;

    case 'join':
        $dec_discussion_id = Encrypter::decrypt($discussion_id);
        $topic = DB::table('discussion')->where('id_discussion', $dec_discussion_id)->first();
        
        // Ambil balasan diskusi
        $comments = DB::table('discussion_comment')
            ->where('id_discussion', $dec_discussion_id)
            ->orderBy('timestamp_add', 'ASC')
            ->get();

        return renderView('student.my_internship.discussion.join', [
            'topic'       => $topic,
            'comments'    => $comments,
            'id_enc'      => $id,
            'disc_id_enc' => $discussion_id
        ]);
    break;


    // --- 3. PROSES BALAS DISKUSI (REPLY) ---
    case 'reply':
        // Dekripsi ID Diskusi jika dikirim terenkripsi dari form
        $disc_id_raw = Encrypter::decrypt(Request::post('id_discussion'));

        $commentData = [
            'id_discussion'    => $disc_id_raw,
            'user_type'        => Request::post('user_type'),
            'user_id'          => Request::post('user_id'),
            'user_name'        => Request::post('user_name'),
            'comment'          => Request::post('comment'),
            'timestamp_add'    => date('Y-m-d H:i:s'),
            'timestamp_update' => '-'
        ];

        if (DB::table('discussion_comment')->insert($commentData)) {
            Notification::snackbar('Comment has been posted');
           Redirect::back(1); exit;
        } else {
            Notification::snackbar('Failed to post comment.');
           Redirect::back(1); exit;
        }
    break;

    // --- 4. PROSES HAPUS (DESTROY) ---
    case 'destroy':
        $disc_id = Request::post('id_discussion');
        
        // Hapus diskusi dan komentar terkait (Cascading manual)
        DB::table('discussion_comment')->where('id_discussion', $disc_id)->delete();
        $delete = DB::table('discussion')->where('id_discussion', $disc_id)->delete();

        if ($delete) {
            echo json_encode(['status' => 'success']); exit;
        } else {
            echo json_encode(['status' => 'error']); exit;
        }
    break;

endswitch;