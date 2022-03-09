<?php

namespace App\Controllers;

use App\Models\TotemsData;

class TotemsDataController
{
    public function groupByMonth($year, $idTotem)
    {
        $totem = new TotemsData();
        return $totem->groupByMonth($year, intval($idTotem));
    }

    public function groupByDay($year, $month, $idTotem)
    {
        $totem = new TotemsData();
        return $totem->groupByDay($year, $month, intval($idTotem));
    }
}
