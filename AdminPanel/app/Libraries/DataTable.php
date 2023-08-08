<?php

namespace App\Libraries;

if (!defined('PATH')) die();

class DataTable
{


    private $query = null;
    private $postData = null;


    public function __construct($query, $pdata = [])
    {
        $this->query = $query;
        $this->postData = $pdata;
    }


    public function query()
    {
        $this->getPaging();
        $this->getOrdering();
        $this->getFiltering();
        return $this->query->get();
    }

    public function getTotalResult($isFiltering = false)
    {
        if ($isFiltering) {
            $this->getFiltering();
            unset($this->query->offset);
            unset($this->query->limit);
            
        }
        return $this->query->get()->count();
    }

    public function make($data)
    {

        $iTotal         = $this->getTotalResult();
        $iFilteredTotal = $this->getTotalResult(true);

        $sOutput = array(
            'draw'              => intval($this->postData['draw']),
            'recordsTotal'      => $iTotal,
            'recordsFiltered'   => $iFilteredTotal,
            'data'              => $data
        );
        return  $sOutput;
    }



    private function getPaging()
    {
        $iStart = $this->postData['start'];
        $iLength = $this->postData['length'];
        if ($iLength != '' && $iLength != '-1') {
            $start = ($iStart) ? $iStart : 0;
            $this->query->offset($start)->limit($iLength);
        }
    }

    private function getOrdering()
    {
        $Data = $this->postData['columns'];
        if (!empty($this->postData['order'])) {
            foreach ($this->postData['order'] as $key) {
                $this->query->orderBy($Data[$key['column']]['data'], $key['dir']);
            }
        }
    }

    private function getFiltering()
    {

        $mColArray  = $this->postData['columns'];
        $search     = $this->postData['search'];
        $sSearch    = trim($search['value']);
        $sWhere = '';
        if ($sSearch != '') {
            for ($i = 0; $i < count($mColArray); $i++) {
                if ($mColArray[$i]['searchable'] == 'true') {
                    if (!empty($mColArray[$i]['data'])) {
                        $col = $mColArray[$i]['data'];
                        $sWhere .= $col . " LIKE '%" . $sSearch . "%' OR ";
                    }
                }
            }
        }
        $sWhere = substr_replace($sWhere, '', -3);
        if ($sWhere != '') {
            $this->query->whereRaw('(' . $sWhere . ')');
        }
    }
}
