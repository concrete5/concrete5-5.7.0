<?php defined('C5_EXECUTE') or die("Access Denied.");

$form = Loader::helper('form');
$ek = PermissionKey::getByHandle('edit_user_properties');
$ik = PermissionKey::getByHandle('activate_user');
$dk = PermissionKey::getByHandle('delete_user');

?>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (user) {%>
<tr>
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-user-id="<%-user.uID%>" data-user-name="<%-user.uName%>" data-user-email="<%-user.uEmail%>" data-search-checkbox="individual" value="<%-user.uID%>" /></span></td>
	<% for (i = 0; i < user.columns.length; i++) {
		var column = user.columns[i];
		%>
		<td><%= column.value %></td>
	<% } %>
</tr>
<% }); %>
</script>


<div data-search-element="wrapper"></div>

<div data-search-element="results">

	<table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
		<thead>
		</thead>
		<tbody>
		</tbody>
	</table>

	<div class="ccm-search-results-pagination"></div>

</div>

<script type="text/template" data-template="search-results-pagination">
	<%=paginationTemplate%>
</script>

<script type="text/template" data-template="search-results-table-head">
	<tr>
		<th><span class="ccm-search-results-checkbox">

		<select data-bulk-action="users" disabled class="ccm-search-bulk-action form-control">
			<option value=""><?php echo t('Items Selected')?></option>
			<?php if ($ek->validate()) { ?>
				<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Edit Properties')?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/properties')?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Edit Properties')?></option>
			<?php } ?>
			<?php /*
					<?php if ($ik->validate()) { ?>
						<option value="activate"><?=t('Activate')?></option>
						<option value="deactivate"><?=t('Deactivate')?></option>
					<?php } ?>
					<option value="group_add"><?=t('Add to Group')?></option>
					<option value="group_remove"><?=t('Remove from Group')?></option>
					<?php if ($dk->validate()) { ?>
					<option value="delete"><?=t('Delete')?></option>
					<?php } ?>
		 */ ?>
			<?php if (isset($mode) && $mode == 'choose_multiple') { ?>
				<option value="choose"><?php echo t('Choose')?></option>
			<?php } ?>
		</select>
			<input type="checkbox" data-search-checkbox="select-all" class="ccm-flat-checkbox" />
				</span>

		</th>
		<%
		for (i = 0; i < columns.length; i++) {
		var column = columns[i];
		if (column.isColumnSortable) { %>
		<th class="<%= column.className %>"><a href="<%=column.sortURL%>"><%- column.title %></a></th>
		<% } else { %>
		<th><span><%- column.title %></span></th>
		<% } %>
		<% } %>
	</tr>
</script>

