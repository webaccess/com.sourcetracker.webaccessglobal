<?php

require_once 'com.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function com_civicrm_config(&$config) {
  _com_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function com_civicrm_xmlMenu(&$files) {
  _com_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function com_civicrm_install() {
  return _com_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function com_civicrm_uninstall() {
  return _com_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function com_civicrm_enable() {
  return _com_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function com_civicrm_disable() {
  return _com_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function com_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _com_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function com_civicrm_managed(&$entities) {
  return _com_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function com_civicrm_caseTypes(&$caseTypes) {
  _com_civix_civicrm_caseTypes($caseTypes);
}
function com_civicrm_postProcess( $formName, &$form ){
  $params = $form->exportValues();
  $session = &CRM_Core_Session::singleton();
  $sessionId = $session->get('id');
  $sessionSource = $session->get('source');
  if ($formName == 'CRM_Contribute_Form_Contribution_Confirm' || $formName == 'CRM_Event_Form_Registration_Confirm') {
    //check membership is there with contribution
    $queryContri = "Select entity_id  from `civicrm_membership_block` where  entity_id=".$sessionId;
    $resultContri = CRM_Core_DAO::singleValueQuery( $queryContri, CRM_Core_DAO::$_nullArray );
    if (!empty($resultContri)) {
      $queryMemid = "Select max(id) from `civicrm_membership` ";
      $resultMemid = CRM_Core_DAO::singleValueQuery( $queryMemid, CRM_Core_DAO::$_nullArray );
      $queryMembership = "Update `civicrm_membership` SET source='".$session_source."' where id=".$resultMemid;
      $resultMembership = CRM_Core_DAO::executeQuery( $queryMembership);
    }
    $query = "Select max(id) from `civicrm_contribution` ";
    $result = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
    $query1 = "Update `civicrm_contribution` SET source='".$sessionSource."' where id=".$result;
    $result1 = CRM_Core_DAO::executeQuery( $query1);

    if ($formName == 'CRM_Event_Form_Registration_Confirm') {
      $query1 = "Select max(id) from `civicrm_participant` ";
      $resultParticipant = CRM_Core_DAO::singleValueQuery( $query1, CRM_Core_DAO::$_nullArray );
      $query = "Update `civicrm_participant` SET source='".$sessionSource."' where id=".$resultParticipant;
      $result = CRM_Core_DAO::executeQuery( $query);
    }
    if ($result1 || $result) {
      $session->resetScope('source');
      $session->resetScope('id');
    }
  }
}

function com_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Event_Form_Registration_Register' || $formName == 'CRM_Contribute_Form_Contribution_Main') {
    $session = &CRM_Core_Session::singleton();
    $sessionId = $session->get('id');
    $sessionSource = $session->get('source');
    if (isset($_GET['source'])){
      $source = $_GET['source'];
    }
    if (isset($_GET['id'])) {
      $enid = $_GET['id'];
    }
    if (empty($sessionSource)) {
      $sourceName = $session->set('source',$source);
    }
    if (empty($sessionId)) {
      $entityId = $session->set('id',$enid);
    }
    $source = $sessionSource;
    $entity = $sessionId;
  }
}
function com_civicrm_navigationMenu( &$params ) {
  //  Get the maximum key of $params
  $maxKey = ( max( array_keys($params) ) );
  foreach($params as $key => $param){
    if ($param['attributes']['label']=='Administer') {
      $maxchildKey = (max(array_keys($param['child'])));
      $adminKey = $key;
		}
  }
  $params[$adminKey]['child'][$maxchildKey+1] = array (
    'attributes' => array (
      'label'      => 'Sourcetracker',
      'name'       => 'Sourcetracker',
      'url'        => '',
      'permission' => '',
      'operator'   => null,
      'separator'  => 1,
      'parentID'   => $adminKey,
      'navID'      => $maxchildKey+1,
      'active'     => 1
    ),
    'child' =>  array (
      '1' => array (
        'attributes' => array (
          'label'      => 'Listing',
          'name'       => 'Listing',
          'url'        => 'civicrm/sourcetracker/search',
          'permission' => '',
          'operator'   => null,
          'separator'  => 1,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ),
        'child' => null
      ),
      '2' => array (
        'attributes' => array (
          'label'      => 'New Source',
          'name'       => 'New Source',
          'url'        => 'civicrm/sourcetracker/newsource',
          'permission' => '',
          'operator'   => null,
          'separator'  => 1,
          'parentID'   => $maxKey+1,
          'navID'      => 1,
          'active'     => 1
        ),
        'child' => null
      )
    )

  );
}
