<?php
/* Smarty version 3.1.29, created on 2016-07-14 03:13:19
  from "/var/www/TimingPlans/templates/completed.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5786bcff476355_45729612',
  'file_dependency' => 
  array (
    'faa3e3ade14d3e85b1e4c8c24b042bde4d5329fb' => 
    array (
      0 => '/var/www/TimingPlans/templates/completed.tpl',
      1 => 1468447996,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_5786bcff476355_45729612 ($_smarty_tpl) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <h4>Заполните данные</h4>
        <div class="row">
            <div class="col-sm-2">
                <h4>Преподаватель:</h4>
            </div>
            <div class="col-sm-4">
                
            </div>
        </div>
    </div>
</div>                            
<?php echo '<script'; ?>
>
    $( document ).ready(function() {
        $("#li1").removeClass('active');
        $("#li2").addClass('active');
    });
<?php echo '</script'; ?>
>
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
