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

<div class="main-div">
    <form name="main_form" method="POST" action="/SHOP/index.php/Admin/Privilege/add.html" enctype="multipart/form-data">
        <table cellspacing="1" cellpadding="3" width="100%">
			<tr>
				<td class="label">上级权限：</td>
				<td>
					<select name="parent_id">
						<option value="0">顶级权限</option>
						<?php foreach ($parentData as $k => $v): ?>						<option value="<?php echo $v['id']; ?>"><?php echo str_repeat('-', 8*$v['level']).$v['pri_name']; ?></option>
						<?php endforeach; ?>					</select>
				</td>
			</tr>
            <tr>
                <td class="label">权限名称：</td>
                <td>
                    <input  type="text" name="pri_name" value="" />
                </td>
            </tr>
            <tr>
                <td class="label">模块名称：</td>
                <td>
                    <input  type="text" name="module_name" value="" />
                </td>
            </tr>
            <tr>
                <td class="label">控制器名称：</td>
                <td>
                    <input  type="text" name="controller_name" value="" />
                </td>
            </tr>
            <tr>
                <td class="label">方法名称：</td>
                <td>
                    <input  type="text" name="action_name" value="" />
                </td>
            </tr>
            <tr>
                <td colspan="99" align="center">
                    <input type="submit" class="button" value=" 确定 " />
                    <input type="reset" class="button" value=" 重置 " />
                </td>
            </tr>
        </table>
    </form>
</div>


<script>
</script>

<!-- 网页脚部 -->
<div id="footer">
    共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
    版权所有 &copy; 2015-2017 广州奶茶信息科技有限公司，并保留所有权利。</div>
</body>
</html>