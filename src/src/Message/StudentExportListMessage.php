<?php

namespace App\Message;

use App\Model\Student\ExportStudentListModel;
use Symfony\Component\Uid\Uuid;

class StudentExportListMessage
{
    public function __construct(private Uuid $userId, private ExportStudentListModel $model) {}

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function setUserId(Uuid $userId): StudentExportListMessage
    {
        $this->userId = $userId;

        return $this;
    }


    public function getModel(): ExportStudentListModel
    {
        return $this->model;
    }


    public function setModel(ExportStudentListModel $model): StudentExportListMessage
    {
        $this->model = $model;

        return $this;
    }
}