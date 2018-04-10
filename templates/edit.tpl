{include file="header.tpl"}
{include file="menu.tpl"}

<script>
    function isNumberInput(field, event) {
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
        if (/[0-9]/.test(keyChar)) {
            window.status = "";
            var num1 = Number($(field).val());
            var num = num1 + keyChar
            if (parseInt(num) > 15) {
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

</script>

<form method="post">
    <div class="tab-content">
        <div id="panel1" class="tab-pane fade in active">
            <div class="row">
                <div class="col-md-8">
                    <label for="title">Заголовок:</label>
                    <input type="text" name="title" id="title" class="form-control" value="{$task->title}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <label for="description">Описание:</label>
                    <textarea name="description" class="form-control" id="description">{$task->description}</textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <label for="assignee">Исполнитель:</label>
                    <select name="assignee" id="assignee" class="form-control selectpicker"
                            data-live-search="true">
                        <option {if !$task->assignee->id}selected{/if} disabled=true value=''>Выберите значение</option>
                        {foreach from=$assignees item=assignee}
                            <option {if $assignee->id == $task->assignee->id}selected{/if} value='{$assignee->id}'>{$assignee->name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row" style="margin-top: 20px">
                <div class="col-md-8">
                    <input name="save" type="submit" class="btn btn-primary" value="Сохранить">
                    {if $task->id}
                        <input name="delete" type="submit" class="btn btn-danger" value="Удалить">
                        <input name="cancel" type="submit" class="btn btn-success" value="Отмена">
                    {/if}
                </div>
            </div>
        </div>
    </div>
</form>
<script>

    $(document).ready(function () {
        $('#li2').removeClass('active');
        $('#li1').addClass('active');
    });

    function getValidation() {
        return $('#subject_validation_' + $('#subject_select').val()).html();
    }

    function getSubgroupsCount() {
        return $('#group_subgroups_' + $('#group_select').val()).html();
    }

    function fillSubgroups() {
        if ($('#group_select').val() != -1 &&
            ($('#teacher_select').val() != -1)) {
            var str = "";
            for (i = 0; i < getSubgroupsCount(); i++) {
                str += "Подгруппа " + (i + 1) + "; " + $('#teacher_select option:selected').text() + "\n";
            }
            $('#teacher_lect1').val(str);
            $('#teacher_lect2').val(str);
            $('#teacher_lab1').val(str);
            $('#teacher_lab2').val(str);
            $('#teacher_prac1').val(str);
            $('#teacher_prac2').val(str);
        }
    }
</script>

{include file="footer.tpl"}