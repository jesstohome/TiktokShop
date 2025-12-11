<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"/www/wwwroot/demo.shop110.top/public/../application/admin/view/merchant/merchant/edit.html";i:1714197510;s:72:"/www/wwwroot/demo.shop110.top/application/admin/view/layout/default.html";i:1711598594;s:69:"/www/wwwroot/demo.shop110.top/application/admin/view/common/meta.html";i:1713930362;s:71:"/www/wwwroot/demo.shop110.top/application/admin/view/common/script.html";i:1713929490;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">
<meta name="robots" content="noindex, nofollow">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>

<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config ?? ''); ?>
    };
</script>
<!--<script src="/assets/js/watermark.js"></script>-->
<script>

</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mer_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mer_name" data-rule="required" class="form-control" name="row[mer_name]" type="text" value="<?php echo htmlentities($row['mer_name'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('商户logo'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-mer_avatar" class="form-control" size="50" name="row[mer_avatar]" type="text" value="<?php echo htmlentities($row['mer_avatar'] ?? ''); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="faupload-mer_avatar" class="btn btn-danger faupload" data-input-id="c-mer_avatar" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="false" data-preview-id="p-mer_avatar"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-mer_avatar" class="btn btn-primary fachoose" data-input-id="c-mer_avatar" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-mer_avatar"></span>
            </div>
            <ul class="row list-inline faupload-preview" id="p-mer_avatar"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Real_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-real_name" data-rule="required" class="form-control" name="row[real_name]" type="text" value="<?php echo htmlentities($row['real_name'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mer_phone'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mer_phone" data-rule="" class="form-control" name="row[mer_phone]" type="text" value="<?php echo htmlentities($row['mer_phone'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('商户邮箱'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mer_email" data-rule="" class="form-control" name="row[mer_email]" type="text" value="<?php echo htmlentities($row['mer_email'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('国家'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-country" data-rule="" class="form-control" name="row[country]" type="text" value="<?php echo htmlentities($row['country'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mer_address'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mer_address" data-rule="" class="form-control" name="row[mer_address]" type="text" value="<?php echo htmlentities($row['mer_address'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mer_keyword'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mer_keyword" data-rule="" class="form-control" name="row[mer_keyword]" type="text" value="<?php echo htmlentities($row['mer_keyword'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <input  id="c-status" name="row[status]" type="hidden" value="<?php echo $row['status']; ?>">
            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-status" data-yes="1" data-no="0" >
                <i class="fa fa-toggle-on text-success <?php if($row['status'] == '0'): ?>fa-flip-horizontal text-gray<?php endif; ?> fa-2x"></i>
            </a>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mer_info'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mer_info" data-rule="" class="form-control" name="row[mer_info]" type="text" value="<?php echo htmlentities($row['mer_info'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mark'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mark" data-rule="" class="form-control" name="row[mark]" type="text" value="<?php echo htmlentities($row['mark'] ?? ''); ?>">
        </div>
    </div>
<!--    <div class="form-group">-->
<!--        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Service_phone'); ?>:</label>-->
<!--        <div class="col-xs-12 col-sm-8">-->
<!--            <input id="c-service_phone" data-rule="required" class="form-control" name="row[service_phone]" type="text" value="<?php echo htmlentities($row['service_phone'] ?? ''); ?>">-->
<!--        </div>-->
<!--    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mer_money'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mer_money" data-rule="" class="form-control" step="0.01" name="row[mer_money]" type="number" value="<?php echo htmlentities($row['mer_money'] ?? ''); ?>">
        </div>
    </div>
<!--    <div class="form-group">-->
<!--        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mer_level'); ?>:</label>-->
<!--        <div class="col-xs-12 col-sm-8">-->
<!--            <select id="c-mer_level" data-rule="required" class="form-control selectpicker" name="row[mer_level]">-->
<!--                <option value="0">未定义</option>-->
<!--                <?php if(is_array($levelList) || $levelList instanceof \think\Collection || $levelList instanceof \think\Paginator): if( count($levelList)==0 ) : echo "" ;else: foreach($levelList as $key=>$vo): ?>-->
<!--                <option value="<?php echo $vo['level_id']; ?>" <?php if($row['mer_level'] == $vo['level_id']): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>-->
<!--                <?php endforeach; endif; else: echo "" ;endif; ?>-->
<!--            </select>-->
<!--        </div>-->
<!--    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Follow_count'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-follow_count" data-rule="" class="form-control" name="row[follow_count]" type="text" value="<?php echo htmlentities($row['follow_count'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Visit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-visit" data-rule="" class="form-control" name="row[visit]" type="text" value="<?php echo htmlentities($row['visit'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Grade'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-grade" data-rule="" class="form-control" name="row[grade]" type="text" value="<?php echo htmlentities($row['grade'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Credit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-credit" data-rule="" class="form-control" name="row[credit]" type="text" value="<?php echo htmlentities($row['credit'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Good_rate'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-good_rate" data-rule="" class="form-control" name="row[good_rate]" type="text" value="<?php echo htmlentities($row['good_rate'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('今日访问量'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-today_visit" data-rule="" class="form-control" name="row[today_visit]" type="text" value="<?php echo htmlentities($row['today_visit'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('七日访问量'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-seven_visit" data-rule="" class="form-control" name="row[seven_visit]" type="text" value="<?php echo htmlentities($row['seven_visit'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('三十日访问量'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-thirty_visit" data-rule="" class="form-control" name="row[thirty_visit]" type="text" value="<?php echo htmlentities($row['thirty_visit'] ?? ''); ?>">
        </div>
    </div>
<!--    <div class="form-group">-->
<!--        <label class="control-label col-xs-12 col-sm-2"><?php echo __('商户类型'); ?>:</label>-->
<!--        <div class="col-xs-12 col-sm-8">-->
<!--            <input id="c-type_id" data-rule="required" data-source="merchant/type/index" data-field="type_name" data-primary-key="mer_type_id" class="form-control selectpage" name="row[type_id]" type="text" value="<?php echo htmlentities($row['type_id'] ?? ''); ?>">-->
<!--        </div>-->
<!--    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('企业资质'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-images" class="form-control" size="50" name="row[images]" type="text" value="<?php echo htmlentities($row['images'] ?? ''); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="faupload-images" class="btn btn-danger faupload" data-input-id="c-images" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="true" data-preview-id="p-images"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-images" class="btn btn-primary fachoose" data-input-id="c-images" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-images"></span>
            </div>
            <ul class="row list-inline faupload-preview" id="p-images"></ul>
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-primary btn-embossed disabled"><?php echo __('OK'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version'] ?? ''); ?>"></script>
    </body>
</html>
