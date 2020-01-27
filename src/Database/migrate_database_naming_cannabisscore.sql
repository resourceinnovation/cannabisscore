
DROP TABLE RII_Book;
DROP TABLE RII_Business;
DROP TABLE RII_BusinessSectors;
DROP TABLE RII_BusinessTagLinks;
DROP TABLE RII_BusinessTags;
DROP TABLE RII_Events;
DROP TABLE RII_Externalities;
DROP TABLE RII_ExternConvert;
DROP TABLE RII_ExternTypes;
DROP TABLE RII_Items;
DROP TABLE RII_Tags;
DROP TABLE RII_Units;
DROP TABLE RII_UnitsConvert;

ALTER TABLE sl_uploads CHANGE `UpDesc` `up_evidence_desc` text;

ALTER TABLE RII_Competitors RENAME TO rii_competitors;
ALTER TABLE RII_LightModels RENAME TO rii_light_models;
ALTER TABLE RII_Manufacturers RENAME TO rii_manufacturers;
ALTER TABLE RII_PowerScore RENAME TO rii_powerscore;
ALTER TABLE RII_PowerScoreBasic RENAME TO rii_powerscore_basic;
ALTER TABLE RII_PSAreas RENAME TO rii_ps_areas;
ALTER TABLE RII_PSAreasBlds RENAME TO rii_ps_areas_blds;
ALTER TABLE RII_PSAreasConstr RENAME TO rii_ps_areas_constr;
ALTER TABLE RII_PSAreasGreen RENAME TO rii_ps_areas_green;
ALTER TABLE RII_PSCommunications RENAME TO rii_ps_communications;
ALTER TABLE RII_PSFarm RENAME TO rii_ps_farm;
ALTER TABLE RII_PsFeedback RENAME TO rii_ps_feedback;
ALTER TABLE RII_PSForCup RENAME TO rii_ps_for_cup;
ALTER TABLE RII_PSGreenhouses RENAME TO rii_ps_greenhouses;
ALTER TABLE RII_PSGrowMedia RENAME TO rii_ps_grow_media;
ALTER TABLE RII_PSHvac RENAME TO rii_ps_hvac;
ALTER TABLE RII_PSLicenses RENAME TO rii_ps_licenses;
ALTER TABLE RII_PSLightTypes RENAME TO rii_ps_light_types;
ALTER TABLE RII_PSMonthly RENAME TO rii_ps_monthly;
ALTER TABLE RII_PSOtherPower RENAME TO rii_ps_other_power;
ALTER TABLE RII_PSOwners RENAME TO rii_ps_owners;
ALTER TABLE RII_PSPageFeedback RENAME TO rii_ps_page_feedback;
ALTER TABLE RII_PSRankings RENAME TO rii_ps_rankings;
ALTER TABLE RII_PSRanks RENAME TO rii_ps_ranks;
ALTER TABLE RII_PsReferral RENAME TO rii_ps_referral;
ALTER TABLE RII_PSRenewables RENAME TO rii_ps_renewables;
ALTER TABLE RII_PSUtiliLinks RENAME TO rii_ps_utili_links;
ALTER TABLE RII_PSUtilities RENAME TO rii_ps_utilities;
ALTER TABLE RII_PSUtiliZips RENAME TO rii_ps_utili_zips;
ALTER TABLE RII_PSWasteAg RENAME TO rii_ps_waste_ag;
ALTER TABLE RII_PSWasteGreen RENAME TO rii_ps_waste_green;
ALTER TABLE RII_PSWasteProcess RENAME TO rii_ps_waste_process;
ALTER TABLE RII_PSWasteSupplies RENAME TO rii_ps_waste_supplies;
ALTER TABLE RII_PSWasteWater RENAME TO rii_ps_waste_water;
ALTER TABLE RII_PSWaterFilter RENAME TO rii_ps_water_filter;
ALTER TABLE RII_PSWaterHolding RENAME TO rii_ps_water_holding;
ALTER TABLE RII_PSWaterSources RENAME TO rii_ps_water_sources;
ALTER TABLE RII_PublicProcess RENAME TO rii_public_process;
ALTER TABLE RII_UserInfo RENAME TO rii_user_info;
ALTER TABLE RII_UserManufacturers RENAME TO rii_user_manufacturers;


ALTER TABLE rii_competitors CHANGE `CmptID` `cmpt_id` int(11);
ALTER TABLE rii_competitors MODIFY COLUMN `cmpt_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_competitors CHANGE `CmptYear` `cmpt_year` int(4);
ALTER TABLE rii_competitors CHANGE `CmptCompetition` `cmpt_competition` int(11);
ALTER TABLE rii_competitors CHANGE `CmptName` `cmpt_name` varchar(255);


ALTER TABLE rii_light_models CHANGE `LgtModID` `lgt_mod_id` int(11);
ALTER TABLE rii_light_models MODIFY COLUMN `lgt_mod_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_light_models CHANGE `LgtModManuID` `lgt_mod_manu_id` int(11);
ALTER TABLE rii_light_models CHANGE `LgtModName` `lgt_mod_name` varchar(255);
ALTER TABLE rii_light_models CHANGE `LgtModTech` `lgt_mod_tech` varchar(255);


