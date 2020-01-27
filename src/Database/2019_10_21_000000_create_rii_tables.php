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
    	Schema::create('rii_powerscore', function(Blueprint $table)
		{
			$table->increments('ps_id');
			$table->integer('ps_user_id')->unsigned()->nullable();
			$table->integer('ps_status')->unsigned()->nullable();
			$table->boolean('PsIsPro')->default('0')->nullable();
			$table->integer('ps_time_type')->unsigned()->nullable();
			$table->integer('ps_year')->nullable();
			$table->integer('PsPrivacy')->unsigned()->nullable();
			$table->string('PsZipCode', 10)->nullable();
			$table->string('PsCounty', 255)->nullable();
			$table->string('ps_state', 255)->nullable();
			$table->string('PsCountry', 100)->nullable();
			$table->string('ps_ashrae', 10)->nullable();
			$table->string('PsClimateLabel', 12)->nullable();
			$table->string('ps_email')->nullable();
			$table->string('ps_name')->nullable();
			$table->integer('ps_characterize')->unsigned()->nullable();
			$table->double('ps_effic_overall')->nullable();
			$table->double('ps_effic_over_similar')->nullable();
			$table->double('ps_effic_facility')->nullable();
			$table->double('ps_effic_production')->nullable();
			$table->double('ps_effic_lighting')->nullable();
			$table->double('ps_effic_hvac')->nullable();
			$table->double('PsGrams')->nullable();
			$table->double('PsKWH')->nullable();
			$table->double('PsTotalSize')->nullable();
			$table->double('ps_total_canopy_size')->nullable();
			$table->integer('ps_havests_per_year')->nullable();
			$table->char('ps_harvest_batch', 1)->nullable();
			$table->boolean('ps_has_water_pump')->nullable();
			$table->boolean('PsCuresIndoor')->default('0')->nullable();
			$table->boolean('PsCuresOutdoor')->default('0')->nullable();
			$table->boolean('PsCuresOffsite')->default('0')->nullable();
			$table->integer('PsIsIntegrated')->unsigned()->nullable();
			$table->boolean('PsSourceUtility')->nullable();
			$table->boolean('PsSourceRenew')->nullable();
			$table->string('PsOtherPower')->nullable();
			$table->integer('ps_mother_loc')->unsigned()->nullable();
			$table->boolean('PsProcessingOnsite')->nullable();
			$table->string('ps_source_utility_other')->nullable();
			$table->boolean('ps_vertical_stack')->nullable();
			$table->boolean('ps_upload_energy_bills')->nullable();
			$table->boolean('PsEnergyNonFarm')->nullable();
			$table->integer('PsEnergyNonFarmPerc')->nullable();
			$table->longText('PsHvacOther')->nullable();
			$table->boolean('ps_heat_water')->nullable();
			$table->boolean('PsExtractingOnsite')->nullable();
			$table->boolean('ps_controls')->nullable();
			$table->boolean('ps_controls_auto')->nullable();
			$table->string('ps_ip_addy')->nullable();
			$table->string('ps_unique_str')->nullable();
			$table->string('PsTreeVersion')->nullable();
			$table->string('PsVersionAB')->nullable();
			$table->string('ps_is_mobile')->nullable();
			$table->integer('PsSubmissionProgress')->nullable();
			$table->longText('PsNotes')->nullable();
			$table->longText('PsWaterInnovation')->nullable();
			$table->double('ps_green_waste_lbs')->nullable();
			$table->boolean('PsGreenWasteMixed')->nullable();
			$table->string('PsComplianceWasteTrack')->nullable();
			$table->boolean('PsGramsAreWetWeight')->nullable();
			$table->boolean('PsMobileRacking')->nullable();
			$table->integer('PsCrop')->unsigned()->default('481')->nullable();
			$table->string('PsCropOther')->nullable();
			$table->integer('PsOnsiteType')->unsigned()->nullable();
			$table->double('ps_effic_water')->nullable();
			$table->double('ps_effic_waste')->nullable();
			$table->timestamps();
		});
		Schema::create('rii_ps_rankings', function(Blueprint $table)
		{
			$table->increments('ps_rnk_id');
			$table->integer('ps_rnk_psid')->unsigned()->nullable();
			$table->integer('PsRnkManuID')->unsigned()->nullable();
			$table->string('ps_rnk_filters')->nullable();
			$table->double('PsRnkOverall')->nullable();
			$table->double('PsRnkOverallAvg')->nullable();
			$table->double('PsRnkFacility')->nullable();
			$table->double('PsRnkProduction')->nullable();
			$table->double('PsRnkHVAC')->nullable();
			$table->double('PsRnkLighting')->nullable();
			$table->double('PsRnkWater')->nullable();
			$table->double('PsRnkWaste')->nullable();
			$table->integer('ps_rnk_tot_cnt')->default('0')->nullable();
			$table->integer('PsRnkFacilityCnt')->default('0')->nullable();
			$table->integer('PsRnkProductionCnt')->default('0')->nullable();
			$table->integer('PsRnkHVACCnt')->default('0')->nullable();
			$table->integer('PsRnkLightingCnt')->default('0')->nullable();
			$table->integer('PsRnkWaterCnt')->default('0')->nullable();
			$table->integer('PsRnkWasteCnt')->default('0')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSLicenses', function(Blueprint $table)
		{
			$table->increments('PsLicID');
			$table->integer('PsLicPSID')->unsigned()->nullable();
			$table->integer('ps_lic_license')->unsigned()->nullable();
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
			$table->integer('ps_month_psid')->unsigned()->nullable();
			$table->integer('ps_month_month')->nullable();
			$table->double('PsMonthKWH1')->nullable();
			$table->double('PsMonthGrams')->nullable();
			$table->double('PsMonthWasteLbs')->nullable();
			$table->integer('PsMonthOrder')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSUtiliLinks', function(Blueprint $table)
		{
			$table->increments('PsUtLnkID');
			$table->integer('PsUtLnkPSID')->unsigned()->nullable();
			$table->integer('ps_ut_lnk_utility_id')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSRenewables', function(Blueprint $table)
		{
			$table->increments('PsRnwID');
			$table->integer('ps_rnw_psid')->unsigned()->nullable();
			$table->integer('ps_rnw_renewable')->unsigned()->nullable();
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
		Schema::create('rii_ps_areas', function(Blueprint $table)
		{
			$table->increments('ps_area_id');
			$table->integer('ps_area_psid')->unsigned()->nullable();
			$table->integer('ps_area_type')->unsigned()->nullable();
			$table->boolean('ps_area_has_stage')->nullable();
			$table->double('ps_area_size')->nullable();
			$table->integer('ps_area_days_cycle')->nullable();
			$table->boolean('ps_area_lgt_sun')->nullable();
			$table->boolean('ps_area_lgt_dep')->nullable();
			$table->boolean('ps_area_lgt_artif')->nullable();
			$table->integer('PsAreaTotalLightWatts')->nullable();
			$table->double('ps_area_lighting_effic')->nullable();
			$table->boolean('PsAreaVertStack')->nullable();
			$table->integer('ps_area_hvac_type')->unsigned()->nullable();
			$table->longText('PsAreaHvacOther')->nullable();
			$table->double('ps_area_hvac_effic')->nullable();
			$table->double('ps_area_calc_watts')->nullable();
			$table->double('ps_area_calc_size')->nullable();
			$table->integer('PsAreaLgtPattern')->unsigned()->nullable();
			$table->double('ps_area_lgt_fix_size1')->nullable();
			$table->double('ps_area_lgt_fix_size2')->nullable();
			$table->double('ps_area_gallons')->nullable();
			$table->integer('PsAreaWaterFreq')->unsigned()->nullable();
			$table->integer('PsAreaWateringMethod')->unsigned()->nullable();
			$table->double('ps_area_water_effic')->nullable();
			$table->integer('PsAreaCommissioning')->unsigned()->nullable();
			$table->integer('PsAreaTemperature')->nullable();
			$table->integer('PsAreaHumidity')->nullable();
			$table->double('ps_area_sq_ft_per_fix1')->nullable();
			$table->double('ps_area_sq_ft_per_fix2')->nullable();
			$table->timestamps();
		});
		Schema::create('rii_ps_areasBlds', function(Blueprint $table)
		{
			$table->increments('PsArBldID');
			$table->integer('PsArBldAreaID')->unsigned()->nullable();
			$table->integer('ps_ar_bld_type')->unsigned()->nullable();
			$table->string('ps_ar_bld_type_other')->nullable();
			$table->timestamps();
		});
		Schema::create('rii_ps_areasConstr', function(Blueprint $table)
		{
			$table->increments('PsArCnsID');
			$table->integer('PsArCnsBldID')->unsigned()->nullable();
			$table->integer('PsArCnsType')->unsigned()->nullable();
			$table->string('PsArCnsTypeOther')->nullable();
			$table->timestamps();
		});
		Schema::create('rii_ps_areasGreen', function(Blueprint $table)
		{
			$table->increments('PsArGrnID');
			$table->integer('PsArGrnAreaID')->unsigned()->nullable();
			$table->integer('PsArGrnType')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('rii_ps_light_types', function(Blueprint $table)
		{
			$table->increments('PsLgTypID');
			$table->integer('ps_lg_typ_area_id')->unsigned()->nullable();
			$table->integer('ps_lg_typ_light')->unsigned()->nullable();
			$table->integer('ps_lg_typ_count')->nullable();
			$table->double('ps_lg_typ_wattage')->nullable();
			$table->double('ps_lg_typ_hours')->nullable();
			$table->string('ps_lg_typ_make')->nullable();
			$table->string('ps_lg_typ_model')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWaterSources', function(Blueprint $table)
		{
			$table->increments('PsWtrSrcID');
			$table->integer('PsWtrSrcPSID')->unsigned()->nullable();
			$table->integer('PsWtrSrcSource')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWaterHolding', function(Blueprint $table)
		{
			$table->increments('PsWtrHldID');
			$table->integer('PsWtrHldPSID')->unsigned()->nullable();
			$table->integer('PsWtrHldHolding')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWaterFilter', function(Blueprint $table)
		{
			$table->increments('PsWtrFltID');
			$table->integer('PsWtrFltPSID')->unsigned()->nullable();
			$table->integer('PsWtrFltFilter')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSGrowMedia', function(Blueprint $table)
		{
			$table->increments('PsGrwMedID');
			$table->integer('PsGrwMedPSID')->unsigned()->nullable();
			$table->integer('PsGrwMedGrowing')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWasteWater', function(Blueprint $table)
		{
			$table->increments('PsWstWtrID');
			$table->integer('PsWstWtrPSID')->unsigned()->nullable();
			$table->integer('PsWstWtrMethod')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWasteGreen', function(Blueprint $table)
		{
			$table->increments('PSWstGrnID');
			$table->integer('PSWstGrnPSID')->unsigned()->nullable();
			$table->integer('PSWstGrnMethod')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWasteAg', function(Blueprint $table)
		{
			$table->increments('PSWstAgID');
			$table->integer('PSWstAgPSID')->unsigned()->nullable();
			$table->integer('PSWstAgMethod')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWasteSupplies', function(Blueprint $table)
		{
			$table->increments('PSWstSupID');
			$table->integer('PSWstSupPSID')->unsigned()->nullable();
			$table->integer('PSWstSupMethod')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSWasteProcess', function(Blueprint $table)
		{
			$table->increments('PsWstPrcsID');
			$table->integer('PsWstPrcsPSID')->unsigned()->nullable();
			$table->integer('PsWstPrcsMethod')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('rii_ps_for_cup', function(Blueprint $table)
		{
			$table->increments('PsCupID');
			$table->integer('ps_cup_psid')->unsigned()->nullable();
			$table->integer('ps_cup_cup_id')->unsigned()->nullable();
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
		Schema::create('rii_ps_owners', function(Blueprint $table)
		{
			$table->increments('PsOwnID');
			$table->integer('ps_own_partner_user')->unsigned()->nullable();
			$table->integer('PsOwnClientUser')->unsigned()->nullable();
			$table->integer('PsOwnType')->unsigned()->nullable();
			$table->string('PsOwnClientName')->nullable();
			$table->timestamps();
		});
		Schema::create('rii_manufacturers', function(Blueprint $table)
		{
			$table->increments('ManuID');
			$table->string('ManuName')->nullable();
			$table->integer('ManuCntFlower')->nullable();
			$table->integer('ManuCntVeg')->nullable();
			$table->integer('ManuCntClone')->nullable();
			$table->integer('ManuCntMother')->nullable();
			$table->longText('ManuIDsFlower')->nullable();
			$table->longText('ManuIDsVeg')->nullable();
			$table->longText('ManuIDsClone')->nullable();
			$table->longText('ManuIDsMother')->nullable();
			$table->timestamps();
		});
		Schema::create('rii_light_models', function(Blueprint $table)
		{
			$table->increments('lgt_mod_id');
			$table->integer('lgt_mod_manu_id')->nullable();
			$table->string('lgt_mod_name')->nullable();
			$table->integer('lgt_mod_tech')->unsigned()->nullable();
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
			$table->increments('ps_ut_zp_id');
			$table->string('ps_ut_zp_zip_code', 10)->nullable();
			$table->integer('ps_ut_zp_util_id')->unsigned()->nullable();
			$table->timestamps();
		});
		Schema::create('RII_PSPageFeedback', function(Blueprint $table)
		{
			$table->increments('PsPagFeedID');
			$table->integer('PsPagFeedPSID')->unsigned()->nullable();
			$table->boolean('PsPagFeedNewsletter')->default('1')->nullable();
			$table->boolean('PsPagFeedMemberInterest')->nullable();
			$table->boolean('PsPagFeedIncentiveUsed')->nullable();
			$table->boolean('PsPagFeedIncentiveWants')->nullable();
			$table->boolean('PsPagFeedConsiderUpgrade')->nullable();
			$table->longText('PsPagFeedFeedback1')->nullable();
			$table->longText('PsPagFeedFeedback2')->nullable();
			$table->longText('PsPagFeedFeedback3')->nullable();
			$table->longText('PsPagFeedFeedback4')->nullable();
			$table->longText('PsPagFeedFeedback5')->nullable();
			$table->longText('PsPagFeedFeedback6')->nullable();
			$table->longText('PsPagFeedFeedback7')->nullable();
			$table->longText('PsPagFeedFeedback8')->nullable();
			$table->longText('PsPagFeedUniqueness1')->nullable();
			$table->longText('PsPagFeedUniqueness2')->nullable();
			$table->longText('PsPagFeedUniqueness3')->nullable();
			$table->longText('PsPagFeedUniqueness4')->nullable();
			$table->longText('PsPagFeedUniqueness5')->nullable();
			$table->longText('PsPagFeedUniqueness6')->nullable();
			$table->longText('PsPagFeedUniqueness7')->nullable();
			$table->longText('PsPagFeedUniqueness8')->nullable();
			$table->longText('PsPagFeedFeedback9')->nullable();
			$table->timestamps();
		});
		Schema::create('rii_ps_feedback', function(Blueprint $table)
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
		Schema::create('RII_UserInfo', function(Blueprint $table)
		{
			$table->increments('UsrID');
			$table->integer('usr_user_id')->unsigned()->nullable();
			$table->string('usr_company_name')->nullable();
			$table->timestamps();
		});
		Schema::create('RII_UserManufacturers', function(Blueprint $table)
		{
			$table->increments('usr_manu_id');
			$table->integer('usr_manu_user_id')->unsigned()->nullable();
			$table->integer('usr_man_manu_id')->unsigned()->nullable();
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
    	Schema::drop('rii_powerscore');
		Schema::drop('rii_ps_rankings');
		Schema::drop('RII_PSLicenses');
		Schema::drop('RII_PSFarm');
		Schema::drop('RII_PSMonthly');
		Schema::drop('RII_PSUtiliLinks');
		Schema::drop('RII_PSRenewables');
		Schema::drop('RII_PSOtherPower');
		Schema::drop('rii_ps_areas');
		Schema::drop('rii_ps_areasBlds');
		Schema::drop('rii_ps_areasConstr');
		Schema::drop('rii_ps_areasGreen');
		Schema::drop('rii_ps_light_types');
		Schema::drop('RII_PSWaterSources');
		Schema::drop('RII_PSWaterHolding');
		Schema::drop('RII_PSWaterFilter');
		Schema::drop('RII_PSGrowMedia');
		Schema::drop('RII_PSWasteWater');
		Schema::drop('RII_PSWasteGreen');
		Schema::drop('RII_PSWasteAg');
		Schema::drop('RII_PSWasteSupplies');
		Schema::drop('RII_PSWasteProcess');
		Schema::drop('rii_ps_for_cup');
		Schema::drop('RII_PsReferral');
		Schema::drop('RII_PSCommunications');
		Schema::drop('rii_ps_owners');
		Schema::drop('rii_manufacturers');
		Schema::drop('rii_light_models');
		Schema::drop('RII_PSUtilities');
		Schema::drop('RII_PSUtiliZips');
		Schema::drop('RII_PSPageFeedback');
		Schema::drop('rii_ps_feedback');
		Schema::drop('RII_PublicProcess');
		Schema::drop('RII_UserInfo');
		Schema::drop('RII_UserManufacturers');
	
    }
}
