<?php
/* Smarty version 3.1.29, created on 2016-07-15 23:00:04
  from "/var/www/TimingPlans/templates/header.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_578924a4593db1_58127849',
  'file_dependency' => 
  array (
    'ae7993e28a3f1346256e8dc15c4a8233f138bbf9' => 
    array (
      0 => '/var/www/TimingPlans/templates/header.tpl',
      1 => 1468605601,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_578924a4593db1_58127849 ($_smarty_tpl) {
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
  <div class="panel-heading"><?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
</div>
  <div class="panel-body">
 
<?php }
}