ALTER TABLE rii_manufacturers CHANGE `ManuID` `manu_id` int(11);
ALTER TABLE rii_manufacturers MODIFY COLUMN `manu_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_manufacturers CHANGE `ManuName` `manu_name` varchar(255);
ALTER TABLE rii_manufacturers CHANGE `ManuCntFlower` `manu_cnt_flower` int(11);
ALTER TABLE rii_manufacturers CHANGE `ManuCntVeg` `manu_cnt_veg` int(11);
ALTER TABLE rii_manufacturers CHANGE `ManuCntClone` `manu_cnt_clone` int(11);
ALTER TABLE rii_manufacturers CHANGE `ManuCntMother` `manu_cnt_mother` int(11);
ALTER TABLE rii_manufacturers CHANGE `ManuIDsFlower` `manu_ids_` text;
ALTER TABLE rii_manufacturers CHANGE `ManuIDsVeg` `manu_ids_veg` text;
ALTER TABLE rii_manufacturers CHANGE `ManuIDsClone` `manu_ids_clone` text;
ALTER TABLE rii_manufacturers CHANGE `ManuIDsMother` `manu_ids_mother` text;


ALTER TABLE rii_powerscore CHANGE `PsID` `ps_id` int(11);
ALTER TABLE rii_powerscore MODIFY COLUMN `ps_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_powerscore CHANGE `PsUserID` `ps_user_id` int(11);
ALTER TABLE rii_powerscore CHANGE `PsStatus` `ps_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsTimeType` `ps_time_type` int(11);
ALTER TABLE rii_powerscore CHANGE `PsYear` `ps_year` int(4);
ALTER TABLE rii_powerscore CHANGE `PsPrivacy` `ps_privacy` int(11);
ALTER TABLE rii_powerscore CHANGE `PsZipCode` `ps_zip_code` varchar(10);
ALTER TABLE rii_powerscore CHANGE `PsAshrae` `ps_ashrae` varchar(10);
ALTER TABLE rii_powerscore CHANGE `PsClimateLabel` `ps_climate_label` varchar(12);
ALTER TABLE rii_powerscore CHANGE `PsName` `ps_name` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsEfficOverall` `ps_effic_overall` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficOverSimilar` `ps_effic_over_similar` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficFacility` `ps_effic_facility` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficProduction` `ps_effic_production` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficLighting` `ps_effic_lighting` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficHvac` `ps_effic_hvac` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficWater` `ps_effic_water` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficWaste` `ps_effic_waste` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficLightingMother` `ps_effic_lighting_mother` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficLightingClone` `ps_effic_lighting_clone` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficLightingVeg` `ps_effic_lighting_veg` double;
ALTER TABLE rii_powerscore CHANGE `PsEfficLightingFlower` `ps_effic_lighting_flower` double;
ALTER TABLE rii_powerscore CHANGE `PsHavestsPerYear` `ps_harvests_per_year` int(11);
ALTER TABLE rii_powerscore CHANGE `PsHarvestBatch` `ps_harvest_batch` varchar(1);
ALTER TABLE rii_powerscore CHANGE `PsDaysVegetative` `ps_days_vegetative` int(3);
ALTER TABLE rii_powerscore CHANGE `PsDaysFlowering` `ps_days_flowering` int(3);
ALTER TABLE rii_powerscore CHANGE `PsTotalSize` `ps_total_size` double;
ALTER TABLE rii_powerscore CHANGE `PsTotalCanopySize` `ps_total_canopy_size` double;
ALTER TABLE rii_powerscore CHANGE `PsCntGreenhouse` `ps_cnt_greenhouse` int(3);
ALTER TABLE rii_powerscore CHANGE `PsSizeOutdoor` `ps_size_outdoor` double;
ALTER TABLE rii_powerscore CHANGE `PsSizeIndoor` `ps_size_indoor` double;
ALTER TABLE rii_powerscore CHANGE `PsSizeGreenhouses` `ps_size_greenhouses` double;
ALTER TABLE rii_powerscore CHANGE `PsSizeNonCult` `ps_size_non_cult` double;
ALTER TABLE rii_powerscore CHANGE `PsSourceUtility` `ps_source_utility` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsSourceUtilityOther` `ps_source_utility_other` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsSourceRenew` `ps_source_renew` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsOtherPower` `ps_other_power` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsVersionAB` `ps_version_ab` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsSubmissionProgress` `ps_submission_progress` int(11);
ALTER TABLE rii_powerscore CHANGE `PsIPaddy` `ps_ip_addy` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsTreeVersion` `ps_tree_version` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsUniqueStr` `ps_unique_str` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsIsMobile` `ps_is_mobile` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsCounty` `ps_county` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsState` `ps_state` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsCountry` `ps_country` varchar(100);
ALTER TABLE rii_powerscore CHANGE `PsCntIndoorBlds` `ps_cnt_indoor_blds` int(11);
ALTER TABLE rii_powerscore CHANGE `PsCharacterize` `ps_characterize` int(11);
ALTER TABLE rii_powerscore CHANGE `PsMotherSeed` `ps_mother_seed` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsCloneSeed` `ps_clone_seed` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsHasWaterPump` `ps_has_water_pump` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsCuresIndoor` `ps_cures_indoor` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsCuresOutdoor` `ps_cures_outdoor` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsCuresOffsite` `ps_cures_offsite` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsFlowerRoomExisting` `ps_flower_room_existing` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsFlowerRoomNew` `ps_flower_room_new` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsMotherLoc` `ps_mother_loc` int(11);
ALTER TABLE rii_powerscore CHANGE `PsFeedback1` `ps_feedback1` text;
ALTER TABLE rii_powerscore CHANGE `PsFeedback2` `ps_feedback2` text;
ALTER TABLE rii_powerscore CHANGE `PsFeedback3` `ps_feedback3` text;
ALTER TABLE rii_powerscore CHANGE `PsFeedback4` `ps_feedback4` text;
ALTER TABLE rii_powerscore CHANGE `PsFeedback5` `ps_feedback5` text;
ALTER TABLE rii_powerscore CHANGE `PsFeedback6` `ps_feedback6` text;
ALTER TABLE rii_powerscore CHANGE `PsFeedback7` `ps_feedback7` text;
ALTER TABLE rii_powerscore CHANGE `PsFeedback8` `ps_feedback8` text;
ALTER TABLE rii_powerscore CHANGE `PsKWH` `ps_kwh` double;
ALTER TABLE rii_powerscore CHANGE `PsGrams` `ps_grams` double;
ALTER TABLE rii_powerscore CHANGE `PsGramsAreWetWeight` `ps_grams_are_wet_weight` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsEmail` `ps_email` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsIncentiveUsed` `ps_incentive_used` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsIncentiveWants` `ps_incentive_wants` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsNewsletter` `ps_newsletter` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsProcessingOnsite` `ps_processing_onsite` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsMobileRacking` `ps_mobile_racking` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsExtractingOnsite` `ps_extracting_onsite` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsVerticalStack` `ps_vertical_stack` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsConsiderUpgrade` `ps_consider_upgrade` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsIsIntegrated` `ps_is_integrated` int(1);
ALTER TABLE rii_powerscore CHANGE `PsHvacOther` `ps_hvac_other` text;
ALTER TABLE rii_powerscore CHANGE `PsUploadEnergyBills` `ps_upload_energy_bills` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsEnergyNonFarm` `ps_energy_non_farm` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsEnergyNonFarmPerc` `ps_energy_non_farm_perc` int(3);
ALTER TABLE rii_powerscore CHANGE `PsHeatWater` `ps_heat_water` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsControls` `ps_controls` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsControlsAuto` `ps_controls_auto` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsMemberInterest` `ps_member_interest` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsUniqueness1` `ps_uniqueness1` text;
ALTER TABLE rii_powerscore CHANGE `PsUniqueness2` `ps_uniqueness2` text;
ALTER TABLE rii_powerscore CHANGE `PsUniqueness3` `ps_uniqueness3` text;
ALTER TABLE rii_powerscore CHANGE `PsUniqueness4` `ps_uniqueness4` text;
ALTER TABLE rii_powerscore CHANGE `PsUniqueness5` `ps_uniqueness5` text;
ALTER TABLE rii_powerscore CHANGE `PsUniqueness6` `ps_uniqueness6` text;
ALTER TABLE rii_powerscore CHANGE `PsUniqueness7` `ps_uniqueness7` text;
ALTER TABLE rii_powerscore CHANGE `PsUniqueness8` `ps_uniqueness8` text;
ALTER TABLE rii_powerscore CHANGE `PsNotes` `ps_notes` text;
ALTER TABLE rii_powerscore CHANGE `PsWaterInnovation` `ps_water_innovation` text;
ALTER TABLE rii_powerscore CHANGE `PsGreenWasteLbs` `ps_green_waste_lbs` double;
ALTER TABLE rii_powerscore CHANGE `PsGreenWasteMixed` `ps_green_waste_mixed` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsComplianceWasteTrack` `ps_compliance_waste_track` varchar(255);
ALTER TABLE rii_powerscore CHANGE `PsLightingError` `ps_lighting_error` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsEfficCarbon` `ps_effic_carbon` int(11);
ALTER TABLE rii_powerscore CHANGE `PsEfficFacilityStatus` `ps_effic_facility_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsEfficProductionStatus` `ps_effic_production_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsEfficLightingStatus` `ps_effic_lighting_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsEfficHvacStatus` `ps_effic_hvac_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsEfficCarbonStatus` `ps_effic_carbon_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsEfficWaterStatus` `ps_effic_water_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsEfficWasteStatus` `ps_effic_waste_status` int(11);
ALTER TABLE rii_powerscore CHANGE `PsIsPro` `ps_is_pro` tinyint(1);
ALTER TABLE rii_powerscore CHANGE `PsCrop` `ps_crop` int(11);
ALTER TABLE rii_powerscore CHANGE `PsOnsiteType` `ps_onsite_type` int(11);
ALTER TABLE rii_powerscore CHANGE `PsCropOther` `ps_crop_other` varchar(255);


