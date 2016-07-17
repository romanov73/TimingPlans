{include file="header.tpl"}
{include file="menu.tpl"}

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <div class="list-group">
            {foreach from=$timing_list item=timing}
                  <a href="#" class="list-group-item"><span class="label label-primary">{$timing->subject->name}</span> {$timing->year}/{$timing->year+1} {$timing->semester} семестр  {$timing->group->name} </a>
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