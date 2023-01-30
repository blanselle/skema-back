<?php

namespace App\Entity;

interface AutomaticStudentOnPostInterface
{
    public function setStudent(?Student $student): self;
}