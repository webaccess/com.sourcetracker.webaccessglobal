SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


--
-- Database: `civicrm_site_ex`
--

-- --------------------------------------------------------

SELECT @domainID := id FROM civicrm_domain where name = 'Default Domain Name';
SELECT @max_wt  :=  IF ( value <> 'NULL',max(weight), 0 ) from civicrm_option_value where option_group_id=41;
INSERT INTO  `civicrm_report_instance` (`domain_id`, `title`, `report_id`, `permission`)
SELECT 1,'Sourcetracker Report','sourcetracker/sourcetracker','administer CiviCRM'
  FROM `civicrm_report_instance`
 WHERE NOT EXISTS (SELECT 1 
                     FROM  `civicrm_report_instance`
                    WHERE title ='Sourcetracker Report'
                      AND report_id = 'sourcetracker/sourcetracker') Limit 1;
INSERT INTO  `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `is_optgroup`, `is_reserved`, `is_active`,`component_id`)
SELECT '41', 'Sourcetracker Report','sourcetracker/sourcetracker', 'CRM_Report_Form_SearchEntity_SourceTracker', NULL, 0,  0, @max_wt + 1, 0, 0, 1,3
  FROM `civicrm_option_value`
 WHERE NOT EXISTS (SELECT 1 
                     FROM  `civicrm_option_value`
                    WHERE name ='CRM_Report_Form_SearchEntity_SourceTracker'
                      AND value = 'sourcetracker/sourcetracker') Limit 1;



--
-- Table structure for table `civicrm_entity_search`
--
	
CREATE TABLE IF NOT EXISTS `civicrm_entity_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255),
  `entity_type` varchar(32),
  `entity_id` int,
  `test_link` varchar(255),
  `search_date` date NOT NULL,
  `entity_page` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------
