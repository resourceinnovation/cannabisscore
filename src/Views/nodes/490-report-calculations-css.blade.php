/* generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-css.blade.php */


#efficScoreMainTitle {
    margin: 50px 0px 20px -15px;
    color: #726658;
}

#scoreCalcsWrapLeft {
    background: #f5f5f3;
    padding: 0px;
    z-index: 10;
}
table.tableScore tr, table.tableScore tr td {
    border: 0px none;
}
table tr#scoreRowFac, table tr#scoreRowHvac, table tr#scoreRowWater {
    background: #ebeee7;
}
table tr#scoreRowProd, table tr#scoreRowLight, table tr#scoreRowWaste {
    background: #f5f5f3;
}
table tr.scoreRowHeader {
    background: #FFF;
}
table tr.scoreRowHeader h5 {
    margin: 10px 0px 10px -10px;
}

.efficBlock {
    color: #636564;
    font-size: 110%;
}
.efficHeadLabel {
    padding: 15px 0px 15px 30px;
}
.efficHeadScore {
    padding: 15px 15px;
}
.efficHeadGuage {
    padding-top: 13px;
}
.efficHeadGuageLabel {
    padding: 15px 15px 15px 30px;
}

#psScoreOverall {
    background: #FFF;
    box-shadow: 0px 10px 15px #DEDEDE;
    position: relative;
    z-index: 1;
}


.scoreCalcs {
    display: none;
    padding: 10px 0px 20px 30px;
}

#efficGuageTxtOverall {
    margin: 0 0 15px 0;
}
#efficBlockOverGuageTitle {
    margin: 15px 15px 5px 0px;
    color: #726658;
}
#efficGuageTxtOverall2 h1 {
    color: #9AC356;
    font-size: 3rem;
    margin: 0px 0px -5px 0px;
}
#efficGuageTxtOverall2 b {
    color: #726658;
    font-size: 1.13rem;
}
#efficGuageTxtOverall3, #efficGuageTxtOverall4 {
    font-size: 80%;
}

#scoreRankFiltWrap {
    background: #01743d;
    color: #FFF;
    padding: 20px 30px 30px 30px;
    margin: 0px -15px;
    position: relative;
    z-index: 10;
}


@media screen and (max-width: 1200px) {
    #treeWrap492 { max-width: 96% }
    .efficHeadScore { padding-left: 30px; }

}
@media screen and (max-width: 992px) {
    #efficGuageTxtOverall2, #efficGuageTxtOverall3, #efficGuageTxtOverall4 { font-size: 100%; }
    #efficGuageTxtOverall3 { margin-top: 25px; margin-bottom: 15px; }
}
@media screen and (max-width: 768px) {
    .efficHeadGuage { padding: 0px 0px 10px 30px; }
    .efficHeadGuageLabel { padding: 0px 0px 0px 30px; }
}


<?php /* 

#node945 { 
    margin-top: 8px;
    padding-left: 13px;
}
#node501 {
    margin-top: -20px;
    padding-left: 13px;
}
#blockWrap945 {
    margin-top: 11px;
}

#hidivBtnFiltsAdv {
    color: #FFF;
}

#bigScoreWrap {
    margin: 30px 0px 0px 0px;
    box-shadow: 0px 20px 60px #DEDEDE;
    border-left: 20px solid #8dc63f;
}
#blockWrap151 {
    margin-top: 40px;
}
#reportTitleWrap {
    margin: 20px 0px 15px 0px;
}

iframe.guageFrame {
    padding: 0px;
    margin: -12px 0 -15px 34px;
}
#guageFrameOverall {
    margin: 15px 0 0 5px;
}

#efficBlockOver {
    background: #FFF;
    border-top: 20px solid #8dc63f;
    border-bottom: 20px solid #8dc63f;
    border-right: 20px solid #8dc63f;
    min-height: 178px;
}
#efficBlockOverLeft {
    background: #8dc63f;
    color: #FFF;
    min-height: 141px;
}

.efficGuageTxt {
    display: block;
    margin: 12px 0 0 0;
}
.efficGuageTxt .slGrey, .efficGuageTxtOver .slGrey {
    font-size: 12px;
    line-height: 16px;
}
.efficBlock {
    width: 100%;
    min-height: 75px;
    padding: 15px 0px 5px 0px;
    border-top: 1px solid #f1f1f1;
}
.efficHeadLabel, .efficHeadScore, .efficHeadLabel2 {
    padding: 10px 15px 0px 15px;
}
.efficHeadScore {
    text-align: right;
}

.efficLabel {
    display: inline;
    margin-left: 15px;
    font-weight: 800;
}

#guageOverallTxt {
    color: #444;
}


@media screen and (max-width: 1200px) {
    #efficGuageTxtOverall {
        padding: 0 0 0 50px;
    }
    iframe.guageFrame {
        margin: -12px 0 -15px 20px;
    }
}
@media screen and (max-width: 992px) {
    #psScoreOverall {
        border-left: 16px #8dc63f solid;
    }
    #efficBlockOverLeft {
        min-height: 160px;
    }
    .efficBlock {
        min-height: 95px;
    }
    .efficHeadLabel {
        margin: -15px 0px 0px 247px;
    }
    .efficHeadScore {
        text-align: left;
        margin: -7px 0 -30px 277px;
    }
    .efficHeadLabel2 {
        margin: -7px 0 0 367px;
    }
    .efficHeadGuage {
        margin: -34px 0 0 41px;
    }
    .efficHeadGuageLabel {
        margin: -23px 0 -22px 277px;
    }
    .efficHeadGuageLabel h5 {
        font-weight: 300;
    }
    .scoreCalcs {
        padding: 25px 0px 20px 30px;
    }
}
@media screen and (max-width: 768px) {
    .efficHeadLabel {
        margin-left: 168px;
    }
    .efficHeadScore {
        margin-left: 199px;
    }
    .efficHeadLabel2 {
        margin: -7px 0 0 297px;
    }
    .efficHeadGuageLabel {
        margin-left: 199px;
    }
}
@media screen and (max-width: 600px) {
    #psScoreOverTxt {
        border-left: 16px #8dc63f solid;
    }
    #efficGuageTxtOverall {
        margin: -20px 0 20px -15px;
    }
    #guageFrameOverall {
        margin: 15px 0 0 14px;
    }
}                                              
@media screen and (max-width: 480px) {

}

*/ ?>
