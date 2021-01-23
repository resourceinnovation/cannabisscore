<!DOCTYPE html><html lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-frame-meter.blade.php -->
<head>
    <link href="/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <script src="/jquery.min.js" type="text/javascript"></script>
    <style>
        body {
            padding: 0px;
            margin: 0px;
            text-align: left;
            background: #{{ $bg }};
        }
        .meterWrap {
            position: relative;
            text-align: left;
            vertical-align: top;
            width: {{ $width }}px;
            height: {{ $height }}px;
            overflow: hidden;
        }
        .meterFill, .meterLines {
            position: absolute;
            z-index: 98;
            top: 0px;
            left: 0px;
            width: {{ $width }}px;
            height: {{ $height }}px;
        }
        .meterFill {
            width: 1px;
        }
        .meterLines .box1, .meterLines .box1fill {
            float: left;
            width: {{ round(($width-120)/10) }}px;
            height: {{ ($height-6) }}px;
            border: 3px #9AC356 solid;
        }
        .meterLines .box1fill {
            background: #9AC356;
        }
        .meterLines .box2 {
            float: left;
            width: 4px;
            height: {{ ($height-6) }}px;
            background: #{{ $bg }};
        }
    </style>
</head><body>

<div class="meterWrap">
    <div class="meterFill"></div>
    <div class="meterLines">
    @for ($i = 0; $i < 10; $i++)
        @if ($i > 0) <div class="box2"></div> @endif
        @if ($i <= floor($perc/10)) <div class="box1fill"></div>
        @else <div class="box1"></div>
        @endif
    @endfor
    </div>
</div>

<script type="text/javascript"> $(document).ready(function() {
    function fillMeter() {
        
        
    }
    setTimeout(function() { fillMeter() }, 100);
}); </script>

</body></html>