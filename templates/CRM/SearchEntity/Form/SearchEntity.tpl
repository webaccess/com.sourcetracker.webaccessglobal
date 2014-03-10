{literal}
<style>
.form_label {
    display: inline-block;
    padding: 7px;
    width: 11%;
}
.form_element {
    display: inline-block;
}
input[name=test_link],input[name=entityId],input[name=id] {
    display:none;
}
</style>
{/literal}

<div class="crm-form-block">
<div class="form_text">
<div class="form_label">{$form.entity_name.label}</div>
<div class="form_element">{$form.entity_name.html}</div>
  {$form.test_link.html}
  {$form.entityId.html}
  {$form.id.html}
</div>
<div class="form_text">
  <div class="form_label">{$form.source_name.label}</div>
  <div class="form_element">{$form.source_name.html}</div>
</div>
<div class="form_text">
  <div class="form_label">{$form.entity_des.label}:</div>
  <div class="form_element" style="width:40%">{$form.entity_des.html}</div>
</div>
<div class="form_text">
  <div class="form_label">Active : </div>
  <div class="form_element">{$form.is_active.html}</div>
</div>  
<div class="form_text">
  <div class="form_label">{$form.entity_type.label}:</div>
  <div class="form_element">{$form.entity_type.html}</div>
</div>
<div class="form_text">
  <div class='no_entity'></div>
  <div class="form_label">{$form.event_id.label}:</div>
  <div class="form_element">{$form.event_id.html}</div>
</div>
<div class="form_text">
  <div class="source form_label " style="display: none;">Source URL:</div>
  <div class="test-link form_element "></div> 
  <div class="live-link form_element"></div>
</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
     </div>
</div>

{literal}
<script type="text/javascript">
  cj(document).ready(function(){
  cj('#is_active').attr('checked',true);      
 });
function getname(params)
{
  var entityname= cj('input[name="entity_name"]').val();
  entityname = entityname.toLowerCase().replace(/ /g, '-');
  cj('input[name="source_name"]').val(entityname);
}
function enityidget(entity_type) {
  funax();
}
function funax(){
  var url1 = location.protocol + "//" + document.domain + "/"+ location.pathname.split('/')[1] + "/";
  var postData = cj("#SearchEntity").serializeArray();
  var url=window.location.pathname;
  cj.ajax({
	    url :url1+"civicrm/sourcetracker/ajaxcall",
	    type: "POST",
	    data : postData,
	    dataType: 'json',
	    success: function(data, textStatus, jqXHR) {
                 if(data){
                          cj('#event_id').html('<option>-select-</option>');
		          cj.each(data, function(key, value) {
                              cj('#event_id').append('<option value="'+key+'">'+value+'</option>');
                           });
                          cj('.no_entity').html(' ');
                          cj('.source').hide();
                          cj('.live-link').html('');
                           }
                 else {
                          cj('.no_entity').html('No Record Found');
                          cj('#event_id').html('<option>-select-</option>');
                          cj('.source').hide();
                          cj('.live-link').html('');
                       }
		},
	   error: function (jqXHR, textStatus, errorThrown) {
		    alert('There was some error removing.Please try Again');
		}
	});
}

function getlinkenity(entityid) {
  var entityType = cj( "#entity_type option:selected" ).text();
  var entityId = cj( "#event_id option:selected" ).val();
  var sourceName = cj('#source_name').val();
  cj('#entityId').val(entityid);
  cj('.source').show();
  var url = window.location.pathname;
  var url1 = url.split("/");
if (entityType == 'Event') {
  var databaseUrl = 'http://'+window.location.hostname+'/'+url1[1]+'/civicrm/event/register?id='+entityid+'&reset=1&source='+sourceName;
}
else {
  var databaseUrl = 'http://'+window.location.hostname+'/'+url1[1]+'/civicrm/contribute/transact?reset=1&id='+entityid+'&source='+sourceName;
}
  cj('.live-link').html(databaseUrl);
  cj('#test_link').val(databaseUrl);
}
</script>
{/literal}

    

