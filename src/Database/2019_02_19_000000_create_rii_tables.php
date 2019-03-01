<?php 
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-gen-migration.blade.php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RIICreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
    	Schema::create('RII_PowerScore', function(Blueprint $table)
		{
			$table->increments('PsID');
			$table->integer('PsUserID')->unsigned()->nullable();
			$table->integer('PsStatus')->unsigned()->nullable();
			$table->integer('PsTimeType')->unsigned()->nullable();
			$table->integer('PsYear')->nullable();
			$table->integer('PsPrivacy')->unsigned()->nullable();
			$table->string('PsZipCode', 10)->nullable();
			$table->string('PsCounty', 255)->nullable();
			$table->string('PsState', 255)->nullable();
			$table->string('PsCountry', 100)->nullable();
			$table->string('PsAshrae', 10)->nullable();
			$table->string('PsEmail')->nullable();
			$table->boolean('PsNewsletter')->default('1')->nullable();
			$table->boolean('PsMemberInterest')->nullable();
			$table->string('PsName')->nullable();
			$table->integer('PsCharacterize')->unsigned()->nullable();
			$table->double('PsEfficOverall')->nullable();
			$table->double('PsEfficOverSimilar')->nullable();
			$table->double('PsEfficFacility')->nullable();
			$table->double('PsEfficProduction')->nullable();
			$table->double('PsEfficLighting')->nullable();
			$table->double('PsEfficLightingMother')->nullable();
			$table->double('PsEfficLightingClone')->nullable();
			$table->double('PsEfficLightingVeg')->nullable();
			$table->double('PsEfficLightingFlower')->nullable();
			$table->double('PsEfficHvac')->nullable();
			$table->double('PsGrams')->nullable();
			$table->double('PsKWH')->nullable();
			$table->double('PsTotalSize')->nullable();
			$table->double('PsTotalCanopySize')->nullable();
			$table->integer('PsHavestsPerYear')->nullable();
			$table->char('PsHarvestBatch', 1)->nullable();
			$table->boolean('PsHasWaterPump')->nullable();
			$table->boolean('PsCuresIndoor')->default('0')->nullable();
			$table->boolean('PsCuresOutdoor')->default('0')->nullable();
			$table->boolean('PsCuresOffsite')->default('0')->nullable();
			$table->integer('PsIsIntegrated')->unsigned()->nullable();
			$table->boolean('PsSourceUtility')->nullable();
			$table->boolean('PsSourceRenew')->nullable();
			$table->string('PsOtherPower')->nullable();
			$table->boolean('PsIncentiveUsed')->nullable();
			$table->boolean('PsIncentiveWants')->nullable();
			$table->integer('PsMotherLoc')->unsigned()->nullable();
			$table->boolean('PsProcessingOnsite')->nullable();
			$table->string('PsSourceUtilityOther')->nullable();
			$table->boolean('PsVerticalStack')->nullable();
			$table->boolean('PsConsiderUpgrade')->nullable();
			$table->boolean('PsUploadEnergyBills')->nullable();
			$table->boolean('PsEnergyNonFarm')->nullable();
			$table->integer('PsEnergyNonFarmPerc')->nullable();
			$table->longText('PsHvacOther')->nullable();
			$table->boolean('PsHeatWater')->nullable();
			$table->boolean('PsExtractingOnsite')->nullable();
			$table->boolean('PsControls')->nullable();
			$table->boolean('PsControlsAuto')->nullable();
			$table->longText('PsFeedback1')->nullable();
			$table->longText('PsFeedback2')->nullable();
			$table->longText('PsFeedback3')->nullable();
			$table->longText('PsFeedback4')->nullable();
			$table->longText('PsFeedback5')->nullable();
			$table->longText('PsFeedback6')->nullable();
			$table->longText('PsFeedback7')->nullable();
			$table->longText('PsFeedback8')->nullable();
			$table->longText('PsUniqueness1')->nullable();
			$table->longText('PsUniqueness2')->nullable();
			$table->longText('PsUniqueness3')->nullable();
			$table->longText('PsUniqueness4')->nullable();
			$table->longText('PsUniqueness5')->nullable();
			$table->longText('PsUniqueness6')->nullable();
			$table->longText('PsUniqueness7')->nullable();
			$table->longText('PsUniqueness8')->nullable();
			$table->string('PsIPaddy')->nullable();
			$table->string('PsUniqueStr')->nullable();
			$table->string('PsTreeVersion')->nullable();
			$table->string('PsVersionAB')->nullable();
			$table->string('PsIsMobile')->nullable();
			$table->integer('PsSubmissionProgress')->nullable();
			$table->longText('PsNotes')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSRankings', function(Blueprint $table)
		{
			$table->increments('PsRnkID');
			$table->integer('PsRnkPSID')->unsigned()->nullable();
			$table->string('PsRnkFilters')->nullable();
			$table->double('PsRnkOverall')->nullable();
			$table->double('PsRnkOverallAvg')->nullable();
			$table->double('PsRnkFacility')->nullable();
			$table->double('PsRnkProduction')->nullable();
			$table->double('PsRnkHVAC')->nullable();
			$table->double('PsRnkLighting')->nullable();
			$table->integer('PsRnkTotCnt')->default('0')->nullable();
			$table->integer('PsRnkFacilityCnt')->default('0')->nullable();
			$table->integer('PsRnkProductionCnt')->default('0')->nullable();
			$table->integer('PsRnkHVACCnt')->default('0')->nullable();
			$table->integer('PsRnkLightingCnt')->default('0')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSLicenses', function(Blueprint $table)
		{
			$table->increments('PsLicID');
			$table->integer('PsLicPSID')->unsigned()->nullable();
			$table->integer('PsLicLicense')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSFarm', function(Blueprint $table)
		{
			$table->increments('PsFrmID');
			$table->integer('PsFrmPSID')->unsigned()->nullable();
			$table->integer('PsFrmType')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSMonthly', function(Blueprint $table)
		{
			$table->increments('PsMonthID');
			$table->integer('PsMonthPSID')->unsigned()->nullable();
			$table->integer('PsMonthMonth')->nullable();
			$table->double('PsMonthKWH1')->nullable();
			$table->double('PsMonthKWH2')->nullable();
			$table->longText('PsMonthNotes')->nullable();
			$table->integer('PsMonthOrder')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSUtiliLinks', function(Blueprint $table)
		{
			$table->increments('PsUtLnkID');
			$table->integer('PsUtLnkPSID')->unsigned()->nullable();
			$table->integer('PsUtLnkUtilityID')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSRenewables', function(Blueprint $table)
		{
			$table->increments('PsRnwID');
			$table->integer('PsRnwPSID')->unsigned()->nullable();
			$table->integer('PsRnwRenewable')->unsigned()->nullable();
			$table->integer('PsRnwLoadPercent')->nullable();
			$table->string('PsRnwKWH')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSOtherPower', function(Blueprint $table)
		{
			$table->increments('PsOthPwrID');
			$table->integer('PsOthPwrPSID')->unsigned()->nullable();
			$table->integer('PsOthPwrSource')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSAreas', function(Blueprint $table)
		{
			$table->increments('PsAreaID');
			$table->integer('PsAreaPSID')->unsigned()->nullable();
			$table->integer('PsAreaType')->unsigned()->nullable();
			$table->boolean('PsAreaHasStage')->nullable();
			$table->double('PsAreaSize')->nullable();
			$table->boolean('PsAreaLgtSun')->nullable();
			$table->boolean('PsAreaLgtDep')->nullable();
			$table->boolean('PsAreaLgtArtif')->nullable();
			$table->integer('PsAreaTotalLightWatts')->nullable();
			$table->double('PsAreaLightingEffic')->nullable();
			$table->integer('PsAreaDaysCycle')->nullable();
			$table->boolean('PsAreaVertStack')->nullable();
			$table->integer('PsAreaHvacType')->unsigned()->nullable();
			$table->longText('PsAreaHvacOther')->nullable();
			$table->double('PsAreaCalcWatts')->nullable();
			$table->double('PsAreaCalcSize')->nullable();
			$table->integer('PsAreaLgtPattern')->unsigned()->nullable();
			$table->double('PsAreaLgtFixSize1')->nullable();
			$table->double('PsAreaLgtFixSize2')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSAreasBlds', function(Blueprint $table)
		{
			$table->increments('PsArBldID');
			$table->integer('PsArBldAreaID')->unsigned()->nullable();
			$table->integer('PsArBldType')->unsigned()->nullable();
			$table->string('PsArBldTypeOther')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSAreasConstr', function(Blueprint $table)
		{
			$table->increments('PsArCnsID');
			$table->integer('PsArCnsBldID')->unsigned()->nullable();
			$table->integer('PsArCnsType')->unsigned()->nullable();
			$table->string('PsArCnsTypeOther')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSLightTypes', function(Blueprint $table)
		{
			$table->increments('PsLgTypID');
			$table->integer('PsLgTypAreaID')->unsigned()->nullable();
			$table->integer('PsLgTypLight')->unsigned()->nullable();
			$table->integer('PsLgTypCount')->nullable();
			$table->double('PsLgTypWattage')->nullable();
			$table->double('PsLgTypHours')->nullable();
			$table->string('PsLgTypMake')->nullable();
			$table->string('PsLgTypModel')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSForCup', function(Blueprint $table)
		{
			$table->increments('PsCupID');
			$table->integer('PsCupPSID')->unsigned()->nullable();
			$table->integer('PsCupCupID')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PsReferral', function(Blueprint $table)
		{
			$table->increments('PsRefID');
			$table->string('PsRefVersionAB')->nullable();
			$table->integer('PsRefSubmissionProgress')->nullable();
			$table->string('PsRefIPaddy')->nullable();
			$table->string('PsRefTreeVersion')->nullable();
			$table->string('PsRefUniqueStr')->nullable();
			$table->integer('PsRefUserID')->unsigned()->nullable();
			$table->string('PsRefIsMobile')->nullable();
			$table->integer('PsRefPowerScore')->unsigned()->nullable();
			$table->integer('PsRefUtility')->unsigned()->nullable();
			$table->string('PsRefAddress')->nullable();
			$table->string('PsRefEmail')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSCommunications', function(Blueprint $table)
		{
			$table->increments('PsComID');
			$table->integer('PsComPSID')->unsigned()->nullable();
			$table->integer('PsComUser')->unsigned()->nullable();
			$table->longText('PsComDescription')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_Manufacturers', function(Blueprint $table)
		{
			$table->increments('ManuID');
			$table->string('ManuName')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_LightModels', function(Blueprint $table)
		{
			$table->increments('LgtModID');
			$table->string('LgtModName')->nullable();
			$table->integer('LgtModTech')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSUtilities', function(Blueprint $table)
		{
			$table->increments('PsUtID');
			$table->string('PsUtName')->nullable();
			$table->integer('PsUtType')->unsigned()->nullable();
			$table->string('PsUtEmail')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSUtiliZips', function(Blueprint $table)
		{
			$table->increments('PsUtZpID');
			$table->string('PsUtZpZipCode', 10)->nullable();
			$table->integer('PsUtZpUtilID')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PsFeedback', function(Blueprint $table)
		{
			$table->increments('PsfID');
			$table->string('PsfVersionAB')->nullable();
			$table->integer('PsfSubmissionProgress')->nullable();
			$table->string('PsfIPaddy')->nullable();
			$table->string('PsfTreeVersion')->nullable();
			$table->string('PsfUniqueStr')->nullable();
			$table->integer('PsfUserID')->unsigned()->nullable();
			$table->string('PsfIsMobile')->nullable();
			$table->integer('PsfPsID')->unsigned()->nullable();
			$table->longText('PsfFeedback1')->nullable();
			$table->longText('PsfFeedback2')->nullable();
			$table->longText('PsfFeedback3')->nullable();
			$table->char('PsfFeedback4', 1)->nullable();
			$table->longText('PsfFeedback5')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PublicProcess', function(Blueprint $table)
		{
			$table->increments('PubPrcID');
			$table->longText('PubPrcLikeProducers')->nullable();
			$table->longText('PubPrcLikeProducersVisual')->nullable();
			$table->integer('PubPrcRafflePrizes')->unsigned()->nullable();
			$table->longText('PubPrcPrizeOther')->nullable();
			$table->longText('PubPrcGrowerOtherValue')->nullable();
			$table->longText('PubPrcFeedback1')->nullable();
			$table->integer('PubPrcUserID')->unsigned()->nullable();
			$table->integer('PubPrcSubmissionProgress')->nullable();
			$table->string('PubPrcTreeVersion')->nullable();
			$table->string('PubPrcVersionAB')->nullable();
			$table->string('PubPrcUniqueStr')->nullable();
			$table->string('PubPrcIPaddy')->nullable();
			$table->string('PubPrcIsMobile')->nullable();
			$table->timestamps();
		});
	
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
    	Schema::drop('RII_PowerScore');
		Schema::drop('RII_PSRankings');
		Schema::drop('RII_PSLicenses');
		Schema::drop('RII_PSFarm');
		Schema::drop('RII_PSMonthly');
		Schema::drop('RII_PSUtiliLinks');
		Schema::drop('RII_PSRenewables');
		Schema::drop('RII_PSOtherPower');
		Schema::drop('RII_PSAreas');
		Schema::drop('RII_PSAreasBlds');
		Schema::drop('RII_PSAreasConstr');
		Schema::drop('RII_PSLightTypes');
		Schema::drop('RII_PSForCup');
		Schema::drop('RII_PsReferral');
		Schema::drop('RII_PSCommunications');
		Schema::drop('RII_Manufacturers');
		Schema::drop('RII_LightModels');
		Schema::drop('RII_PSUtilities');
		Schema::drop('RII_PSUtiliZips');
		Schema::drop('RII_PsFeedback');
		Schema::drop('RII_PublicProcess');
	
    }
}
