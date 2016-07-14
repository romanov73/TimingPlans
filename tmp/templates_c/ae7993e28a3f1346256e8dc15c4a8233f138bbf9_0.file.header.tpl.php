<?php
/* Smarty version 3.1.29, created on 2016-07-14 03:40:52
  from "/var/www/TimingPlans/templates/header.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5786c3742ab257_83355104',
  'file_dependency' => 
  array (
    'ae7993e28a3f1346256e8dc15c4a8233f138bbf9' => 
    array (
      0 => '/var/www/TimingPlans/templates/header.tpl',
      1 => 1468449649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5786c3742ab257_83355104 ($_smarty_tpl) {
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
</title>
    <link href="libs/bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="libs/bootstrap-select.min.css" />
    <link rel="stylesheet" href="templates/custom.css" />
  </head>
  <body>
        <?php echo '<script'; ?>
 src="libs/jquery/jquery.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="libs/bootstrap.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="libs/bootstrap-select.min.js"><?php echo '</script'; ?>
>
        
<div class="panel panel-default" style="width:90%; margin-left: auto; margin-right: auto;">
  <div class="panel-heading">Ввод расчасовок</div>
  <div class="panel-body">
    
<ul class="nav nav-tabs">
  <li id="li1" role="presentation" class="active"><a href="index.php">Ввод новой</a></li>
  <li id="li2" role="presentation"><a href="?completed=1">Готовые</a></li>
</ul>

      
<?php }
}
