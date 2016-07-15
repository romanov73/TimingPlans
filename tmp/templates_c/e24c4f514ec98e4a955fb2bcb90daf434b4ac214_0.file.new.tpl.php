<?php
/* Smarty version 3.1.29, created on 2016-07-15 23:20:09
  from "/var/www/TimingPlans/templates/new.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5789295947d004_53943201',
  'file_dependency' => 
  array (
    'e24c4f514ec98e4a955fb2bcb90daf434b4ac214' => 
    array (
      0 => '/var/www/TimingPlans/templates/new.tpl',
      1 => 1468605443,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:menu.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_5789295947d004_53943201 ($_smarty_tpl) {
$_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:menu.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<?php echo '<script'; ?>
>
function isNumberInput(field, event){
  var key, keyChar;
  if (window.event) {
    key = window.event.keyCode;
  }
  else if (event) {
    key = event.which;
  }
  else {
    return true;
  }
  if ((key == null) || (key == 0) || (key == 8) || (key == 13) || (key == 27)) {
    return true;
  }
  keyChar = String.fromCharCode(key);
  if (/[0-9]/.test(keyChar))  {
    window.status = "";
    var num1 = Number($(field).val());
    var num = num1 + keyChar
    if(parseInt(num) > 15) {
        window.status = "Поле принимает только числа.";
        window.alert("Поле принимает только числа 1 - 15.");
        return false;
    }
    return true;
  } else {
    window.status = "Поле принимает только числа.";
    window.alert("Поле принимает только числа 1 - 15.");
    return false;
  }
}

function sumForm() {
    var sum = 0;
    $(".lect").each(function(i) {
        sum += Number($(this).val());
    });
    if (sum === 0) {
            sum = "";
    }
    $("#itog_lect").html(sum);
    
    sum = 0;
    $(".pract").each(function(i) {
        sum += Number($(this).val());
    });
    if (sum === 0) {
            sum = "";
    }
    $("#itog_pract").html(sum);
    
    sum = 0;
    $(".lab").each(function(i) {
        sum += Number($(this).val());
    });
    if (sum === 0) {
            sum = "";
    }
    $("#itog_lab").html(sum);
    sumHour();
}

function sumHour() {
    for (i = 1; i < 19; i++) {
        sum = 0;
        $(".week"+i).each(function(i) {
            sum += Number($(this).val());
        });
        if (sum === 0) {
            sum = "";
        }
        $("#itog_week"+i).html(sum);
    }
}
<?php echo '</script'; ?>
>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <h5>Заполните данные</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Преподаватель:</h5>
                    </div>
                    <div class="col-md-3">
                        <select id="teacher_select" class="selectpicker" data-live-search="true" >
                            <?php
$_from = $_smarty_tpl->tpl_vars['teacher_list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_teacher_0_saved_item = isset($_smarty_tpl->tpl_vars['teacher']) ? $_smarty_tpl->tpl_vars['teacher'] : false;
$_smarty_tpl->tpl_vars['teacher'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['teacher']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['teacher']->value) {
$_smarty_tpl->tpl_vars['teacher']->_loop = true;
$__foreach_teacher_0_saved_local_item = $_smarty_tpl->tpl_vars['teacher'];
?>
                                <option value='<?php echo $_smarty_tpl->tpl_vars['teacher']->value->id;?>
'><?php echo $_smarty_tpl->tpl_vars['teacher']->value->FIO;?>
</option>
                            <?php
$_smarty_tpl->tpl_vars['teacher'] = $__foreach_teacher_0_saved_local_item;
}
if ($__foreach_teacher_0_saved_item) {
$_smarty_tpl->tpl_vars['teacher'] = $__foreach_teacher_0_saved_item;
}
?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Дисциплина:</h5>
                    </div>
                    <div class="col-md-3">
                        <select id="subject_select" class="selectpicker" data-live-search="true" >
                        </select>
                    </div>
                </div>
            </div>
                        
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Группа:</h5>
                    </div>
                    <div class="col-md-3">
                        <select id="group_select" class="selectpicker" data-live-search="true" >
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <table border="1" width="100%">
                    <tr>
                        <td><strong>Форма занятий</strong></td>
                        <td><strong>Недели</strong></td>
                            <?php
$__section_week_0_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_week']) ? $_smarty_tpl->tpl_vars['__smarty_section_week'] : false;
$_smarty_tpl->tpl_vars['__smarty_section_week'] = new Smarty_Variable(array());
if (true) {
for ($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] = 1, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index'] = 0; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] <= 18; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']++, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index']++){
?>
                                <td class="td-center"><strong><?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] : null);?>
</strong></td>
                            <?php
}
}
if ($__section_week_0_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_week'] = $__section_week_0_saved;
}
?>
                        <td><strong>&nbsp;&sum;&nbsp;</strong></td>
                        <td><strong>Отчетн.</strong></td>
                    </tr>
                    <tr class="tr-input-hour">
                        <td rowspan="2"><strong>1. Лекции</strong></td>
                        <td>аудит.</td>
                            <?php
$__section_week_1_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_week']) ? $_smarty_tpl->tpl_vars['__smarty_section_week'] : false;
$_smarty_tpl->tpl_vars['__smarty_section_week'] = new Smarty_Variable(array());
if (true) {
for ($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] = 1, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index'] = 0; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] <= 18; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']++, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index']++){
?>
                                <td class="td-input-hour"><input type="text" class="input-hour lect week<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] : null);?>
" onkeypress="return isNumberInput(this, event);" onchange="sumForm();"></td>
                            <?php
}
}
if ($__section_week_1_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_week'] = $__section_week_1_saved;
}
?>
                        <td class="td-center" id="itog_lect">&nbsp;</td>
                        <td rowspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><textarea id="lection_audit" ></textarea></td>
                        <td colspan="9" style="text-align:center;"><textarea id="teacher_lect1" width="100%"></textarea></td>
                        <td colspan="10" style="text-align:center;"><textarea id="teacher_lect2" width="100%"></textarea></td>
                    </tr>
                    <tr class="tr-input-hour">
                        <td rowspan="2"><strong>2. Практ. занятия, семинары</strong></td>
                        <td>аудит.</td>
                            <?php
$__section_week_2_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_week']) ? $_smarty_tpl->tpl_vars['__smarty_section_week'] : false;
$_smarty_tpl->tpl_vars['__smarty_section_week'] = new Smarty_Variable(array());
if (true) {
for ($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] = 1, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index'] = 0; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] <= 18; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']++, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index']++){
?>
                                <td id="" class="td-input-hour"><input type="text" class="input-hour pract week<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] : null);?>
" onkeypress="return isNumberInput(this, event);" onchange="sumForm();"></td>
                            <?php
}
}
if ($__section_week_2_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_week'] = $__section_week_2_saved;
}
?>
                        <td class="td-center" id="itog_pract">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><textarea id="lection_audit" ></textarea></td>
                        <td colspan="9" style="text-align:center;"><textarea id="teacher_prac1" width="100%"></textarea></td>
                        <td colspan="10" style="text-align:center;"><textarea id="teacher_prac2" width="100%"></textarea></td>
                    </tr>
                    <tr class="tr-input-hour">
                        <td rowspan="2"><strong>3. Лабораторные занятия</strong></td>
                        <td>аудит.</td>
                            <?php
$__section_week_3_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_week']) ? $_smarty_tpl->tpl_vars['__smarty_section_week'] : false;
$_smarty_tpl->tpl_vars['__smarty_section_week'] = new Smarty_Variable(array());
if (true) {
for ($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] = 1, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index'] = 0; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] <= 18; $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']++, $_smarty_tpl->tpl_vars['__smarty_section_week']->value['index']++){
?>
                                <td id="" class="td-input-hour"><input type="text" class="input-hour lab week<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_section_week']->value['iteration'] : null);?>
" onkeypress="return isNumberInput(this, event);"  onchange="sumForm();"></td>
                            <?php
}
}
if ($__section_week_3_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_week'] = $__section_week_3_saved;
}
?>
                        <td class="td-center" id="itog_lab">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><textarea id="lection_audit" ></textarea></td>
                        <td colspan="9" style="text-align:center;"><textarea id="teacher_lab1" width="100%"></textarea></td>
                        <td colspan="10" style="text-align:center;"><textarea id="teacher_lab2" width="100%"></textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Всего:</strong></td>
                        <td>аудит.</td>
                            <?php
$__section_itog_4_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_itog']) ? $_smarty_tpl->tpl_vars['__smarty_section_itog'] : false;
$_smarty_tpl->tpl_vars['__smarty_section_itog'] = new Smarty_Variable(array());
if (true) {
for ($_smarty_tpl->tpl_vars['__smarty_section_itog']->value['iteration'] = 1, $_smarty_tpl->tpl_vars['__smarty_section_itog']->value['index'] = 0; $_smarty_tpl->tpl_vars['__smarty_section_itog']->value['iteration'] <= 18; $_smarty_tpl->tpl_vars['__smarty_section_itog']->value['iteration']++, $_smarty_tpl->tpl_vars['__smarty_section_itog']->value['index']++){
?>
                                <td class="td-center" id="itog_week<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_itog']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_section_itog']->value['iteration'] : null);?>
">&nbsp;</td>
                            <?php
}
}
if ($__section_itog_4_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_itog'] = $__section_itog_4_saved;
}
?>
                        <td id="itog_final">&nbsp;</td>
                        <td id="itog_final">&nbsp;</td>
                    </tr>
                    <tr>
                        <td rowspan=1>Пожелания препод-ля:</td>
                        <td colspan="20" valign="center"><textarea id="prepod_wish" cols="20" rows="3" style="width:100%;height:50;border:solid 1px"> </textarea></td>
                        <td colspan="4" align="center" valign="center"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>                            
<?php echo '<script'; ?>
>
    $( document ).ready(function() {
        $('#li2').removeClass('active');
        $('#li1').addClass('active');
    });
    
    $('#teacher_select').change(function() {
        $.ajax({
            url: "index.php",
            type: "GET",
            data: "get_subject_list=1&teacher_id="+this.value,
            dataType: 'text',
            success: function(data) {
                var subjects = JSON.parse(data);
                var options = '';
                $.each(subjects, function(index, value) {
                    options += '<option value="'+ value.id + '">' + value.name + '</option>';
                });
                $('#subject_select').empty();
                $('#subject_select').append(options);
                $('#subject_select').selectpicker('refresh');
            },
            error: function(xhr, status, error){
                 alert(error);
            }
        });
    });
<?php echo '</script'; ?>
>
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
