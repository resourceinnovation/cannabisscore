<!-- generated from resources/views/vendor/cannabisscore/nodes/1807-water-report.blade.php -->

<div class="slCard nodeWrap">
    <h2 class="slBlueDark">Water Report</h2>
    <?php /*
    <a class="btn btn-secondary mT10" href="?excel=1"
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
        Export to Excel
    </a>
    */ ?>
    <h4>Water Analysis of PowerScore Data</h4>
    <div class="p15 row2">
        <div class="row">
            <div class="col-12">
                Coming Soon!..
            </div>
        </div>
    </div>

    <p><br /></p>

    <h4>Water Board Data 2019</h4>
    <p>
        <a href="/dashboard/db/tbl-raw?tbl=water_board_data"
            >{{ number_format($waterBoardCalcs->totCnt) }} Records</a>
    </p>

    <div class="p15 mT30">
        <div class="row">
            <div class="col-4">
                <b>Data Calculations</b>
            </div>
            <div class="col-2">
                <b>Outdoor</b>
            </div>
            <div class="col-2">
                <b>Mixed Light</b>
            </div>
            <div class="col-2">
                <b>Indoor</b>
            </div>
            <div class="col-2">
                <b>Multi-Type</b>
            </div>
        </div>
    </div>
    <div class="p15 row2">
        <div class="row slGrey">
            <div class="col-4">
                Record Count
            </div>
        @foreach ($waterBoardCalcs->types as $type => $typeStats)
            <div class="col-2">
                {{ number_format($waterBoardCalcs->types[$type]->cnt) }}
            </div>
        @endforeach
        </div>
    </div>
    <div class="p15">
        <div class="row">
            <div class="col-4">
                Storage Gallons? / Canopy Square Feet?
            </div>
        @foreach ($waterBoardCalcs->types as $type => $typeStats)
            <div class="col-2">
                {{ $GLOBALS["SL"]->sigFigs(($waterBoardCalcs->types[$type]->storageTot
                    /$waterBoardCalcs->types[$type]->canopy), 3) }}
            </div>
        @endforeach
        </div>
    </div>
    <div class="p15 row2">
        <div class="row">
            <div class="col-4">
                Applied Gallons? / Canopy Square Feet?
            </div>
        @foreach ($waterBoardCalcs->types as $type => $typeStats)
            <div class="col-2">
                {{ $GLOBALS["SL"]->sigFigs(($waterBoardCalcs->types[$type]->appliedTot
                    /$waterBoardCalcs->types[$type]->canopy), 3) }}
            </div>
        @endforeach
        </div>
    </div>
    <div class="p15">
        <div class="row">
            <div class="col-4">
                Storage Gallons? / Plant
            </div>
        @foreach ($waterBoardCalcs->types as $type => $typeStats)
            <div class="col-2">
                {{ $GLOBALS["SL"]->sigFigs(($waterBoardCalcs->types[$type]->storageTot
                    /$waterBoardCalcs->types[$type]->plants), 3) }}
            </div>
        @endforeach
        </div>
    </div>
    <div class="p15 row2">
        <div class="row">
            <div class="col-4">
                Applied Gallons? / Plant
            </div>
        @foreach ($waterBoardCalcs->types as $type => $typeStats)
            <div class="col-2">
                {{ $GLOBALS["SL"]->sigFigs(($waterBoardCalcs->types[$type]->appliedTot
                    /$waterBoardCalcs->types[$type]->plants), 3) }}
            </div>
        @endforeach
        </div>
    </div>
    <div class="p15">
        <div class="row">
            <div class="col-4">
                Canopy Square Feet? / Plant
            </div>
        @foreach ($waterBoardCalcs->types as $type => $typeStats)
            <div class="col-2">
                {{ $GLOBALS["SL"]->sigFigs(($waterBoardCalcs->types[$type]->canopy
                    /$waterBoardCalcs->types[$type]->plants), 3) }}
            </div>
        @endforeach
        </div>
    </div>
    <div class="p15 row2">
        <div class="row">
            <div class="col-4">
                Plants / Canopy Square Feet?
            </div>
        @foreach ($waterBoardCalcs->types as $type => $typeStats)
            <div class="col-2">
                {{ $GLOBALS["SL"]->sigFigs(($waterBoardCalcs->types[$type]->plants
                    /$waterBoardCalcs->types[$type]->canopy), 3) }}
            </div>
        @endforeach
        </div>
    </div>


    <p><br /></p>
    <p><br /></p>

    <div class="p15">
        <div class="row">
            <div class="col-4">
                <b>Data Field</b>
            </div>
            <div class="col-3">
                <b>Average</b>
            </div>
            <div class="col-3">
                <b>Total</b>
            </div>
            <div class="col-2 slGrey">
                <b>Record Count</b>
            </div>
        </div>
    </div>
    <?php $cnt = 0; ?>
    @foreach ($waterBoardCalcs->tots as $fldEng => $tot)
        <?php $cnt++; ?>
        <div class="p15 @if ($cnt%2 == 1) row2 @endif ">
            <div class="row">
                <div class="col-4">
                    {{ $fldEng }}
                </div>
                <div class="col-3">
                    {{ number_format($waterBoardCalcs->avgs[$fldEng]) }}
                </div>
                <div class="col-3">
                    {{ number_format($tot) }}
                </div>
                <div class="col-2 slGrey">
                    {{ number_format($waterBoardCalcs->totsCnt[$fldEng]) }}
                </div>
            </div>
        </div>
    @endforeach

</div>