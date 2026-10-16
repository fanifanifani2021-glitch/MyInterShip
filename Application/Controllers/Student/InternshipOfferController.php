<?php

namespace Application\Controllers\Student; // Wajib sesuai folder

use Controller; // Gunakan ini karena Base Controller ada di global namespace

class InternshipController extends Controller {

    public function index($params) {
        return $this->view('cdc.internship.index');
    }

}