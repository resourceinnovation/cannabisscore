<!DOCTYPE html><html lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-frame-guage.blade.php -->
<head>
    <link href="/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <script src="/jquery.min.js" type="text/javascript"></script>
    <style>
        body {
            padding: 0px;
            margin: 0px;
            text-align: left;
            background: #FFF;
        }
        .guageWrap {
            position: relative;
            text-align: left;
            vertical-align: top;
            width: {{ $size }}px;
            height: {{ round(0.66*$size) }}px;
            overflow: hidden;
        }
        .guageImgMeter, .guageWrap .guageImgMeter {
            position: absolute;
            z-index: 98;
            top: 0px;
            left: 0px;
            height: {{ $size }}px;
            width: {{ $size }}px;
        }
        .guageImgDial, .guageWrap .guageImgDial {
            position: absolute;
            z-index: 99;
            top: 50%;
            left: 50%;
            margin-top: -{{ round(0.32*$size) }}px;
            margin-left: -{{ round(0.5*$size) }}px;
            height: {{ $size }}px;
            width: {{ $size }}px;

            -webkit-transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
            -moz-transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
            -o-transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
            transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
        }
    </style>
</head><body>

<div class="guageWrap">
    <img src="/cannabisscore/uploads/greenometer2-foreground-dial.png" id="dialID" class="guageImgDial" border=0 >
    <img src="/cannabisscore/uploads/greenometer2-background-meter.png" class="guageImgMeter" border=0 >
</div>

<script type="text/javascript"> $(document).ready(function() {
    function rotateDial() {
        var degrees = Math.round( ({{ $perc }}/100) * 180);
        $('#dialID').css('-moz-transform', 'rotate(' + degrees + 'deg)')
            .css('-webkit-transform', 'rotate(' + degrees + 'deg)')
            .css('-o-transform', 'rotate(' + degrees + 'deg)')
            .css('-ms-transform', 'rotate(' + degrees + 'deg)');
    }
    setTimeout(function() { rotateDial() }, 100);
}); </script>

</body></html>