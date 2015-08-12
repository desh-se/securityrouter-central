<?php
require_once('inc.php');

if (isset($_POST['edit'])) {
	$q = db()->prepare('UPDATE units SET serial=:serial,name=:name,url=:url,username=:username WHERE id=:id');
	if (!$q->execute(array(
		':id' => $_POST['id'],
		':serial' => $_POST['serial'],
		':name' => $_POST['name'],
		':url' => $_POST['url'],
		':username' => $_POST['username'],
	)))
		die('UPDATE failed');
	if ($_POST['password'] !== '') {
		$q = db()->prepare('UPDATE units SET password=:password WHERE id=:id');
		if (!$q->execute(array(
			':id' => $_POST['id'],
			':password' => $_POST['password'],
		)))
			die('UPDATE password failed');
	}
	header('Location: ?');
	die();
}

require_once('header.php') ?>
<div class="container">
	<ol class="breadcrumb">
		<li class="active">Routers</li>
	</ol>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Serial</th>
				<th>Seen</th>
				<th>Uptime</th>
				<th>Version</th>
				<th>Subscription</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
<?php
$q = db()->prepare('SELECT id,serial,name,url,username,strftime("%s","now")-strftime("%s",seen) as seen FROM units');
if (!$q->execute())
	die('SELECT failed');
while ($r = $q->fetch()) {
	$color = '';
	if ($r['seen'] > 15) $color = 'warning';
	if ($r['seen'] > 900) $color = 'danger';
?>
			<tr class="unit <?php p($color)?>" data-id="<?php p($r['id']) ?>">
				<td><a href="<?php p(str_replace('/remote','', $r['url'])) ?>"><?php p($r['name']) ?></a></td>
				<td class="serial"><?php p($r['serial']) ?></td>
				<td><?php p(intval($r['seen']/60)) ?> minutes ago</td>
				<td class="uptime"></td>
				<td class="version"></td>
				<td class="sub"></td>
				<td>
					<a href="cfgbackup.php?serial=<?php p($r['serial'])?>" class="btn btn-default btn-xs" title="Download configuration"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>
					<a href="#" data-id="<?php p($r['id']) ?>" data-serial="<?php p($r['serial']) ?>" data-name="<?php p($r['name']) ?>" data-url="<?php p($r['url']) ?>" data-username="<?php p($r['username']) ?>" data-toggle="modal" data-target="#edit_unit" class="btn btn-default btn-xs" title="Edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table> 
</div>
<div class="modal fade" id="edit_unit"><div class="modal-dialog"><div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Edit</h4>
	</div>
	<form class="form-horizontal" method="post">
		<input type="hidden" id="edit_id" name="id">
		<div class="modal-body">
			<div class="form-group">
				<label class="col-sm-4 control-label">Serial</label>
				<div class="col-sm-8"><input type="text" class="form-control" name="serial" id="edit_serial"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Name</label>
				<div class="col-sm-8"><input type="text" class="form-control" name="name" id="edit_name"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">URL</label>
				<div class="col-sm-8"><input type="text" class="form-control" name="url" id="edit_url"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Username</label>
				<div class="col-sm-8"><input type="text" class="form-control" name="username" id="edit_username"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Password</label>
				<div class="col-sm-8"><input type="password" class="form-control" name="password"></div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" name="edit" class="btn btn-primary">Save</button>
		</div>
	</form>
</div></div></div>

<script>
$("#edit_unit").on("show.bs.modal", function (event) {
	$("#edit_id").val($(event.relatedTarget).data("id"));
	$("#edit_serial").val($(event.relatedTarget).data("serial"));
	$("#edit_name").val($(event.relatedTarget).data("name"));
	$("#edit_url").val($(event.relatedTarget).data("url"));
	$("#edit_username").val($(event.relatedTarget).data("username"));
});
$(".unit").each(function() {
	var serial = $(this).find(".serial").text();
	var id = $(this).data("id");
	$.ajax({
		target: this,
		url: "ajax.php",
		data: {
			type: "subscription",
			serial: serial,
		},
		dataType: "json",
		success: function(result) {
			if (!result.operation || result.operation == "Expired")
				$(this.target).addClass("warning").find(".sub").html("<span class='glyphicon glyphicon-exclamation-sign'></span> Expired");
			else
				$(this.target).find(".sub").text(result.operation);
		}
	});
	$.ajax({
		target: this,
		url: "ajax.php",
		data: {
			type: "versionuptime",
			id: id,
		},
		dataType: "json",
		success: function(result) {
			$(this.target).find(".version").text(result.version);
			$(this.target).find(".uptime").text(result.uptime);
		}
	});
});
</script>
