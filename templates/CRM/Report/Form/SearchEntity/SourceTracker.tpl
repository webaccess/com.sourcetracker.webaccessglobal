{*
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
*}
{include file="CRM/Report/Form.tpl"}
 <table class="criteria-group">
  {foreach from=$filterDetailExtra key=field item=fieldDetail}
      <tr class="crm-report crm-report-criteria-field crm-report-criteria-field-{$field}">
          <td width="25%">{$fieldDetail.title}</td><td> {$form.filter.$field.html}</td>
      </tr>
   {/foreach}          
  </table>

{* reposition the above block after #someOtherBlock *}
{literal}
<script type="text/javascript">
cj(document).ready(function(){
 cj('.criteria-group').insertAfter('.civireport-criteria');
 cj('#filter_entity_type').attr('size', '3');
 cj('.criteria-group').insertAfter('.civireport-criteria');
  var boxes = cj("input:checkbox").click(function() {
      boxes.not(this).attr('checked', false);
      });
cj( "select" ).change(function () {
   var foo = [];
   cj('#filter_entity_type :selected').each(function(i, selected){
     foo[i] = cj(selected).val();
     var selectedval=cj(selected).val();
  if(cj.inArray('Event', foo) !== -1){
     cj('#fields_event_title').attr('checked',true);
     cj('#fields_title').attr('checked',false);
     cj('#fields_new_title').attr('checked',false);
   }
  if(cj.inArray('Contribution', foo) !== -1) {
    cj('#fields_title').attr('checked',true);
    cj('#fields_event_title').attr('checked',false);
    cj('#fields_new_title').attr('checked',false);
  }
  if(cj.inArray('Membership', foo) !== -1) {
    cj('#fields_new_title').attr('checked',true);
    cj('#fields_title').attr('checked',false);
    cj('#fields_event_title').attr('checked',false);
  }
 });
cj('#filter_selectbox').val(foo);
  });
 });
 </script>
{/literal}
