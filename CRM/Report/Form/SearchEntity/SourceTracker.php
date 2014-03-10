<?php

/*
  +--------------------------------------------------------------------+
  | CiviCRM version 4.4                                                |
  +--------------------------------------------------------------------+
  | Copyright CiviCRM LLC (c) 2004-2013                                |
  +--------------------------------------------------------------------+
  | This file is a part of CiviCRM.                                    |
  |                                                                    |
  | CiviCRM is free software; you can copy, modify, and distribute it  |
  | under the terms of the GNU Affero General Public License           |
  | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
  |                                                                    |
  | CiviCRM is distributed in the hope that it will be useful, but     |
  | WITHOUT ANY WARRANTY; without even the implied warranty of         |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
  | See the GNU Affero General Public License for more details.        |
  |                                                                    |
  | You should have received a copy of the GNU Affero General Public   |
  | License and the CiviCRM Licensing Exception along                  |
  | with this program; if not, contact CiviCRM LLC                     |
  | at info[AT]civicrm[DOT]org. If you have questions about the        |
  | GNU Affero General Public License or the licensing of CiviCRM,     |
  | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
  +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */
class CRM_Report_Form_SearchEntity_SourceTracker extends CRM_Report_Form_Event {
  public $_submited_val;
  protected $_charts = array(
    '' => 'Tabular',
    'barChart' => 'Bar Chart',
    'pieChart' => 'Pie Chart',
  );
  function __construct() {
    $entityTableArray = $this->entityname();
    $entityName = $entityTableArray['entityNameArray'];
    if ($entityName == 'Event') {
      $defaultColumnsEvent = array(
        'event_title' => array('title' => ts('Event Title'),'default' => TRUE,
        ),);
    }
    else {
      $defaultColumnsEvent = array(
        'event_title' => array('title' => ts('Event Title')
        ),);
    }
    if ($entityName == 'Contribution') {
      $defaultColumnsContri = array(
        'title' => array('title' => ts('Contribution Title'),'default' => TRUE,
        ),);
    }
    else {
      $defaultColumnsContri = array(
        'title' => array('title' => ts('Contribution Title')
        ),);
    }
    if ($entityName == 'Membership') {
      $defaultColumnsMembership = array(
        'new_title' => array('title' => ts('Membership Title'),'default' => TRUE,
        ),);
    }
    else {
      $defaultColumnsMembership = array(
        'new_title' => array('title' => ts('Membership Title')
        ),);
    }
    $this->_columns = array(
      'civicrm_contribution_page'=>
      array(
        'dao' => 'CRM_Contribute_DAO_ContributionPage',
        'fields' =>
        $defaultColumnsContri,
      ),
      'civicrm_event'=>
      array(
        'dao' => 'CRM_Event_DAO_Event',
        'fields' =>
        $defaultColumnsEvent,
      ),
      'civicrm_membership_block'=>
      array(
        'dao' => 'CRM_Member_DAO_MembershipBlock',
        'fields' =>
        $defaultColumnsMembership,
      ),
      'civicrm_contribution'=>
      array(
        'dao' => 'CRM_Contribute_DAO_Contribution',
      ),
      'civicrm_participant'=>
      array(
        'dao' => 'CRM_Event_DAO_Participant',
      ),
      'civicrm_membership'=>
      array(
        'dao' => 'CRM_Member_DAO_Membership',
      ),
    );
    parent::__construct();
  }

  function buildQuickForm() {
    parent::buildQuickForm();
    $this->filterColumnsAdd();
  }

  function filterColumnsAdd() {
    $entityArray = array('Event'=>'Event','Contribution'=>'Contribution','Membership'=>'Membership');
    $elements = array();
    $elements[] = &$this->createElement('select', 'entity_type',NULL,$entityArray);
    $elements[] = &$this->createElement('text', 'source_name', NULL);
    $this->addGroup($elements, 'filter');
    $this->_filterDetailExtra = array('entity_type' => array('title' => ts('Entity Type'),'name' => 'entity_type'),
                                'source_name'=>array('title'=>ts('Source'),'name'=>'entity_name',)
    );
    $this->assign('filterDetailExtra', $this->_filterDetailExtra);
  }

