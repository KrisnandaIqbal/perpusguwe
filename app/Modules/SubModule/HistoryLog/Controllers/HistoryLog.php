<?php

namespace HistoryLog\Controllers;

use HistoryLog\Models\HistoryDataModel;

class HistoryLog extends \Base\Controllers\BaseController
{
    protected $historyModel;

    function __construct()
    {
        $this->historyModel = new HistoryDataModel();
    }

    public function index()
    {
        $data['title'] = 'Riwayat Perubahan Data';

        $filterTable  = $this->request->getGet('table');
        $filterAction = $this->request->getGet('action');
        $filterDateFrom = $this->request->getGet('date_from');
        $filterDateTo   = $this->request->getGet('date_to');

        $builder = $this->historyModel->orderBy('ID', 'DESC');

        if (!empty($filterTable)) {
            $builder->where('TableName', $filterTable);
        }
        if (!empty($filterAction)) {
            $builder->where('Action', $filterAction);
        }
        if (!empty($filterDateFrom)) {
            $builder->where('CreateDate >=', $filterDateFrom . ' 00:00:00');
        }
        if (!empty($filterDateTo)) {
            $builder->where('CreateDate <=', $filterDateTo . ' 23:59:59');
        }

        $perPage = 25;
        $history = $builder->paginate($perPage);
        $pager   = $this->historyModel->pager;

        // Ambil daftar TableName unik untuk dropdown filter
        $db = db_connect();
        $tableList = $db->table('historydata')
            ->select('TableName')
            ->distinct()
            ->orderBy('TableName', 'ASC')
            ->get()
            ->getResultArray();

        // Lengkapi nama user pelaku (create maupun update)
        foreach ($history as $row) {
            $row->CreateByName = get_username($row->CreateBy ?? 0);
            $row->UpdateByName = !empty($row->UpdateBy) ? get_username($row->UpdateBy) : '-';
        }

        $data['history']       = $history;
        $data['pager']         = $pager;
        $data['tableList']     = $tableList;
        $data['filterTable']   = $filterTable;
        $data['filterAction']  = $filterAction;
        $data['filterDateFrom'] = $filterDateFrom;
        $data['filterDateTo']   = $filterDateTo;

        echo view('HistoryLog\Views\index', $data);
    }
}