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

<!-- 列表 -->
<div class="list-div" id="listDiv">
    <form method="post" action="/SHOP/index.php/Admin/Goods/goods_number/id/10.html">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <!-- 循环输出属性 -->
                <?php foreach ($gaData as $k => $v): ?>
                    <th><?php echo $k; ?></th>
                <?php endforeach; ?>
                <th >库存量</th>
                <th width="60">操作</th>
            </tr>
            <?php if($gnData): ?>
                <?php foreach ($gnData as $k0 => $v0): ?>
                <tr class="tron">
                    <?php
 $gaCount = count($gaData); foreach ($gaData as $k =>$v): ?>
                        <td align="center">
                            <select title="" name="goods_attr_id[]">
                                <option value="">请选择</option>
                                <?php foreach ($v as $k1 => $v1) : $_attr = explode(',',$v0['goods_attr_id']); if(in_array($v1['id'],$_attr)){ $select = 'selected="selected"'; }else{ $select = ''; } ?>
                                    <!-- 这里的ID是p39_goods_attr表的ID -->
                                    <option <?php echo $select; ?> value="<?php echo $v1['id']; ?>"><?php echo $v1[attr_value]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    <?php endforeach; ?>
                    <td align="center"><input type="text" name="goods_number[]" title="" value="<?php echo $v0['goods_number']; ?>" /></td>
                    <td align="center"><input type="button" value="<?php echo $k0==0?' + ':' - ' ?>" onclick="addNewTr(this);" /></td>
                </tr>
                <?php endforeach;?>
            <?php else: ?>



                <tr class="tron">
                    <?php
 $gaCount = count($gaData); foreach ($gaData as $k =>$v){ ?>
                    <td align="center">
                        <select title="" name="goods_attr_id[]">
                            <option value="">请选择</option>
                            <?php foreach ($v as $k1 => $v1): ?>
                            <!-- 这里的ID是p39_goods_attr表的ID -->
                            <option value="<?php echo $v1['id']; ?>"><?php echo $v1[attr_value]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <?php } ?>
                    <td align="center"><input type="text" name="goods_number[]" title="" value=""/></td>
                    <td align="center"><input type="button" value=" + " onclick="addNewTr(this);" /></td>
                </tr>

            <?php endif; ?>
            <tr id="submit">
                <td align="center" colspan="<?php echo $gaCount+2; ?>"><input title="" type="submit" value=" 提 交 " /></td>
            </tr>
        </table>
    </form>
</div>

<!-- 点击＋号的js脚本 -->
<script>
    function addNewTr(btn){
        var tr = $(btn).parent().parent();
        if($(btn).val() == ' + '){
            var newTr = tr.clone();
            newTr.find(':button').val(' - ');
            $('#submit').before(newTr);
        }else{
            tr.remove();
        }
    }
</script>
<script src="/SHOP/Public/Admin/Js/tron.js"></script>

<!-- 网页脚部 -->
<div id="footer">
    共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
    版权所有 &copy; 2015-2017 广州奶茶信息科技有限公司，并保留所有权利。</div>
</body>
</html>