<?php
	define("JA_DWQS", "jiuaidwqs");  //调用includes下的文件
	header("content-type:text/html;charset=utf-8");
	// 三个查询参数
	if(count(split('&', $_SERVER['QUERY_STRING'])) === 1)
	{
		// cn:类别名   ca:类别别名  cd:类别id
		if(isset($_GET['cn']))
		{
			if(!empty($_GET['cn']))
			{
				// 创建类别中文和别名、类别id数组，用于参数判断
				$catName = array('久艾推荐','前端开发','HTML-CSS','JavaScript','JQuery','后台开发','PHP','Ruby','Python',
					'数据库','MySQL','NoSQL','移动开发','Android','IOS','编程语言','C-Cpp','Java','业界分享','开发平台','Linux-Vim','Git',);
				
				$catId = array('1314','910','9101','9102','9103','520','5201','5202','5203',
					'920','9201','9202','1128','11281','11282','401','4011','4012','708','1025','10251','10252');

				// 获取的类中中文和别名
				$getCatName = $_GET['cn'];
				if(in_array($getCatName, $catName))
				{
					//showcat.php/showtag.php/search.php/page.php共用这个模板
					define("CATAGORY", "cat");    //引入类别页的css文件,
					include "../includes/constant.inc.php";  
					include ROOT_PATH."/includes/header.inc.php";  
					include ROOT_PATH."/includes/JADB.class.php"; 
					global $jadb;   //全局变量
					$jadb = new JADB(JA_DB_HOST,JA_DB_USER,JA_DB_PWD,JA_DB_NAME);
					if(!$jadb->isConnectError())
					{
						echo $jadb->getErrorInfo();
					}
					include ROOT_PATH.'/includes/func.php';

					$getCatId = $catId[array_search($getCatName, $catName)];
					if(strcmp($getCatId, "1314")  === 0)   //1314对应久艾推荐
					{
						// 查询全部
						//对于这种查询，按照索引或者主键降序 提高速度
						$catSql = "".TW;
						$catResults = $jadb->getQuery($catSql);
					}
					else if(in_array($getCatId, $catId))
					{
						// 查询对应类别下的
						$catSql = "".TW;
						$catResults = $jadb->getQuery($catSql);
					}
					else
					{
						header("location:../404");
					}
				}
				else
				{
					// echo "参数内容不对";
					header("location:../404");
				}
			}
			else
			{
				 //echo "参数内容是空";
				header("location:../404");
			}
		}
		else
		{
			 //echo "参数变量名不对";
			header("location:../404");
		}
	}
	else
	{
		//echo "参数个数不对";
		header("location:../404");
	}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
	<title><?php echo $getCatName; ?>-久艾分享-优质互联网资源共享</title>
</head>
<body>
	<?php 
		include ROOT_PATH."/header.php"; 
		include ROOT_PATH."/nav.php"; 
	?>
	<div class="cat">
		<div class="catcontent">
			<header class="STXingkai catheader">
				<span><img src="../../images/cat.png"></span>
				<span>分类：<?php echo $getCatName; ?></span>
			</header>
			<div class="catarticles">
				<!-- 结果集不为空 -->
				<?php if($catResults): ?>
					<?php 
						while($catRow = $jadb->getRowsAssoc($catResults)):
					?>
							<div class="articlelist">
								<header class="articletitle">
									<a href="<?php echo JA_URL.'/articles/'.stripslashes($catRow['article_uniqid']); ?>" title="<?php echo stripslashes($catRow['article_title']); ?>" target="_blank">
										<?php echo stripslashes($catRow['article_title']); ?>
									</a>
								</header>
								<div class="articleimg">
									<a href="<?php echo JA_URL.'/articles/'.stripslashes($catRow['article_uniqid']); ?>" title="<?php echo stripslashes($catRow['article_title']); ?>"  target="_blank">
										<img src="<?php echo stripslashes($catRow['article_img']); ?>" title="<?php echo stripslashes($catRow['article_title']); ?>">
									</a>
								</div>
								<div class="articledata">
									<div class="articlemeta minijianyingbikaishu">
										<img src="../../images/author.png">&nbsp;<?php echo stripslashes($catRow['article_source']); ?>
										<img src="../../images/time.png">&nbsp;<?php echo substr(stripslashes($catRow["article_date"]), 0,-9); ?>
										<img src="../../images/read.png">&nbsp;<?php echo stripslashes($catRow['article_views']); ?>人看过
									</div>
									<div class="articlesumary">
										<?php echo strip_tags(stripslashes($catRow['article_content'])); ?>
									</div>
									<div class="articleread">
										<a href="<?php echo JA_URL.'/articles/'.stripslashes($catRow['article_uniqid']); ?>" title="<?php echo stripslashes($catRow['article_title']); ?>">阅读全文>></a>
									</div>
								</div>
							</div>
					<?php
						endwhile;
					?>
					<div class="pages">
						<?php
							$pageRes = $jadb->getQuery(explode("limit", $catSql)[0]);
							include ROOT_PATH."/includes/PAGE.class.php";
							$curPath = JA_URL."/category";
							
							// 开启session跨页面传值
							// 全局变量不能跨页面传值
							session_start();
							$_SESSION['totalData'] = $jadb->affectedRows($pageRes);
							$page = new PAGE(TW,$_GET['cn'],$curPath);
							$page->setTotalData($_SESSION['totalData']);
						?>
						<ul class="pageul">
							<li id="showpagedata">
								<?php echo $_SESSION['totalData'];?>条数据&nbsp;&nbsp;共<?php echo $page->getTotalPage();?>页
							</li>
							<?php
								$page->getPages();
							?>
						</ul>
					</div>
				<?php
					else:
						echo "没有找到相关内容";
						getRandArticlesInSearch();
					endif;
				?>
			</div>

		</div>
		<div class="catside">
			<?php 
				include ROOT_PATH."/includes/catside.php"; 
			?>
		</div>
	</div>
	<?php
		include ROOT_PATH."/footer.php"; 
	?>
	
</body>
<?php include ROOT_PATH."/includes/fonts.php"; ?>
</html>