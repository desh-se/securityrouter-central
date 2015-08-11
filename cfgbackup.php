<?php
require_once('inc.php');
if (isset($_GET['download'])) {
	$q = db()->prepare('SELECT config FROM cfgbackup WHERE id=:id');
	if (!$q->execute(array(':id' => $_GET['download'])))
		die('SELECT failed');
	$r = $q->fetch();
	header('Content-type: text/plain');
	echo $r['config'];
	die();
}
require_once('header.php') ?>
<div class="container">
	<ol class="breadcrumb">
		<li><a href="index.php">Routers</a></li>
		<li class="active"><?php p($_GET['serial'])?></li>
		<li class="active">Backups</li>
	</ol>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Date</th>
				<th>Revision</th>
				<th>Author</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
<?php
$q = db()->prepare('SELECT id,author,revision,timestamp FROM cfgbackup WHERE serial=:serial ORDER BY id DESC LIMIT 100');
if (!$q->execute(array(':serial' => $_GET['serial'])))
	die('q1 failed');
while ($r = $q->fetch()) {
?>
			<tr>
				<td><?php p($r['timestamp']) ?></a></td>
				<td><?php p($r['revision']) ?></a></td>
				<td><?php p($r['author']) ?></a></td>
				<td>
					<a href="cfgbackup.php?download=<?php p($r['id']) ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-download" aria-hidden="true"></span></a>
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table> 
</div>
