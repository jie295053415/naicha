<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>SHOP 管理中心 - <?php echo $_page_title?> </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/SHOP/Public/Admin/Styles/general.css" rel="stylesheet" type="text/css" />
    <link href="/SHOP/Public/Admin/Styles/main.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/SHOP/Public/umeditor1_2_2-utf8-php/third-party/jquery.min.js"></script>
</head>
<body>
<h1>
    <?php if($_page_btn_name){?>
        <span class="action-span"><a href="<?php echo $_page_btn_link?>"><?php echo $_page_btn_name?></a></span>
    <?php }?>
    <span class="action-span1"><a href="/SHOP/index.php">管理中心</a></span>
    <span id="search_id" class="action-span1"> - <?php echo $_page_title?> </span>
    <div style="clear:both"></div>
</h1>

<!-- 正文内容 -->
<!-- 引入布局文件 -->


<!-- 目录列表 -->
<form method="post" action="" name="listForm" onsubmit="">
    <div class="list-div" id="listDiv">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th>分类名称</th>
                <th>操作</th>
            </tr>
            <?php foreach($data as $k=>$v){?>
            <tr class="tron">
                <td><?php echo str_repeat('-',8*$v['level']).$v['cat_name'];?></td>
                <td align="center">
                    <a href="<?php echo U('edit?id='.$v['id']); ?>">修改</a>
                    <a onclick="return confirm('确定要删除吗？');" href="<?php echo U('delete?id='.$v['id']); ?>">删除</a>
                </td>
            </tr>
            <?php }?>
        </table>

        <!-- 分页开始 -->
        <table id="page-table" cellspacing="0">
            <tr>
                <td width="80%">&nbsp;</td>
                <td align="center" nowrap="true"><?php echo $page;?>
                </td>
            </tr>
        </table>
        <!-- 分页结束 -->
    </div>
</form>







<!-- 引入行高亮显示 -->
<script type="text/javascript" src="/SHOP/public/Admin/Js/tron.js">
    $(".tron").mouseover(function(){
        // 修改这个TR里每个TD的背景色
        $(this).find("td").css('backgroundColor', '#DEE7F5');
    });
    $(".tron").mouseout(function(){
        // 修改这个TR里每个TD的背景色
        $(this).find("td").css('backgroundColor', '#FFF');
    });
</script>

<!-- 网页脚部 -->
<div id="footer">
    共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
    版权所有 &copy; 2015-2017 广州奶茶信息科技有限公司，并保留所有权利。</div>
</body>
</html>