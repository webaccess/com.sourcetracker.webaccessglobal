<?php
class CRM_SearchEntity_Page_View_SearchEntity extends CRM_Core_Page {
  function preProcess() {
    $host = $_SERVER['HTTP_HOST'];
    $path = explode('/',$_SERVER['REQUEST_URI']);
    $baseuti = 'http://'.$host.'/'.$path[1].'/civicrm/sourcetracker/search';
    //get id of the report
    $sql1 = "SELECT id FROM civicrm_report_instance where title='Sourcetracker Report'";
    $reportId = CRM_Core_DAO::singleValueQuery( $sql1, CRM_Core_DAO::$_nullArray );
    //get all past search
    $query = "select entity_type,id,description,name,test_link from civicrm_entity_search ";
    $result = CRM_Core_DAO::executeQuery( $query);
    $output = '';
    $output.= "<table id='options' class='display'> <thead>
               <tr><th>Name</th><th>Description</th><th>Source</th><th>Action</th></tr></thead>";
    $i=0;
    while ( $result->fetch( )) {
      $i++;
      //source name
      $source=str_replace(" ","-",strtolower($result->name));
      $output .= "<tr>";
      $output .= "<td>".$result->name."</td>";
      $output .= "<td>".$result->description."</td>";
      $output .= "<td>".$result->test_link."</td>";
      $output .= "<td><a href='newsource/?action=update&id=".$result->id."'>Edit</a>&nbsp;&nbsp;<a href='/".$path[1]."/civicrm/report/instance/".$reportId."?reset=1&entity_type=".$result->entity_type."&source=".$source."' class='reportlink report".$i."'>Report</a>&nbsp;&nbsp;<a href='".$baseuti."/delete?action=delete&id=".$result->id."'>Delete</a></td>";
      $output .= "</tr>";
    }
    $output .= "</table>";
    $this->assign('output', $output);
  }
  function run() {
    $this->preProcess();
    return parent::run();
  }
}

