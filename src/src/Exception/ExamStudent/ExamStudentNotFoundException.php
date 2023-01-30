<?php

namespace App\Exception\ExamStudent;

use Exception;

class ExamStudentNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct();
    }
}
