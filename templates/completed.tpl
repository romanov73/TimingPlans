{include file="header.tpl"}
{include file="menu.tpl"}

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <div class="list-group">
            {foreach from=$timing_list item=timing}
                  <a href="?timing_plan_id={$timing->id}" class="list-group-item"><span class="label label-primary">{$timing->id} {$timing->subject->name}</span>
                                                    <span class="label label-primary">{$timing->year}/{$timing->year+1} учебный год</span>
                                                    <span class="label label-primary">{$timing->semester} семестр</span>
                                                    <span class="label label-primary">{$timing->group->name}</span></a>
            {/foreach}
        </div>
    </div>
</div>                            
<script>
    $( document ).ready(function() {
        $("#li1").removeClass('active');
        $("#li2").addClass('active');
    });
</script>
{include file="footer.tpl"}
