<!-- generated from resources/views/vendor/cannabisscore/nodes/976-basic-score-stats.blade.php -->
<div class="nodeAnchor"><a name="n976"></a></div>
<div id="node398" class="nodeWrap">

<div class="slCard nodeWrap">
    <h2 class="slBlueDark">PowerScore Basic Stats</h2>
    <p>
    &darr; <a href="#basicByState" class="mL5 mR5">Completion By State</a>
    </p>
</div>

<div class="nodeAnchor"><a name="basicByState"></a></div>
<div class="slCard nodeWrap">
    <h3 class="slBlueDark">Completed PowerScores by State/Province</h3>
    <h5>Total in Ranked Data Set: {{ $completionStats["ALL"][1] }}</h5>
    <h5>Total Archived / Outliers:  {{ $completionStats["ALL"][0] }}</h5>
    <h5>Current Incomplete Records: {{ $completionStats["ALL"][2] }}</h5>
    <p>
        'Incomplete Records' include website visitors to 
        the PowerScore survey within the past couple days 
        who did not respond to any survey questions.
    </p>

    <table class="table table-striped">
    <?php $cnt = 0; ?>
    @foreach ($completionStats as $abbr => $stats)
        @if ($abbr != 'ALL')
            <?php $cnt++; ?>
            @if ($cnt%10 == 1)
                <tr>
                    <th colspan=2 >State</th>
                    <th style="border-right: 1px #777 solid;">Total Completed</th>
                    <th>Official <nobr>Data Set</nobr></th>
                    <th>Archived / Outliers</th>
                    <th>Incomplete Records</th>
                </tr>
            @endif
                <td>{{ $abbr }}</td>
                <td>{{ $GLOBALS["SL"]->states->getState($abbr) }}</td>
                <td style="border-right: 1px #777 solid;">
                    {{ ($stats[0]+$stats[1]) }}
                </td>
                @if ($stats[1] == 0) <td class="slGrey">-</td> 
                @else <td>{{ $stats[1] }}</td> 
                @endif
                @if ($stats[0] == 0) <td class="slGrey">-</td> 
                @else <td>{{ $stats[0] }}</td> 
                @endif
                @if ($stats[2] == 0) <td class="slGrey">-</td> 
                @else <td>{{ $stats[2] }}</td> 
                @endif
            </tr>
        @endif
    @endforeach
    </table>

</div>

</div> <!-- end #node976 -->