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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'CiviTest/CiviSeleniumTestCase.php';
class WebTest_SourceTracker_SourceTrackerTest extends CiviSeleniumTestCase {
  protected function setUp() {
    parent::setUp();
  }

  function testEventsource() {
    // open browser, login
    $this->webtestLogin();
    $sourcename ='newspaper'.substr(sha1(rand()), 0, 7);
    $firstName = 'Ma' . substr(sha1(rand()), 0, 4);
    $lastName = 'An' . substr(sha1(rand()), 0, 7);
    $this->openCiviPage("sourcetracker/newsource", "reset=1");
    $sourcename ='newspaper'.substr(sha1(rand()), 0, 7);
    $this->type('entity_name', $sourcename);
    $this->select('entity_type','value=1');
    sleep(3);
    $this->select('event_id',"label=regexp:Rain-forest Cup Youth Soccer Tournament");
    $this->openCiviPage('event/register','reset=1&id=3&source='.$sourcename,'_qf_Register_upload-bottom');

    //Credit Card Info
    $this->select("credit_card_type", "value=Visa");
    $this->type('credit_card_number', '4111111111111111');
    $this->type('first_name','fhgjfhgkjf');
    $this->type('cvv2', '000');
    $this->select('credit_card_exp_date[M]', 'value=1');
    $this->select('credit_card_exp_date[Y]', 'value=2020');

    //Billing Info
    $this->type('billing_first_name', $firstName);
    $this->type('billing_last_name', $lastName);
    $this->type('billing_street_address-5', '15 Main St.');
    $this->type('billing_city-5', 'San Jose');
    $this->select('billing_country_id-5', 'value=1228');
    $this->select('billing_state_province_id-5', 'value=1004');
    $this->type('billing_postal_code-5', '94129');
    $this->click("_qf_Register_upload-bottom");
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->click("_qf_Confirm_next-top");
    $this->waitForPageToLoad($this->getTimeoutMsec());

    //Report Source tracker
    $this->openCiviPage('sourcetracker/report/sourcetracker');
    $this->type('filter_source_name', $sourcename);
    $this->select('filter_entity_type', 'Event');
    $this->click('fields_event_title');
    $this->click('_qf_Event_submit');
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->_Contributionsource($sourcename,$firstName,$lastName);
  }
  function _Contributionsource($sourcename,$firstName,$lastName) {
    $this->openCiviPage("sourcetracker/newsource", "reset=1");
    $sourcename ='newspaper'.substr(sha1(rand()), 0, 7);
    $this->type('entity_name', $sourcename);
    $this->select('entity_type','value=2');
    sleep(3);
    $this->select('event_id',"label=regexp:Help Support CiviCRM!");
    $this->openCiviPage('contribute/transact','reset=1&id=1&source='.$sourcename,'_qf_Main_upload-bottom');

    //Credit Card Info
    $this->select("credit_card_type", "value=Visa");
    $this->type('credit_card_number', '4111111111111111');
    $this->type('first_name','fhgjfhgkjf');
    $this->type('cvv2', '000');
    $this->select('credit_card_exp_date[M]', 'value=1');
    $this->select('credit_card_exp_date[Y]', 'value=2020');

    //Billing Info
    $this->type('billing_first_name', $firstName);
    $this->type('billing_last_name', $lastName);
    $this->type('billing_street_address-5', '15 Main St.');
    $this->type('billing_city-5', 'San Jose');
    $this->select('billing_country_id-5', 'value=1228');
    $this->select('billing_state_province_id-5', 'value=1004');
    $this->type('billing_postal_code-5', '94129');
    $this->click("_qf_Main_upload-bottom");
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->click("_qf_Confirm_next-top");
    $this->waitForPageToLoad($this->getTimeoutMsec());

    //Report Source tracker
    $this->openCiviPage('sourcetracker/report/sourcetracker');
    $this->type('filter_source_name', $sourcename);
    $this->select('filter_entity_type', 'Contribution');
    $this->click('fields_title');
    $this->click('_qf_Event_submit');
    $this->waitForPageToLoad($this->getTimeoutMsec());
    // $this->waitForElementPresent("report-layout");
    $this->_Membershipsource($sourcename,$firstName,$lastName);
  }
  function _Membershipsource($sourcename,$firstName,$lastName) {
    $this->openCiviPage("sourcetracker/newsource", "reset=1");
    $sourcename ='newspaper'.substr(sha1(rand()), 0, 7);
    $this->type('entity_name', $sourcename);
    $this->select('entity_type','value=3');
    sleep(3);
    $this->select('event_id',"label=regexp:Membership Levels");
    $this->click('_qf_SearchEntity_submit_view-top');
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->openCiviPage('contribute/transact','reset=1&id=1&source='.$sourcename,'_qf_Main_upload-bottom');

    //Credit Card Info
    $this->select("credit_card_type", "value=Visa");
    $this->type('credit_card_number', '4111111111111111');
    $this->type('first_name','fhgjfhgkjf');
    $this->type('cvv2', '000');
    $this->select('credit_card_exp_date[M]', 'value=1');
    $this->select('credit_card_exp_date[Y]', 'value=2020');

    //Billing Info
    $this->type('billing_first_name', $firstName);
    $this->type('billing_last_name', $lastName);
    $this->type('billing_street_address-5', '15 Main St.');
    $this->type('billing_city-5', 'San Jose');
    $this->select('billing_country_id-5', 'value=1228');
    $this->select('billing_state_province_id-5', 'value=1004');
    $this->type('billing_postal_code-5', '94129');
    $this->click("_qf_Main_upload-bottom");
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->click("_qf_Confirm_next-top");
    $this->waitForPageToLoad($this->getTimeoutMsec());

    //Report Source tracker
    $this->openCiviPage('sourcetracker/report/sourcetracker');
    $this->type('filter_source_name', $sourcename);
    $this->select('filter_entity_type', 'Membership');
    $this->click('fields_new_title');
    $this->click('_qf_Event_submit');
    $this->waitForPageToLoad($this->getTimeoutMsec());
  }
}

