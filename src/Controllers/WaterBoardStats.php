<?php
/**
  * WaterBoardStats processes the imported data.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use App\Models\RIIWaterBoardData;
use ResourceInnovation\CannabisScore\Controllers\ScoreReportStats;

class WaterBoardStats extends ScoreReportStats
{
    public function printWaterReport($nID)
    {
        $this->initClimateFilts();
        $this->searcher->loadAllScoresPublic();


        $this->v["waterBoardCalcs"] = new WaterBoardCalcs;

        return view(
            'vendor.cannabisscore.nodes.1807-water-report', 
            $this->v
        )->render();
    }

}

class WaterBoardCalcs
{
    public $totCnt   = 0;
    public $recs     = [];
    public $tots     = [];
    public $totsCnt  = [];
    public $avgs     = [];
    public $types    = [];
    private $fldsEng = null;
    private $fldsNam = null;
    private $data    = null;

    public function __construct()
    {
        $this->totFlds = [
            'Outdoor Canopy Area', 'Outdoor Plant Count',
            'Mixed Light Canopy Area', 'Mixed Light Plant Count',
            'Indoor Canopy Area', 'Indoor Plant Count', 
            'Total Canopy Area', 'Water Storage Cap (gal)',
            'Annual Storage', 'Annual Application'
        ];
        foreach ($this->totFlds as $fldEng) {
            $this->fldsNam[] = $GLOBALS["SL"]->slugify($fldEng, '_');
        }
        foreach ($this->totFlds as $f => $fldEng) {
            $this->tots[$fldEng]    = 0;
            $this->totsCnt[$fldEng] = 0;
            $this->avgs[$fldEng]    = 0;
        }
        $this->types = [
            'Outdoor'     => new WaterBoardCalcsType('Outdoor'),
            'Mixed Light' => new WaterBoardCalcsType('Mixed Light'),
            'Indoor'      => new WaterBoardCalcsType('Indoor'),
            'Multi-Type'  => new WaterBoardCalcsType('Multi-Type')
        ];
        $this->loadData();
    }

    private function loadData()
    {
        $this->data = RIIWaterBoardData::get();
        $this->totCnt = $this->data->count();
        if ($this->totCnt > 0) {
            foreach ($this->data as $i => $rec) {
                $this->recs[$i] = new WaterBoardRecord($rec);
                foreach ($this->totFlds as $f => $fldEng) {
                    if (!in_array($fldEng, 
                        ['Annual Storage', 'Annual Application'])) {
                        $fldName = 'wa_bo_da_' . $this->fldsNam[$f];
                        if (isset($rec->{ $fldName })
                            && floatval($rec->{ $fldName }) > 0) {
                            $this->tots[$fldEng] += floatval($rec->{ $fldName });
                            $this->totsCnt[$fldEng]++;
                        }
                    }
                }
                if ($this->recs[$i]->storageTot > 0) {
                    $this->tots['Annual Storage'] += $this->recs[$i]->storageTot;
                    $this->totsCnt['Annual Storage']++;
                }
                if ($this->recs[$i]->appliedTot > 0) {
                    $this->tots['Annual Application'] += $this->recs[$i]->appliedTot;
                    $this->totsCnt['Annual Application']++;
                }
                $type = '';
                $types = $this->getRecTypes($rec);
                if (sizeof($types) > 1) {
                    $type = 'Multi-Type';
                } elseif (sizeof($types) > 0) {
                    $type = $types[0];
                }
                if ($type != '') {
                    $this->types[$type]->cnt++;
                    $this->types[$type]->storageTot += $this->recs[$i]->storageTot;
                    $this->types[$type]->appliedTot += $this->recs[$i]->appliedTot;
                    if ($type == 'Multi-Type') {
                        $this->types[$type]->canopy += $this->recs[$i]->getTypeCanopy('Outdoor')
                            +$this->recs[$i]->getTypeCanopy('Mixed Light')
                            +$this->recs[$i]->getTypeCanopy('Indoor');
                        $this->types[$type]->plants += $this->recs[$i]->getTypePlants('Outdoor')
                            +$this->recs[$i]->getTypePlants('Mixed Light')
                            +$this->recs[$i]->getTypePlants('Indoor');
                    } else {
                        $this->types[$type]->canopy += $this->recs[$i]->getTypeCanopy($type);
                        $this->types[$type]->plants += $this->recs[$i]->getTypePlants($type);
                    }
                }
            }
            foreach ($this->totFlds as $f => $fldEng) {
                if ($this->totsCnt[$fldEng] > 0) {
                    $this->avgs[$fldEng] = $this->tots[$fldEng]
                        /$this->totsCnt[$fldEng];
                } else {
                    $this->avgs[$fldEng] = 0;
                }
            }
        }
        return true;
    }

    private function getRecTypes($rec)
    {
        $types = [];
        if (isset($rec->wa_bo_da_outdoor_canopy_area)
            && intVal($rec->wa_bo_da_outdoor_canopy_area) > 0) {
            $types[] = 'Outdoor';
        }
        if (isset($rec->wa_bo_da_mixed_light_canopy_area)
            && intVal($rec->wa_bo_da_mixed_light_canopy_area) > 0) {
            $types[] = 'Mixed Light';
        }
        if (isset($rec->wa_bo_da_indoor_canopy_area)
            && intVal($rec->wa_bo_da_indoor_canopy_area) > 0) {
            $types[] = 'Indoor';
        }
        return $types;
    }

}

class WaterBoardCalcsType
{
    public $type       = '';
    public $cnt        = 0;
    public $canopy     = 0;
    public $plants     = 0;
    public $storageTot = 0;
    public $appliedTot = 0;

    public function __construct($type)
    {
        $this->type = $type;
    }

}

class WaterBoardRecord
{
    public $rec = null;
    public $storage = [];
    public $applied = [];
    public $storageTot = 0;
    public $appliedTot = 0;

    public function __construct($rec)
    {
        $this->rec = $rec;
        $this->storage[] = new WaterBoardRecStorage($rec, 1);
        $this->storage[] = new WaterBoardRecStorage($rec, 2);
        $this->storage[] = new WaterBoardRecStorage($rec, 3);
        $this->applied[] = new WaterBoardRecApply($rec, 1);
        $this->applied[] = new WaterBoardRecApply($rec, 2);
        $this->applied[] = new WaterBoardRecApply($rec, 3);
        for ($ind = 0; $ind < 2; $ind++) {
            $this->storageTot += $this->storage[$ind]->months->tot;
            $this->appliedTot += $this->applied[$ind]->months->tot;
        }
    }

    public function getTypeCanopy($type)
    {
        $fldName = 'wa_bo_da_' . $GLOBALS["SL"]->slugify($type, '_') 
            . '_canopy_area';
        if (isset($this->rec->{ $fldName })) {
            return floatval($this->rec->{ $fldName });
        }
        return 0;
    }

    public function getTypePlants($type)
    {
        $fldName = 'wa_bo_da_' . $GLOBALS["SL"]->slugify($type, '_') 
            . '_plant_count';
        if (isset($this->rec->{ $fldName })) {
            return intVal($this->rec->{ $fldName });
        }
        return 0;
    }
}

class WaterBoardRecStorage
{
    private $ind   = 1;
    public $desc   = '';
    public $months = null;

    public function __construct($rec, $ind)
    {
        $fldName = 'wa_bo_da_water_storage_source_' . $ind;
        if (isset($rec->{ $fldName })) {
            $this->desc = trim($rec->{ $fldName });
        }
        $this->months = new WaterBoardMonths($rec, $ind, 'stored');
    }
}

class WaterBoardRecApply
{
    private $ind   = 1;
    public $desc   = '';
    public $months = null;

    public function __construct($rec, $ind)
    {
        $fldName = 'wa_bo_da_water_application_source_' . $ind;
        if (isset($rec->{ $fldName })) {
            $this->desc = trim($rec->{ $fldName });
        }
        $this->months = new WaterBoardMonths($rec, $ind, 'applied');
    }
}

class WaterBoardMonths
{
    public $mon = [];
    public $tot = 0;

    public function __construct($rec, $ind, $type = 'stored')
    {
        $this->tot = 0;
        for ($mon = 1; $mon <= 12; $mon++) {
            $this->mon[$mon] = 0;
            $monTxt = strtolower($GLOBALS["SL"]->num2Month3($mon));
            $fldName = 'wa_bo_da_water_' . $type 
                . '_source_' . $ind . '_' . $monTxt;
            if (isset($rec->{ $fldName }) 
                && intVal($rec->{ $fldName }) > 0) {
                $this->mon[$mon] = intVal($rec->{ $fldName });
            }
            $this->tot += $this->mon[$mon];
        }
    }
}
