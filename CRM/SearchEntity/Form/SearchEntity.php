<?php
class CRM_SearchEntity_Form_SearchEntity extends CRM_Core_Form {
  public function ajaxcall(){
    $params = $_POST;
    //switch case for api
    if(!$params['is_active']) {
      $params['is_active'] = 0;
    }
    switch ($params['entity_type']) {
      case "1":
        $apiTable = 'event';
        $api = 1;
        $entityType = 'Event';
        $desc ='description';
        $tableTitle ='title';
        break;
      case "2":
        $apiTable = 'contribution_page';
        $api = 1;
        $entityType = 'Contribution';
        $desc = 'intro_text';
        $tableTitle = 'title';
        break;
      case "3":
        $apiTable = 'civicrm_membership_block';
        $api = 0;
        $entityType ='Membership';
        $desc = 'new_text ';
        $tableTitle ='new_title';
        break;
      default:
        $apiTable = 'event' ;
        $api = 1;
        $entityType = 'Event';
    }
    if ($params['entity_des'] && $params['entity_name']) {
      $titleCon = $tableTitle. " Like '%".mysql_real_escape_string($params['entity_name'])."%'";
      $descCon = " OR ".$desc." Like '%".mysql_real_escape_string($params['entity_des'])."%' and ";
    }
    else if (!$params['entity_des'] && $params['entity_name']) {
      $titleCon = $tableTitle." Like '%".mysql_real_escape_string($params['entity_name'])."%' and ";
      $descCon = '';
    }
    else if ($params['entity_des'] && !$params['entity_name']) {
      $descCon = $desc." Like '%".mysql_real_escape_string($params['entity_des'])."%' and ";
      $titleCon = " ";
    }
    else {
      $descCon = " ";
      $titleCon = " ";
    }
    if ($api == 1) {
      $query = "select id,title from civicrm_".$apiTable." where  is_active=".$params['is_active'];
      $result = CRM_Core_DAO::executeQuery( $query);
      while ( $result->fetch( ) ) {
        $enityId = $result->id;
        if ($result->title) {
          $option[$result->id] = $result->title;
        }
      }
    }
    else {
      $query = "select cp.title,mb.new_title,cp.id from civicrm_contribution_page cp,".$apiTable." mb  where cp.id=mb.entity_id and  mb.is_active=".$params['is_active'];
      $result = CRM_Core_DAO::executeQuery( $query);
      while ( $result->fetch( ) ) {
        $enityId = $result->id;
        if ($result->title) {
          $option[$result->id] = $result->title;
        }
      }
    }
    echo json_encode($option);
    CRM_Utils_System::civiExit();
  }

  public function buildQuickForm() {
    $this->addElement('text','entity_name',ts('Title:'),array('onkeyup'=>'getname(this.value);','onChange'=>'getname(this.value);'));
    $this->addElement('text','source_name',ts('Name:'));
    $this->addElement('text','test_link');
    $this->addElement('text','id');
    $this->addElement('text','entityId');
    $this->addElement('textarea','entity_des',ts('Description'));
    $entityTypes = array('1'=>'Event','2'=>'Contribution','3'=>'Membership');
    $this->add('select', 'entity_type', ts('Entity'), array(
        '' => ts('- select -')) + $entityTypes,
      FALSE, array('onchange' => "enityidget(this.value);")
    );
    $entityResult = $this->postProcess();

    if (count($entityResult) > 0) {
      $entityResult = $entityResult;
      $this->add('select',
        'event_id',
        ts('Entity Pages'),
        array(
          '' => ts('- select -'))+$entityResult,
        FALSE,
        array('onChange' => "getlinkenity(this.value );")
      );
    }
    else {
      $this->add('select',
        'event_id',
        ts('Entity Pages'),
        array(
          '' => ts('- select -')),
        FALSE,
        array('onChange' => "getlinkenity(this.value );")
      );
    }
    $this->addElement('checkbox', 'is_active', ts('Is active?') );
    $buttons = array(
      array(
        'type' => 'submit',
        'name' => ts('Save'),
        'subName' => 'view',
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => ts('cancel'),
        'subName' => 'cancel',
        'isDefault' => TRUE,
      ),
    );
    $this->addButtons($buttons);
  }

  function setDefaultValues() {
    if (isset($_GET['id'])) {
      $id = $_GET['id'];
      $query = "select name,description,entity_id from `civicrm_entity_search` where `id`=".$id ;
      $result = CRM_Core_DAO::executeQuery( $query);
      $idtxt = & $this->getElement('id');
      $idtxt->setValue($id);
      while ( $result->fetch( ) ) {
        $entityName = & $this->getElement('entity_name');
        $entityName->setValue($result->name);
        $desc = & $this->getElement('entity_des');
        $desc->setValue($result->description);
        $desc = & $this->getElement('entity_des');
        $desc->setValue($result->description);
        $entityId = & $this->getElement('entityId');
        $entityId->setValue($result->entity_id );
      }
    }
  }

  public function postProcess() {
    $session = CRM_Core_Session::singleton();
    $params = $this->controller->exportValues($this->_name);
    if (count($params) > 0 ) {
      //switch case for api
      switch ($params['entity_type']) {
        case "1":
          $apiTable = 'civicrm_event';
          $entityType = 'Event';
          break;
        case "2":
          $apiTable = 'civicrm_contribution_page';
          $entityType = 'Contribution';
          break;
        case "3":
          $apiTable = 'civicrm_contribution_page';
          $entityType = 'Membership';
          break;
        default:
          $apiTable = 'event' ;
          $entityType = 'Event';
      }
      //get page title
      $pageTitlequery = "SELECT title  FROM ".$apiTable." where id=".$params['entityId'];
      $pageTitle = CRM_Core_DAO::singleValueQuery( $pageTitlequery, CRM_Core_DAO::$_nullArray );
      if (isset($params['id'])) {
        $id = $params['id'];
        $query = "Insert into `civicrm_entity_search`(name,description,entity_type,entity_id,test_link,search_date,entity_page)Values('".$params['entity_name']."','".$params['entity_des']."','". $entityType."',".$params['entityId'].",'".$params['test_link']."','".date('Y-m-d')."','".$pageTitle."')";
      }
      else {
        $query = "Update `civicrm_entity_search` SET name='".$params['entity_name']."',description='".$params['entity_des']."',entity_type='". $entityType."',entity_id=".$params['entityId'].",test_link='".$params['test_link']."',search_date='".date('Y-m-d')."',entity_page='".$pageTitle."' where id=".$id;
      }
      $result = CRM_Core_DAO::executeQuery($query);
      if ($result) {
        $session->replaceUserContext(CRM_Utils_System::redirect('search'));
      }
    }
  }
}
