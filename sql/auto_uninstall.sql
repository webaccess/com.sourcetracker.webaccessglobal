--Delete table
DROP TABLE IF EXISTS `civicrm_entity_search`;
--Delete Row 
DELETE FROM `civicrm_option_value`
  WHERE `name`='CRM_Report_Form_SearchEntity_SourceTracker'; 
  DELETE FROM `civicrm_report_instance`
  WHERE `report_id`='sourcetracker/sourcetracker';
