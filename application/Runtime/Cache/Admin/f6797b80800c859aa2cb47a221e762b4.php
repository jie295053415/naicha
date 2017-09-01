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


<div class="form-div">
    <!--搜索表单-->
    <form action="/SHOP/index.php/Admin/Goods/lst" method="get" name="searchForm">
        <p>
            分&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;类：
            <?php $cat_id = I('get.catId'); ?>
            <select name="cat_id">
                <option value="">选择分类</option>
                <?php foreach($catData as $v){ if($v['id'] == $cat_id){ $select = 'selected="selected"'; }else{ $select = ''; } ?>
                <option <?php echo $select; ?> value="<?php echo $v['id'];?>">
                    <?php echo str_repeat('-',8*$v['level']).$v['cat_name']; ?>
                </option>
                <?php } ?>
            </select>
        </p>

        <p>
            所在品牌： <?php buildSelect('brand', 'brand_id', 'id', 'brand_name',I('get.brand_id')); ?>
            <!--TODO 选品牌，一点搜索 还是选定品牌-->
        </p>
        <p>
            商品名称： <input value="<?php echo I('get.gn'); ?>" type="text" name="gn" size="60" />
        </p>
        <p>
            价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格：
            从<input value="<?php echo I('get.fp'); ?>" type="text" name="fp" size="8" />
            到<input value="<?php echo I('get.tp'); ?>" type="text" name="tp" size="8" />
        </p>
        <p>
            是否上架：  <?php $ios = I('get.ios'); ?>
            <input type="radio" name="ios" value="" <?php if($ios == '') echo 'checked="checked"'; ?> />全部
            <input type="radio" name="ios" value="是" <?php if($ios == '是') echo 'checked="checked"'; ?> />上架
            <input type="radio" name="ios" value="否" <?php if($ios == '否') echo 'checked="checked"'; ?> />下架
        </p>
        <p>
            添加时间：
            从<input type="text" id="fa" name="fa" value="<?php echo I('get.fa'); ?>" />
            到<input type="text" id="ta" name="fa" value="<?php echo I('get.ta'); ?>" />
        </p>
        <p>
            排序方式：   <?php  $odby = I('get.odby','id_desc'); ?>
            <input onclick="this.parentNode.parentNode.submit();" type="radio" name="odby" value="id_desc" <?php if($odby == 'id_desc') echo 'checked="checked"'; ?>  />以添加时间降序
            <input onclick="this.parentNode.parentNode.submit();" type="radio" name="odby" value="id_asc" <?php if($odby == 'id_asc') echo 'checked="checked"'; ?> />以添加时间升序
            <input onclick="this.parentNode.parentNode.submit();" type="radio" name="odby" value="price_desc" <?php if($odby == 'price_desc') echo 'checked="checked"'; ?> />以价格降序
            <input onclick="this.parentNode.parentNode.submit();" type="radio" name="odby" value="price_asc" <?php if($odby == 'price_asc') echo 'checked="checked"'; ?> />以价格升序
        </p>
        <p>
            <input type="submit" value="搜索" />
        </p>
    </form>
</div>

<!-- 商品列表 -->
<form method="post" action="" name="listForm" onsubmit="">
    <div class="list-div" id="listDiv">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th>编号</th>
                <th>主分类</th>
                <th>扩展分类</th>
                <th>品牌</th>
                <th>商品名称</th>
                <th>logo</th>
                <th>市场价格</th>
                <th>本店价格</th>
                <th>上架</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
                <?php foreach($data as $k=>$v){?>
                <tr class="tron">
                    <td align="center"><?php echo $v['id']; ?></td>
                    <td align="center"><?php echo $v['cat_name']; ?></td>
                    <td align="center"><?php echo $v['ext_cat_name']; ?></td>
                    <td align="center"><?php echo $v['brand_name']; ?></td>
                    <td align="center" class="first-cell"><?php echo $v['goods_name']; ?></td>
                    <td align="center"><?php showImage($v['sm_logo']); ?></td>
                    <td align="center"><?php echo $v['market_price']; ?></td>
                    <td align="center"><?php echo $v['shop_price']; ?></td>
                    <td align="center"><?php echo $v['is_on_sale']; ?></td>
                    <td align="center"><?php echo $v['addtime']; ?></td>
                    <td align="center">
                        <a href="<?php echo U('goods_number?id='.$v['id']); ?>">库存量</a>
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






<!-- 引入时间插件 -->

<script type="text/javascript" src="/SHOP/Public/umeditor1_2_2-utf8-php/third-party/jquery.min.js"></script>
<!--<script type="text/javascript" src="/SHOP/Public/Admin/Js/jquery-1.10.0.min.js"></script> -->
<link href="/SHOP/Public/datetimepicker/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="/SHOP/Public/datetimepicker/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/SHOP/Public/datetimepicker/datepicker-zh_cn.js"></script>
<link rel="stylesheet" media="all" type="text/css" href="/SHOP/Public/datetimepicker/time/jquery-ui-timepicker-addon.min.css" />
<script type="text/javascript" src="/SHOP/Public/datetimepicker/time/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" src="/SHOP/Public/datetimepicker/time/i18n/jquery-ui-timepicker-addon-i18n.min.js"></script>

<script>
    // 添加时间插件
    $.timepicker.setDefaults($.timepicker.regional['zh-CN']);  // 设置使用中文

    $("#fa").datetimepicker();
    $("#ta").datetimepicker();
</script>
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