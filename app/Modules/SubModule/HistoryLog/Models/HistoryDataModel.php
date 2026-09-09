<?php

namespace HistoryLog\Models;

use CodeIgniter\Model;

class HistoryDataModel extends Model
{
    protected $table            = 'historydata';
    protected $primaryKey       = 'ID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'Action', 'TableName', 'IDRef', 'Note',
        'CreateBy', 'CreateDate', 'CreateTerminal',
        'UpdateBy', 'UpdateDate', 'UpdateTerminal',
        'Member_id', 'Branch_id', 'active'
    ];

    protected $useTimestamps = false;
}