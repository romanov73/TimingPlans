{include file="header.tpl"}
{include file="menu.tpl"}

<script>
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
    var total_sum = 0;
    $(".lect").each(function(i) {
        sum += Number($(this).val());
    });
    total_sum = sum;
    if (sum === 0) {
            sum = "";
    }
    $("#itog_lect").html(sum);
    
    sum = 0;
    $(".pract").each(function(i) {
        sum += Number($(this).val());
    });
    total_sum += sum;
    if (sum === 0) {
            sum = "";
    }
    $("#itog_pract").html(sum);
    
    sum = 0;
    $(".lab").each(function(i) {
        sum += Number($(this).val());
    });
    total_sum += sum;
    if (sum === 0) {
            sum = "";
    }
    $("#itog_lab").html(sum);
    sumHour();
    $("#itog_final").html(total_sum);
}

function sumHour() {
    for (i = 1; i < {$weeks_count}+1; i++) {
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
</script>

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
                        <select id="teacher_select" class="selectpicker glowing-border" data-live-search="true" >
                            {foreach from=$teacher_list item=teacher}
                                <option value='{$teacher->id}'>{$teacher->FIO}</option>
                            {/foreach}
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
                            {foreach from=$subject_list item=subject}
                                <option value='{$subject->id}'>{$subject->name}</option>
                            {/foreach}
                        </select>
                            {foreach from=$subject_list item=subject}
                                <div style="display:none;" id='subject_lect_hours_{$subject->id}'>{$subject->lection_hours}</div>
                                <div style="display:none;" id='subject_pract_hours_{$subject->id}'>{$subject->practic_hours}</div>
                                <div style="display:none;" id='subject_lab_hours_{$subject->id}'>{$subject->labs_hours}</div>
                                <div style="display:none;" id='subject_validation_{$subject->id}'>{$subject->validation}</div>
                            {/foreach}
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
                            {foreach from=$group_list item=group}
                                <option value='{$group->id}'>{$group->name}</option>
                            {/foreach}
                        </select>
                            {foreach from=$group_list item=group}
                                <div style="display:none;" id='group_subgroups_{$group->id}'>{$group->count_subgroups}</div>
                            {/foreach}
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
                            {section name=week loop={$weeks_count}}
                                <td class="td-center"><strong>{$smarty.section.week.iteration}</strong></td>
                            {/section}
                        <td><strong>&nbsp;&sum;&nbsp;</strong></td>
                        <td><strong>Отчетн.</strong></td>
                    </tr>
                    <tr class="tr-input-hour">
                        <td rowspan="2"><strong>1. Лекции</strong></td>
                        <td>аудит.</td>
                            {section name="week" loop={$weeks_count}}
                                <td class="td-input-hour"><input type="text" class="input-hour lect week{$smarty.section.week.iteration}" onkeypress="return isNumberInput(this, event);" onchange="sumForm();"></td>
                            {/section}
                        <td class="td-center" id="itog_lect">&nbsp;</td>
                        <td rowspan="6" id="validation">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><textarea id="lection_audit" ></textarea></td>
                        <td colspan="{$weeks_count/2}" style="text-align:center;"><textarea id="teacher_lect1" style="width:100%"></textarea></td>
                        <td colspan="{$weeks_count/2+1}" style="text-align:center;"><textarea id="teacher_lect2" style="width:100%"></textarea></td>
                    </tr>
                    <tr class="tr-input-hour">
                        <td rowspan="2"><strong>2. Практ. занятия, семинары</strong></td>
                        <td>аудит.</td>
                            {section name="week" loop={$weeks_count}}
                                <td id="" class="td-input-hour"><input type="text" class="input-hour pract week{$smarty.section.week.iteration}" onkeypress="return isNumberInput(this, event);" onchange="sumForm();"></td>
                            {/section}
                        <td class="td-center" id="itog_pract">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><textarea id="lection_audit" ></textarea></td>
                        <td colspan="{$weeks_count/2}" style="text-align:center;"><textarea id="teacher_prac1" style="width:100%"></textarea></td>
                        <td colspan="{$weeks_count/2+1}" style="text-align:center;"><textarea id="teacher_prac2" style="width:100%"></textarea></td>
                    </tr>
                    <tr class="tr-input-hour">
                        <td rowspan="2"><strong>3. Лабораторные занятия</strong></td>
                        <td>аудит.</td>
                            {section name="week" loop={$weeks_count}}
                                <td id="" class="td-input-hour"><input type="text" class="input-hour lab week{$smarty.section.week.iteration}" onkeypress="return isNumberInput(this, event);"  onchange="sumForm();"></td>
                            {/section}
                        <td class="td-center" id="itog_lab">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><textarea id="lection_audit" ></textarea></td>
                        <td colspan="{$weeks_count/2}" style="text-align:center;"><textarea id="teacher_lab1" style="width:100%"></textarea></td>
                        <td colspan="{$weeks_count/2+1}" style="text-align:center;"><textarea id="teacher_lab2" style="width:100%"></textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Всего:</strong></td>
                        <td>аудит.</td>
                            {section name="itog" loop={$weeks_count}}
                                <td class="td-center" id="itog_week{$smarty.section.itog.iteration}">&nbsp;</td>
                            {/section}
                        <td id="itog_final">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td rowspan=1>Пожелания препод-ля:</td>
                        <td colspan="{$weeks_count+2}" valign="center"><textarea id="prepod_wish" cols="20" rows="3" style="width:100%;height:50;border:solid 1px"> </textarea></td>
                        <td colspan="4" align="center" valign="center"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>                            
<script>

    $( document ).ready(function() {
        $('#li2').removeClass('active');
        $('#li1').addClass('active');
     });
    
    function getValidation() {
        return $('#subject_validation_'+$('#subject_select').val()).html();
    }
    
    function getSubgroupsCount() {
        return $('#group_subgroups_'+$('#group_select').val()).html();
    }
    
    function fillSubgroups() {
        if ($('#group_select').val() != -1 && 
                ($('#teacher_select').val() != -1)) {
            var str = "";
            for (i =0; i < getSubgroupsCount();i++) {
                str += "Подгруппа " + (i+1) + "; " + $('#teacher_select option:selected').text()+"\n";
            }
            $('#teacher_lect1').val(str);    
            $('#teacher_lect2').val(str);    
            $('#teacher_lab1').val(str);    
            $('#teacher_lab2').val(str);
            $('#teacher_prac1').val(str);    
            $('#teacher_prac2').val(str);
        }
    }
    
    $('#group_select').change(function() {
        fillSubgroups();
    });
    
    $('#subject_select').change(function() {
        if ($('#subject_select').val() != "-1") {
            $('#validation').html(getValidation());
        } else {
            $('#validation').html("");
        }
    });
    
    $('#teacher_select').change(function() {
        if ($('#teacher_select').val() !== '-1') {
            $('#teacher_select').next().removeClass("glowing-border");
        } else {
            $('#teacher_select').next().addClass("glowing-border");
        }
        $.ajax({
            url: "index.php",
            type: "GET",
            data: "get_subject_list=1&teacher_id="+this.value,
            dataType: 'text',
            success: function(data) {
                /*var subjects = JSON.parse(data);
                var options = '';
                $.each(subjects, function(index, value) {
                    options += '<option value="'+ value.id + '">' + value.name + '</option>';
                });
                $('#subject_select').empty();
                $('#subject_select').append(options);
                $('#subject_select').selectpicker('refresh');*/
            },
            error: function(xhr, status, error){
                 alert(error);
            }
        });
    });
</script>
{include file="footer.tpl"}