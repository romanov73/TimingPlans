{include file="header.tpl"}
{include file="menu.tpl"}

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <div class="list-group">
            {foreach from=$task_list item=task}
                <a href="?task_id={$task->id}" class="list-group-item">
                      <span class="label label-primary">{$task->title}
                </a>
            {/foreach}
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#li1").removeClass('active');
        $("#li2").addClass('active');
    });
</script>
{include file="footer.tpl"}
