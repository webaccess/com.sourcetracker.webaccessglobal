<?php
class CRM_SearchEntity_Form_Delete extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->addElement('text','id');
    if ($this->elementExists('id')) {
      $entityId = & $this->getElement('id');
      if (isset($_GET['id'])) {
        $entityId->setValue($_GET['id']);
      }
    }
    $buttons = array(
      array(
        'type' => 'submit',
        'name' => ts('Delete'),
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
  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);
    $session = CRM_Core_Session::singleton();
    if (isset($params['id'])) {
      $id = $params['id'];
      $query = "Delete from civicrm_entity_search where id=".$id;
      $result = CRM_Core_DAO::executeQuery( $query);
      $host = $_SERVER['HTTP_HOST'];
      $path = explode('/',$_SERVER['REQUEST_URI']);
      $baseuti = 'http://'.$host.'/'.$path[1].'/civicrm/sourcetracker/search';
      if ($result) {
        $session->replaceUserContext(CRM_Utils_System::redirect($baseuti));
      }
    }
  }
}