ALTER TABLE rii_powerscore_basic CHANGE `PsbID` `psb_id` int(11);
ALTER TABLE rii_powerscore_basic MODIFY COLUMN `psb_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_powerscore_basic CHANGE `PsbZipCode` `psb_zip_code` varchar(10);
ALTER TABLE rii_powerscore_basic CHANGE `PsbCounty` `psb_county` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbState` `psb_state` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbKWH` `psb_kwh` double;
ALTER TABLE rii_powerscore_basic CHANGE `PsbLbs` `psb_lbs` double;
ALTER TABLE rii_powerscore_basic CHANGE `PsbEmail` `psb_email` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbNewsletter` `psb_newsletter` tinyint(1);
ALTER TABLE rii_powerscore_basic CHANGE `PsbDumpEnergy` `psb_dump_energy` text;
ALTER TABLE rii_powerscore_basic CHANGE `PsbDumpHarvest` `psb_dump_harvest` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbCloneLight` `psb_clone_light` tinyint(1);
ALTER TABLE rii_powerscore_basic CHANGE `PsbCloneSun` `psb_clone_sun` tinyint(1);
ALTER TABLE rii_powerscore_basic CHANGE `PsbVegLight` `psb_veg_light` tinyint(1);
ALTER TABLE rii_powerscore_basic CHANGE `PsbVegSun` `psb_veg_sun` tinyint(1);
ALTER TABLE rii_powerscore_basic CHANGE `PsbFlowerLight` `psb_flower_light` tinyint(1);
ALTER TABLE rii_powerscore_basic CHANGE `PsbFlowerSun` `psb_flower_sun` tinyint(1);
ALTER TABLE rii_powerscore_basic CHANGE `PsbUserID` `psb_user_id` int(11);
ALTER TABLE rii_powerscore_basic CHANGE `PsbVersionAB` `psb_version_ab` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbSubmissionProgress` `psb_submission_progress` int(11);
ALTER TABLE rii_powerscore_basic CHANGE `PsbIPaddy` `psb_ip_addy` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbTreeVersion` `psb_tree_version` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbUniqueStr` `psb_unique_str` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbIsMobile` `psb_is_mobile` varchar(255);
ALTER TABLE rii_powerscore_basic CHANGE `PsbScore` `psb_score` double;
ALTER TABLE rii_powerscore_basic CHANGE `PsbPercentile` `psb_percentile` double;


ALTER TABLE rii_ps_areas CHANGE `PsAreaID` `ps_area_id` int(11);
ALTER TABLE rii_ps_areas MODIFY COLUMN `ps_area_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_areas CHANGE `PsAreaPSID` `ps_area_psid` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaType` `ps_area_type` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaHasStage` `ps_area_has_stage` tinyint(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaSize` `ps_area_size` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaCanopyArea` `ps_area_canopy_area` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaDaysCycle` `ps_area_days_cycle` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaRoomNew` `ps_area_room_new` tinyint(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaRoomExisting` `ps_area_room_existing` tinyint(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaLgtArtifPerc` `ps_area_lgt_artif_perc` int(3);
ALTER TABLE rii_ps_areas CHANGE `PsAreaLgtArtif` `ps_area_lgt_artif` tinyint(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaLgtSun` `ps_area_lgt_sun` tinyint(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaLgtDep` `ps_area_lgt_dep` tinyint(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaLightDep` `ps_area_light_dep` varchar(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaTotalLightWatts` `ps_area_total_light_watts` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaCalcWatts` `ps_area_calc_watts` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaCalcSize` `ps_area_calc_size` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaLgtPattern` `ps_area_lgt_pattern` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaLgtFixSize1` `ps_area_lgt_fix_size1` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaLgtFixSize2` `ps_area_lgt_fix_size2` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaLightingEffic` `ps_area_lighting_effic` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaVertStack` `ps_area_vert_stack` tinyint(1);
ALTER TABLE rii_ps_areas CHANGE `PsAreaHvacType` `ps_area_hvac_type` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaHvacOther` `ps_area_hvac_other` text;
ALTER TABLE rii_ps_areas CHANGE `PsAreaHvacEffic` `ps_area_hvac_effic` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaGallons` `ps_area_gallons` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaWaterFreq` `ps_area_water_freq` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaWateringMethod` `ps_area_watering_method` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaWaterEffic` `ps_area_water_effic` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaSqFtPerFix1` `ps_area_sq_ft_per_fix1` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaSqFtPerFix2` `ps_area_sq_ft_per_fix2` double;
ALTER TABLE rii_ps_areas CHANGE `PsAreaTemperature` `ps_area_temperature` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaHumidity` `ps_area_humidity` int(11);
ALTER TABLE rii_ps_areas CHANGE `PsAreaCommissioning` `ps_area_commissioning` int(11);


ALTER TABLE rii_ps_areas_blds CHANGE `PsArBldID` `ps_ar_bld_id` int(11);
ALTER TABLE rii_ps_areas_blds MODIFY COLUMN `ps_ar_bld_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_areas_blds CHANGE `PsArBldAreaID` `ps_ar_bld_area_id` int(11);
ALTER TABLE rii_ps_areas_blds CHANGE `PsArBldType` `ps_ar_bld_type` int(11);
ALTER TABLE rii_ps_areas_blds CHANGE `PsArBldTypeOther` `ps_ar_bld_type_other` varchar(255);


ALTER TABLE rii_ps_areas_constr CHANGE `PsArCnsID` `ps_ar_cns_id` int(11);
ALTER TABLE rii_ps_areas_constr MODIFY COLUMN `ps_ar_cns_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_areas_constr CHANGE `PsArCnsBldID` `ps_ar_cns_bld_id` int(11);
ALTER TABLE rii_ps_areas_constr CHANGE `PsArCnsType` `ps_ar_cns_type` int(11);
ALTER TABLE rii_ps_areas_constr CHANGE `PsArCnsTypeOther` `ps_ar_cns_type_other` varchar(255);


ALTER TABLE rii_ps_areas_green CHANGE `PsArGrnID` `ps_ar_grn_id` int(11);
ALTER TABLE rii_ps_areas_green MODIFY COLUMN `ps_ar_grn_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_areas_green CHANGE `PsArGrnAreaID` `ps_ar_grn_area_id` int(11);
ALTER TABLE rii_ps_areas_green CHANGE `PsArGrnType` `ps_ar_grn_type` int(11);


ALTER TABLE rii_ps_communications CHANGE `PsComID` `ps_com_id` int(11);
ALTER TABLE rii_ps_communications MODIFY COLUMN `ps_com_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_communications CHANGE `PsComPSID` `ps_com_psid` int(11);
ALTER TABLE rii_ps_communications CHANGE `PsComUser` `ps_com_user` bigint(20);
ALTER TABLE rii_ps_communications CHANGE `PsComDescription` `ps_com_description` text;


ALTER TABLE rii_ps_farm CHANGE `PsFrmID` `ps_frm_id` int(11);
ALTER TABLE rii_ps_farm MODIFY COLUMN `ps_frm_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_farm CHANGE `PsFrmPSID` `ps_frm_psid` int(11);
ALTER TABLE rii_ps_farm CHANGE `PsFrmType` `ps_frm_type` int(11);


ALTER TABLE rii_ps_feedback CHANGE `PsfID` `psf_id` int(11);
ALTER TABLE rii_ps_feedback MODIFY COLUMN `psf_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_feedback CHANGE `PsfVersionAB` `psf_version_ab` varchar(255);
ALTER TABLE rii_ps_feedback CHANGE `PsfSubmissionProgress` `psf_submission_progress` int(11);
ALTER TABLE rii_ps_feedback CHANGE `PsfIPaddy` `psf_ip_addy` varchar(255);
ALTER TABLE rii_ps_feedback CHANGE `PsfTreeVersion` `psf_tree_version` varchar(255);
ALTER TABLE rii_ps_feedback CHANGE `PsfUniqueStr` `psf_unique_str` varchar(255);
ALTER TABLE rii_ps_feedback CHANGE `PsfUserID` `psf_user_id` bigint(20);
ALTER TABLE rii_ps_feedback CHANGE `PsfIsMobile` `psf_is_mobile` varchar(255);
ALTER TABLE rii_ps_feedback CHANGE `PsfPsID` `psf_psid` varchar(255);
ALTER TABLE rii_ps_feedback CHANGE `PsfFeedback1` `psf_feedback1` text;
ALTER TABLE rii_ps_feedback CHANGE `PsfFeedback2` `psf_feedback2` text;
ALTER TABLE rii_ps_feedback CHANGE `PsfFeedback3` `psf_feedback3` text;
ALTER TABLE rii_ps_feedback CHANGE `PsfFeedback4` `psf_feedback4` varchar(1);
ALTER TABLE rii_ps_feedback CHANGE `PsfFeedback5` `psf_feedback5` text;


ALTER TABLE rii_ps_for_cup CHANGE `PsCupID` `ps_cup_id` int(11);
ALTER TABLE rii_ps_for_cup MODIFY COLUMN `ps_cup_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_for_cup CHANGE `PsCupPSID` `ps_cup_psid` int(11);
ALTER TABLE rii_ps_for_cup CHANGE `PsCupCupID` `ps_cup_cup_id` int(11);


ALTER TABLE rii_ps_greenhouses CHANGE `PsGrnID` `ps_grn_id` int(11);
ALTER TABLE rii_ps_greenhouses MODIFY COLUMN `ps_grn_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_greenhouses CHANGE `PsGrnPSID` `ps_grn_psid` int(11);
ALTER TABLE rii_ps_greenhouses CHANGE `PsGrnSize` `ps_grn_size` double;
ALTER TABLE rii_ps_greenhouses CHANGE `PsGrnLightDep` `ps_grn_light_dep` varchar(1);
ALTER TABLE rii_ps_greenhouses CHANGE `PsGrnAreaType` `ps_grn_area_type` int(11);
ALTER TABLE rii_ps_greenhouses CHANGE `PsGrnAreaTypeOther` `ps_grn_area_type_other` varchar(255);
ALTER TABLE rii_ps_greenhouses CHANGE `PsGrnDaysCycle` `ps_grn_days_cycle` int(11);


ALTER TABLE rii_ps_grow_media CHANGE `PsGrwMedID` `ps_grw_med_id` int(11);
ALTER TABLE rii_ps_grow_media MODIFY COLUMN `ps_grw_med_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_grow_media CHANGE `PsGrwMedPSID` `ps_grw_med_psid` int(11);
ALTER TABLE rii_ps_grow_media CHANGE `PsGrwMedGrowing` `ps_grw_med_growing` int(11);


ALTER TABLE rii_ps_hvac CHANGE `PsHvcID` `ps_hvc_id` int(11);
ALTER TABLE rii_ps_hvac MODIFY COLUMN `ps_hvc_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_hvac CHANGE `PsHvcPSID` `ps_hvc_psid` int(11);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcUnitType` `ps_hvc_unit_type` int(11);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcCount` `ps_hvc_count` int(11);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcSize` `ps_hvc_size` varchar(255);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcEfficiency` `ps_hvc_efficiency` varchar(255);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcMake` `ps_hvc_make` varchar(255);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcModel` `ps_hvc_model` varchar(255);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcHours` `ps_hvc_hours` int(11);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcMonths` `ps_hvc_months` int(11);
ALTER TABLE rii_ps_hvac CHANGE `PsHvcUnitTypeOther` `ps_hvc_unit_type_other` varchar(255);


ALTER TABLE rii_ps_licenses CHANGE `PsLicID` `ps_lic_id` int(11);
ALTER TABLE rii_ps_licenses MODIFY COLUMN `ps_lic_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_licenses CHANGE `PsLicPSID` `ps_lic_psid` int(11);
ALTER TABLE rii_ps_licenses CHANGE `PsLicLicense` `ps_lic_license` int(11);


ALTER TABLE rii_ps_light_types CHANGE `PsLgTypID` `ps_lg_typ_id` int(11);
ALTER TABLE rii_ps_light_types MODIFY COLUMN `ps_lg_typ_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_light_types CHANGE `PsLgTypAreaID` `ps_lg_typ_area_id` int(11);
ALTER TABLE rii_ps_light_types CHANGE `PsLgTypLight` `ps_lg_typ_light` int(11);
ALTER TABLE rii_ps_light_types CHANGE `PsLgTypCount` `ps_lg_typ_count` int(11);
ALTER TABLE rii_ps_light_types CHANGE `PsLgTypWattage` `ps_lg_typ_wattage` double;
ALTER TABLE rii_ps_light_types CHANGE `PsLgTypHours` `ps_lg_typ_hours` double;
ALTER TABLE rii_ps_light_types CHANGE `PsLgTypMake` `ps_lg_typ_make` varchar(255);
ALTER TABLE rii_ps_light_types CHANGE `PsLgTypModel` `ps_lg_typ_model` varchar(255);


ALTER TABLE rii_ps_monthly CHANGE `PsMonthID` `ps_month_id` int(11);
ALTER TABLE rii_ps_monthly MODIFY COLUMN `ps_month_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_monthly CHANGE `PsMonthPSID` `ps_month_psid` int(11);
ALTER TABLE rii_ps_monthly CHANGE `PsMonthMonth` `ps_month_month` int(2);
ALTER TABLE rii_ps_monthly CHANGE `PsMonthKWH1` `ps_month_kwh1` double;
ALTER TABLE rii_ps_monthly CHANGE `PsMonthGrams` `ps_month_grams` double;
ALTER TABLE rii_ps_monthly CHANGE `PsMonthWasteLbs` `ps_month_waste_lbs` double;
ALTER TABLE rii_ps_monthly CHANGE `PsMonthKWH2` `ps_month_kwh2` double;
ALTER TABLE rii_ps_monthly CHANGE `PsMonthNotes` `ps_month_notes` text;
ALTER TABLE rii_ps_monthly CHANGE `PsMonthOrder` `ps_month_order` int(2);


ALTER TABLE rii_ps_other_power CHANGE `PsOthPwrID` `ps_oth_pwr_id` int(11);
ALTER TABLE rii_ps_other_power MODIFY COLUMN `ps_oth_pwr_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_other_power CHANGE `PsOthPwrPSID` `ps_oth_pwr_psid` int(11);
ALTER TABLE rii_ps_other_power CHANGE `PsOthPwrSource` `ps_oth_pwr_source` int(11);


ALTER TABLE rii_ps_owners CHANGE `PsOwnID` `ps_own_id` int(11);
ALTER TABLE rii_ps_owners MODIFY COLUMN `_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_owners CHANGE `PsOwnPartnerUser` `ps_own_partner_user` int(11);
ALTER TABLE rii_ps_owners CHANGE `PsOwnClientUser` `ps_own_client_user` int(11);
ALTER TABLE rii_ps_owners CHANGE `PsOwnType` `ps_own_type` int(11);


ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedID` `ps_pag_feed_id` int(11);
ALTER TABLE rii_ps_page_feedback MODIFY COLUMN `ps_pag_feed_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedPSID` `ps_pag_feed_psid` int(11);
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedNewsletter` `ps_pag_feed_newsletter` tinyint(1);
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedMemberInterest` `ps_pag_feed_member_interest` tinyint(1);
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedIncentiveUsed` `ps_pag_feed_incentive_used` tinyint(1);
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedIncentiveWants` `ps_pag_feed_incentive_wants` tinyint(1);
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedConsiderUpgrade` `ps_pag_feed_consider_upgrade` tinyint(1);
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback1` `ps_pag_feed_feedback1` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback2` `ps_pag_feed_feedback2` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback3` `ps_pag_feed_feedback3` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback4` `ps_pag_feed_feedback4` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback5` `ps_pag_feed_feedback5` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback6` `ps_pag_feed_feedback6` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback7` `ps_pag_feed_feedback7` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback8` `ps_pag_feed_feedback8` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedFeedback9` `ps_pag_feed_feedback9` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness1` `ps_pag_feed_uniqueness1` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness2` `ps_pag_feed_uniqueness2` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness3` `ps_pag_feed_uniqueness3` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness4` `ps_pag_feed_uniqueness4` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness5` `ps_pag_feed_uniqueness5` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness6` `ps_pag_feed_uniqueness6` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness7` `ps_pag_feed_uniqueness7` text;
ALTER TABLE rii_ps_page_feedback CHANGE `PsPagFeedUniqueness8` `ps_pag_feed_uniqueness8` text;


ALTER TABLE rii_ps_rankings CHANGE `PsRnkID` `ps_rnk_id` int(11);
ALTER TABLE rii_ps_rankings MODIFY COLUMN `ps_rnk_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkPSID` `ps_rnk_psid` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkFilters` `ps_rnk_filters` varchar(255);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkTotCnt` `ps_rnk_tot_cnt` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkOverall` `ps_rnk_overall` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkOverallAvg` `ps_rnk_overall_avg` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkFacility` `ps_rnk_facility` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkProduction` `ps_rnk_production` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkHVAC` `ps_rnk_hvac` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkLighting` `ps_rnk_lighting` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkWater` `ps_rnk_water` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkWaste` `ps_rnk_waste` double;
ALTER TABLE rii_ps_rankings CHANGE `PsRnkFacilityCnt` `ps_rnk_facility_cnt` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkProductionCnt` `ps_rnk_production_cnt` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkHVACCnt` `ps_rnk_hvac_cnt` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkLightingCnt` `ps_rnk_lighting_cnt` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkWaterCnt` `ps_rnk_water_cnt` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkWasteCnt` `ps_rnk_waste_cnt` int(11);
ALTER TABLE rii_ps_rankings CHANGE `PsRnkManuID` `ps_rnk_manu_id` int(11);


ALTER TABLE rii_ps_ranks CHANGE `PsRnkID` `ps_rnk_id` int(11);
ALTER TABLE rii_ps_ranks MODIFY COLUMN `ps_rnk_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkFilters` `ps_rnk_filters` varchar(255);
ALTER TABLE rii_ps_ranks CHANGE `PsRnkTotCnt` `ps_rnk_tot_cnt` int(11);
ALTER TABLE rii_ps_ranks CHANGE `PsRnkOverallAvg` `ps_rnk_overall_avg` text;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkFacility` `ps_rnk_facility` text;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkProduction` `ps_rnk_production` text;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkHVAC` `ps_rnk_hvac` text;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkLighting` `ps_rnk_lighting` text;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkWater` `ps_rnk_water` text;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkWaste` `ps_rnk_waste` text;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkAvgSqftKwh` `ps_rnk_avg_sqft_kwh` double;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkAvgSqftGrm` `ps_rnk_avg_sqft_grm` double;
ALTER TABLE rii_ps_ranks CHANGE `PsRnkManuID` `ps_rnk_manu_id` int(11);


ALTER TABLE rii_ps_referral CHANGE `PsRefID` `ps_ref_id` int(11);
ALTER TABLE rii_ps_referral MODIFY COLUMN `ps_ref_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_referral CHANGE `PsRefVersionAB` `ps_ref_version_ab` varchar(255);
ALTER TABLE rii_ps_referral CHANGE `PsRefSubmissionProgress` `ps_ref_submission_progress` int(11);
ALTER TABLE rii_ps_referral CHANGE `PsRefIPaddy` `ps_ref_ip_addy` varchar(255);
ALTER TABLE rii_ps_referral CHANGE `PsRefTreeVersion` `ps_ref_tree_version` varchar(255);
ALTER TABLE rii_ps_referral CHANGE `PsRefUniqueStr` `ps_ref_unique_str` varchar(255);
ALTER TABLE rii_ps_referral CHANGE `PsRefUserID` `ps_ref_user_id` bigint(20);
ALTER TABLE rii_ps_referral CHANGE `PsRefIsMobile` `ps_ref_is_mobile` varchar(255);
ALTER TABLE rii_ps_referral CHANGE `PsRefPowerScore` `ps_ref_powerscore` int(11);
ALTER TABLE rii_ps_referral CHANGE `PsRefUtility` `ps_ref_utility` int(11);
ALTER TABLE rii_ps_referral CHANGE `PsRefAddress` `ps_ref_address` varchar(255);
ALTER TABLE rii_ps_referral CHANGE `PsRefEmail` `ps_ref_email` varchar(255);


ALTER TABLE rii_ps_renewables CHANGE `PsRnwID` `ps_rnw_id` int(11);
ALTER TABLE rii_ps_renewables MODIFY COLUMN `ps_rnw_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_renewables CHANGE `PsRnwPSID` `ps_rnw_psid` int(11);
ALTER TABLE rii_ps_renewables CHANGE `PsRnwRenewable` `ps_rnw_renewable` int(11);
ALTER TABLE rii_ps_renewables CHANGE `PsRnwLoadPercent` `ps_rnw_load_percent` int(3);
ALTER TABLE rii_ps_renewables CHANGE `PsRnwKWH` `ps_rnw_kwh` double;


ALTER TABLE rii_ps_utili_links CHANGE `PsUtLnkID` `ps_ut_lnk_id` int(11);
ALTER TABLE rii_ps_utili_links MODIFY COLUMN `ps_ut_lnk_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_utili_links CHANGE `PsUtLnkPSID` `ps_ut_lnk_psid` int(11);
ALTER TABLE rii_ps_utili_links CHANGE `PsUtLnkUtilityID` `ps_ut_lnk_utility_id` int(11);


ALTER TABLE rii_ps_utili_zips CHANGE `PsUtZpID` `ps_ut_zp_id` int(11);
ALTER TABLE rii_ps_utili_zips MODIFY COLUMN `ps_ut_zp_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_utili_zips CHANGE `PsUtZpZipCode` `ps_ut_zp_zip_code` int(11);
ALTER TABLE rii_ps_utili_zips CHANGE `PsUtZpUtilID` `ps_ut_zp_util_id` int(11);


ALTER TABLE rii_ps_utilities CHANGE `PsUtID` `ps_ut_id` int(11);
ALTER TABLE rii_ps_utilities MODIFY COLUMN `ps_ut_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_utilities CHANGE `PsUtName` `ps_ut_name` varchar(255);
ALTER TABLE rii_ps_utilities CHANGE `PsUtType` `ps_ut_type` int(11);
ALTER TABLE rii_ps_utilities CHANGE `PsUtEmail` `ps_ut_email` varchar(255);


ALTER TABLE rii_ps_waste_ag CHANGE `PSWstAgID` `ps_wst_ag_id` int(11);
ALTER TABLE rii_ps_waste_ag MODIFY COLUMN `ps_wst_ag_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_waste_ag CHANGE `PSWstAgPSID` `ps_wst_ag_psid` int(11);
ALTER TABLE rii_ps_waste_ag CHANGE `PSWstAgMethod` `ps_wst_ag_method` int(11);


ALTER TABLE rii_ps_waste_green CHANGE `PSWstGrnID` `ps_wst_grn_id` int(11);
ALTER TABLE rii_ps_waste_green MODIFY COLUMN `ps_wst_grn_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_waste_green CHANGE `PSWstGrnPSID` `ps_wst_grn_psid` int(11);
ALTER TABLE rii_ps_waste_green CHANGE `PSWstGrnMethod` `ps_wst_grn_method` int(11);


ALTER TABLE rii_ps_waste_process CHANGE `PsWstPrcsID` `ps_wst_prcs_id` int(11);
ALTER TABLE rii_ps_waste_process MODIFY COLUMN `ps_wst_prcs_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_waste_process CHANGE `PSWstPrcsPSID` `ps_wst_prcs_psid` int(11);
ALTER TABLE rii_ps_waste_process CHANGE `PSWstPrcsMethod` `ps_wst_prcs_method` int(11);


ALTER TABLE rii_ps_waste_supplies CHANGE `PSWstSupID` `ps_wst_sup_id` int(11);
ALTER TABLE rii_ps_waste_supplies MODIFY COLUMN `ps_wst_sup_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_waste_supplies CHANGE `PSWstSupPSID` `ps_wst_sup_psid` int(11);
ALTER TABLE rii_ps_waste_supplies CHANGE `PSWstSupMethod` `ps_wst_sup_method` int(11);


ALTER TABLE rii_ps_waste_water CHANGE `PsWstWtrID` `ps_wst_wtr_id` int(11);
ALTER TABLE rii_ps_waste_water MODIFY COLUMN `ps_wst_wtr_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_waste_water CHANGE `PSWstWtrPSID` `ps_wst_wtr_psid` int(11);
ALTER TABLE rii_ps_waste_water CHANGE `PSWstWtrMethod` `ps_wst_wtr_method` int(11);


ALTER TABLE rii_ps_water_filter CHANGE `PsWtrFltID` `ps_wtr_flt_id` int(11);
ALTER TABLE rii_ps_water_filter MODIFY COLUMN `ps_wtr_flt_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_water_filter CHANGE `PsWtrFltPSID` `ps_wtr_flt_psid` int(11);
ALTER TABLE rii_ps_water_filter CHANGE `PsWtrFltFilter` `ps_wtr_flt_filter` int(11);


ALTER TABLE rii_ps_water_holding CHANGE `PsWtrHldID` `ps_wtr_hld_id` int(11);
ALTER TABLE rii_ps_water_holding MODIFY COLUMN `ps_wtr_hld_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_water_holding CHANGE `PsWtrHldPSID` `ps_wtr_hld_psid` int(11);
ALTER TABLE rii_ps_water_holding CHANGE `PsWtrHldHolding` `ps_wtr_hld_holding` int(11);


ALTER TABLE rii_ps_water_sources CHANGE `PsWtrSrcID` `ps_wtr_src_id` int(11);
ALTER TABLE rii_ps_water_sources MODIFY COLUMN `ps_wtr_src_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_ps_water_sources CHANGE `PsWtrSrcPSID` `ps_wtr_src_psid` int(11);
ALTER TABLE rii_ps_water_sources CHANGE `PsWtrSrcSource` `ps_wtr_src_source` int(11);


ALTER TABLE rii_public_process CHANGE `PubPrcID` `pub_prc_id` int(11);
ALTER TABLE rii_public_process MODIFY COLUMN `pub_prc_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_public_process CHANGE `PubPrcLikeProducers` `pub_prc_like_producers` text;
ALTER TABLE rii_public_process CHANGE `PubPrcLikeProducersVisual` `pub_prc_like_producers_visual` text;
ALTER TABLE rii_public_process CHANGE `PubPrcRafflePrizes` `pub_prc_raffle_prizes` int(11);
ALTER TABLE rii_public_process CHANGE `PubPrcPrizeOther` `pub_prc_prize_other` text;
ALTER TABLE rii_public_process CHANGE `PubPrcGrowerOtherValue` `pub_prc_grower_other_value` text;
ALTER TABLE rii_public_process CHANGE `PubPrcFeedback1` `pub_prc_feedback1` text;
ALTER TABLE rii_public_process CHANGE `PubPrcUserID` `pub_prc_user_id` bigint(20);
ALTER TABLE rii_public_process CHANGE `PubPrcSubmissionProgress` `pub_prc_submission_progress` int(11);
ALTER TABLE rii_public_process CHANGE `PubPrcTreeVersion` `pub_prc_tree_version` varchar(255);
ALTER TABLE rii_public_process CHANGE `PubPrcVersionAB` `pub_prc_version_ab` varchar(255);
ALTER TABLE rii_public_process CHANGE `PubPrcUniqueStr` `pub_prc_unique_str` varchar(255);
ALTER TABLE rii_public_process CHANGE `PubPrcIPaddy` `pub_prc_ip_addy` varchar(255);
ALTER TABLE rii_public_process CHANGE `PubPrcIsMobile` `pub_prc_is_mobile` varchar(255);


ALTER TABLE rii_user_info CHANGE `UsrID` `usr_id` int(11);
ALTER TABLE rii_user_info MODIFY COLUMN `usr_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_user_info CHANGE `UsrUserID` `usr_user_id` bigint(20);
ALTER TABLE rii_user_info CHANGE `UsrCompanyName` `usr_company_name` varchar(255);


ALTER TABLE rii_user_manufacturers CHANGE `UsrManID` `usr_man_id` int(11);
ALTER TABLE rii_user_manufacturers MODIFY COLUMN `usr_man_id` int(10) AUTO_INCREMENT;
ALTER TABLE rii_user_manufacturers CHANGE `UsrManUserID` `usr_man_user_id` bigint(20);
ALTER TABLE rii_user_manufacturers CHANGE `UsrManManuID` `usr_man_manu_id` int(11);





CREATE INDEX rii_ps_areas_blds_ps_ar_bld_area_idx ON rii_ps_areas_blds(ps_ar_bld_area_id);
CREATE INDEX rii_ps_areas_constr_ps_ar_cns_bld_idx ON rii_ps_areas_constr(ps_ar_cns_bld_id);
CREATE INDEX rii_ps_areas_green_ps_ar_grn_area_idx ON rii_ps_areas_green(ps_ar_grn_area_id);
CREATE INDEX rii_ps_communications_ps_com_psidx ON rii_ps_communications(ps_com_psid);
CREATE INDEX rii_ps_farm_ps_frm_psidx ON rii_ps_farm(ps_frm_psid);
CREATE INDEX rii_ps_for_cup_ps_cup_psidx ON rii_ps_for_cup(ps_cup_psid);
CREATE INDEX rii_ps_greenhouses_ps_grn_psidx ON rii_ps_greenhouses(ps_grn_psid);
CREATE INDEX rii_ps_grow_media_ps_grw_med_psidx ON rii_ps_grow_media(ps_grw_med_psid);
CREATE INDEX rii_ps_hvac_ps_hvc_psidx ON rii_ps_hvac(ps_hvc_psid);
CREATE INDEX rii_ps_licenses_ps_lic_psidx ON rii_ps_licenses(ps_lic_psid);
CREATE INDEX rii_ps_light_types_ps_lg_typ_area_idx ON rii_ps_light_types(ps_lg_typ_area_id);
CREATE INDEX rii_ps_monthly_ps_month_psidx ON rii_ps_monthly(ps_month_psid);
CREATE INDEX rii_ps_other_power_ps_oth_pwr_psidx ON rii_ps_other_power(ps_oth_pwr_psid);
CREATE INDEX rii_ps_page_feedback_ps_pag_feed_psidx ON rii_ps_page_feedback(ps_pag_feed_psid);
CREATE INDEX rii_ps_renewables_ps_rnw_psidx ON rii_ps_renewables(ps_rnw_psid);
CREATE INDEX rii_ps_utili_links_ps_ut_lnk_psidx ON rii_ps_utili_links(ps_ut_lnk_psid);
CREATE INDEX rii_ps_waste_ag_ps_wst_ag_psidx ON rii_ps_waste_ag(ps_wst_ag_psid);
CREATE INDEX rii_ps_waste_green_ps_wst_grn_psidx ON rii_ps_waste_green(ps_wst_grn_psid);
CREATE INDEX rii_ps_waste_process_ps_wst_prcs_psidx ON rii_ps_waste_process(ps_wst_prcs_psid);
CREATE INDEX rii_ps_waste_supplies_ps_wst_sup_psidx ON rii_ps_waste_supplies(ps_wst_sup_psid);
CREATE INDEX rii_ps_waste_water_ps_wst_wtr_psidx ON rii_ps_waste_water(ps_wst_wtr_psid);
CREATE INDEX rii_ps_water_filter_ps_wtr_flt_psidx ON rii_ps_water_filter(ps_wtr_flt_psid);
CREATE INDEX rii_ps_water_holding_ps_wtr_hld_psidx ON rii_ps_water_holding(ps_wtr_hld_psid);
CREATE INDEX rii_ps_water_sources_ps_wtr_src_psidx ON rii_ps_water_sources(ps_wtr_src_psid);

CREATE INDEX rii_ps_utili_zips_ps_ut_zp_zip_codex ON rii_ps_utili_zips(ps_ut_zp_zip_code);

CREATE INDEX rii_user_info_usr_user_idx ON rii_user_info(usr_user_id);
CREATE INDEX rii_user_manufacturers_usr_man_user_idx ON rii_user_manufacturers(usr_man_user_id);







