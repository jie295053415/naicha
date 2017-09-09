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


<div class="main-div">
    <form action="/SHOP/index.php/Admin/Category/edit/id/16.html" method="POST" name="main_form">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
        <table width="100%" align="center" cellpadding="3" cellspacing="1">
            <tr>
                <td class="label">上级分类：</td>
                <td>
                    <select name="parent_id">
                        <option value="0">顶级分类</option>
                        <?php foreach($catData as $v): if($v['id'] == $data['id'] || in_array($v['id'],$children)){ continue; } if($v['id'] == $data['parent_id']){ $select = 'selected="selected"'; }else{ $select = ''; } ?>
                        <option <?php echo $select; ?> value="<?php echo $v['id']; ?>"><?php echo str_repeat('-', 8*$v['level']) . $v['cat_name']; ?></option>

                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label">分类名称：</td>
                <td>
                    <input title="" name="cat_name" type="text" size="60" value="<?php echo $data['cat_name']?>"/>
                </td>
            </tr>
            <tr>
                <td class="label">是否推荐到楼层：</td>
                <td>
                    <input title="" type="radio" name="is_floor" value="是" <?php if($data['is_floor']=='是'){echo 'checked="checked"';} ?> /> 是
                    <input title="" type="radio" name="is_floor" value="否" <?php if($data['is_floor']=='否'){echo 'checked="checked"';} ?> /> 否
                </td>
            </tr>

            <tr>
                <td colspan="99" align="center">
                    <input type="submit" value=" 确定 " class="button"/>
                    <input type="reset" value=" 重置 " class="button"/>
                </td>
            </tr>
        </table>
    </form>
</div>


<!-- 网页脚部 -->
<div id="footer">
    共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
    版权所有 &copy; 2015-2017 广州奶茶信息科技有限公司，并保留所有权利。</div>
</body>
</html>