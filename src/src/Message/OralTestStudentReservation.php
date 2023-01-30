<?php

namespace App\Message;

class OralTestStudentReservation
{
    public function __construct(private int $oralTestStudentId) {}

    public function getOralTestStudentId(): int
    {
        return $this->oralTestStudentId;
    }

    public function setOralTestStudentId(int $oralTestStudentId): OralTestStudentReservation
    {
        $this->oralTestStudentId = $oralTestStudentId;

        return $this;
    }
}