  function select() {
    $entityTableArray = $this->entityname();
    $entityName = $entityTableArray['entityNameArray'];
    $select = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (!empty($entityName)) {
            if (CRM_Utils_Array::value('required', $field) ||
              CRM_Utils_Array::value($fieldName, $this->_params['fields'])
            ) {
              $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
            }
          }
        }
      }
    }
    if (!empty($select)) {
      $this->_select = 'SELECT ' . implode(', ', $select);
    }
  }

  function from() {
    $entityTableArray = $this->entityname();
    $innerQuery = '';
    $entityName = $entityTableArray['entityNameArray'];
    switch ($entityName) {
      case "Event":
        $innerQuery.= "`civicrm_participant` {$this->_aliases['civicrm_participant']} INNER JOIN `civicrm_event` {$this->_aliases['civicrm_event']} ON {$this->_aliases['civicrm_event']}.id ={$this->_aliases['civicrm_participant']}.event_id";
        break;
      case "Contribution":
        $innerQuery.= "`civicrm_contribution` {$this->_aliases['civicrm_contribution']} INNER JOIN `civicrm_contribution_page` {$this->_aliases['civicrm_contribution_page']} ON {$this->_aliases['civicrm_contribution_page']}.id={$this->_aliases['civicrm_contribution']}.contribution_page_id";
        break;
      case "Membership":
        $innerQuery.= "`civicrm_membership_block` {$this->_aliases['civicrm_membership_block']} INNER JOIN  `civicrm_contribution` {$this->_aliases['civicrm_contribution']} ON {$this->_aliases['civicrm_contribution']}.contribution_page_id ={$this->_aliases['civicrm_membership_block']}.entity_id INNER JOIN `civicrm_membership_payment` ON civicrm_membership_payment.contribution_id={$this->_aliases['civicrm_contribution']}.id";
        break;
      default:
        $innerQuery = "";
    }
    if(!empty($innerQuery)){
      $this->_from = " FROM ".$innerQuery;
    }
    else{
      $this->_from = '';
    }
  }

  function where() {
    $clauses = array();
    $entityTableArray = $this->entityname();
    $entityName = $entityTableArray['entityNameArray'];
    $sourceName = $entityTableArray['sourceName'];
    $this->_where = '';
    switch ($entityName) {
      case "Event":
        $this->_where .= "Where {$this->_aliases['civicrm_participant']}.source='".$sourceName."' ";
        break;
      case "Contribution":
        $this->_where .= "Where {$this->_aliases['civicrm_contribution']}.source='".$sourceName."' ";
        break;
      case "Membership":
        $this->_where .= "Where {$this->_aliases['civicrm_contribution']}.source='".$sourceName."' ";
        break;
      default:
        $this->_where.= '';
    }
  }

  //build header for table
  function buildColumnHeaders() {
    $entityTableArray=$this->entityname();
    $entityName=$entityTableArray['entityNameArray'];
    $this->_columnHeaders = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if(!empty($entityName)){
            if (CRM_Utils_Array::value('required', $field) ||
              CRM_Utils_Array::value($fieldName, $this->_params['fields'])
            ) {
              $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
              $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = CRM_Utils_Array::value('title', $field);
            }
          }
        }
      }
    }
  }

  function postProcess() {
    //get submited value
    $params = $this->exportValues();
    $this->_submited_val=$params;
    $this->beginPostProcess();
    $this->buildColumnHeaders();
    $entityTablearray=$this->entityname();
    $entityName=$entityTablearray['entityNameArray'];
    if(!empty($entityName)){
      $sql = $this->buildQuery(TRUE);
      $dao = CRM_Core_DAO::executeQuery($sql);
      $this->setPager();
      $rows = $graphRows = array();
      $count = 0;
      while ($dao->fetch()) {
        $row = array();
        foreach ($this->_columnHeaders as $key => $value) {
          if (isset($dao->$key)) {
            $row[$key] = $dao->$key;
          }
        }
        $rows[] = $row;
      }
      // do not call pager here
      $this->doTemplateAssignment($rows);
      $this->endPostProcess($rows);
    }
  }

  public function entityname(){
    if(!empty($this->_submited_val) && array_key_exists('fields',$this->_submited_val)){
      $entityTableArray = $this->_submited_val['fields'];
      $sourceName = $this->_submited_val['filter']['source_name'];
    }
    else {
      if (isset($_GET['entity_type'])) {
        $entityNameGet = $_GET['entity_type'];
        switch ($entityNameGet){
          case "Event":
            $entityTableArray = array('event_title'=>1);
            break;
          case "Contribution":
            $entityTableArray = array('title'=>1);
            break;
          case "Membership":
            $entityTableArray = array('new_title'=>1);
            break;
          default:
            $entityTableArray = '';
        }
      }
      if (isset($_GET['source'])) {
        $sourceName = $_GET['source'];
      }
    }
    if (isset($entityTableArray) && array_key_exists('title',$entityTableArray)) {
      $entityNameArray = 'Contribution';
    }
    if (isset($entityTableArray) && array_key_exists('event_title',$entityTableArray)) {
      $entityNameArray = 'Event';
    }
    if (isset($entityTableArray) && array_key_exists('new_title',$entityTableArray)) {
      $entityNameArray = 'Membership';
    }
    if (isset($entityNameArray)) {
      return array('entityNameArray' => $entityNameArray,'sourceName' => $sourceName);
    }
  }
}

