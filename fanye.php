<?php
	require_once('E:/www/web/kaoshi/configs/config.php');
	require_once('pdo_conn.php');
	$sql = "select * from test_ly";
	$stmt = $pdo->query($sql);
	$rows = $stmt->rowCount();
	$cols = $stmt->columnCount();
	if($rows<1)
	{
		echo "暂无记录<br>";
		exit();
	}
	$size = 5;
	$page = isset($_GET['page'])?$_GET['page']:1;
	//$page=1;
	$maxPage = ceil($rows/$size);
	if($page<1 || $page=="" || $page==null)
	{
		$page = 1;
	}
	if($page>$maxPage)
	{
		$page = $maxPage;
	}
	$sql .= " order by t_id desc ";
	$sql .= " limit ".(($page-1)*$size).",".$size;
	//echo $sql;
	$stmt2 = $pdo->prepare($sql);
	$stmt2->execute();
	$row = $stmt2->fetchAll(PDO::FETCH_ASSOC);
	//print_r($row);
	$tr = array('bgcolor="aquamarine"','bgcolor="yellow"','bgcolor="pink"');
	$smarty->assign('row',$row);
	$smarty->assign('cols',$cols);
	$smarty->assign('page',$page);
	$smarty->assign('maxPage',$maxPage);
	$smarty->assign('tr',$tr);
	$smarty->display('fanye.html');
?>