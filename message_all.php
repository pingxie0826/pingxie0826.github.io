<?php  
	error_reporting(0);
	define('IN_PHP','ok');

	$curp = isset($_GET['pno'])?$_GET['pno']:'1';
	//echo '$curp='.$curp."<br>";

	include_once('class/mysql.class.php');
	include_once('class/page.class.php');
$host='localhost';
	$root='root';
	$pass='root';
	$dbname='wu';
	$dbObj=new db_mysql($host,$root,$pass,$dbname);

//查询总的记录个数
$sql = "select count(*) as n from message {$wheres}";
$totalArr = $dbObj->getone($sql);
$sum=$totalArr['n'];
//echo $sum;
$pageObj = new Page($totalArr['n'],'5');

$sql2="select * from message order by s_id desc limit ".$pageObj->limit();
$nAll = $dbObj->getall($sql2);
//echo "<pre>";
//print_r($nAll);
//echo "</pre>";
///exit();

?>
<script src='js/jquery-1.12.4.js'></script>
<script>
<!--
//==================================================
/***
	单删
	*/
	function delone(s_id,page)
	{
		//alert('单删');
		if(confirm('学生信息删除要慎重!确定要删除吗'))
		{
			location.href="message_del.php?s_id="+s_id+"&pid="+page;
		}
	}
//==================================================
-->
</script>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="lib/html5shiv.js"></script>
<script type="text/javascript" src="lib/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="lib/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="static/h-ui.admin/css/style.css" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
<title>学生管理</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 学生管理 <span class="c-gray en">&gt;</span> 学生管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="cl pd-5 bg-1 bk-gray"> <span class="l"> <a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a class="btn btn-primary radius" href="javascript:;" onclick="admin_role_add('添加学生','message_add.html','800')"><i class="Hui-iconfont">&#xe600;</i> 添加学生</a> </span> <span class="r">共有数据：<strong><?=$sum?></strong> 条</span> </div>
	<table class="table table-border table-bordered table-hover table-bg">
		<thead>
			<tr>
				<th scope="col" colspan="12">学生管理</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type='checkbox' onclick="selall(this);"></th>
					<th width="50">ID</th>	
					<th width="50">学生</th>
					<th width="50">班主任</th>
					<th>头像</th>
					<th>年龄</th>
					<th>性别</th>
				<th>生日</th>
				<th>电话</th>
				<th>家庭住址</th>
				<th>成绩好坏</th>
				<th width="100">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php
				for($i=0;$i<count($nAll);$i++)
				{
			?>
<!------------------------------------------------------>
<!------------------------------------------------------>
				<tr class="text-c">
				<td>
				<input type='checkbox' class='sels' name='selid' value="<?=$nAll[$i]['s_id'] ?>">
				</td>
				<td><?php echo $nAll[$i]['s_id']; ?></td>
				<td><?php echo $nAll[$i]['s_name']; ?></td>
				<td><?php echo $nAll[$i]['s_name2']; ?></td>
				<td class='img'><?php 
							$str='<img src="'.$nAll[$i]['s_path'].'" height="40" style="border-radius:50%;">';
							echo $str;
				?></td>
			
				<td><?php echo $nAll[$i]['s_age']; ?></td>
				<td><?php echo $nAll[$i]['s_sex']; ?></td>
				<td><?php echo $nAll[$i]['s_time']; ?></td>
				<td><?php echo $nAll[$i]['s_tel']; ?></td>
				<td><?php echo $nAll[$i]['s_add']; ?></td>
				<td><?php echo $nAll[$i]['s_level']; ?></td>
				<td class="f-14">
					<a href='message_upd.php?s_id=<?php echo $nAll[$i]['s_id']; ?>&page=<?php echo $curp; ?>' title="更新" style="text-decoration:none;">更新<i class="Hui-iconfont">&#xe6df;</i></a>&nbsp;&nbsp;
						
					<a href="javascript:;" onclick="delone('<?php echo $nAll[$i]['s_id']; ?>' ,'<?php echo $curp; ?>');" style="text-decoration:none;" title="删除">
				 <i class="fa fa-trash"></i>删除
				</tr>
				<?php
				}
				?>
<!------------------------------------------------------>
			<tr>
				<td colspan='12' style="text-align:center;">
					<?php
						echo $pageObj->pageBar(5);
					?>&nbsp;
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="static/h-ui/js/H-ui.min.js"></script> 
<script type="text/javascript" src="static/h-ui.admin/js/H-ui.admin.js"></script> <!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript">
/*管理员-角色-添加*/
function admin_role_add(title,url,w,h){
	layer_show(title,url,w,h);
}

</script>
</body>
</html>