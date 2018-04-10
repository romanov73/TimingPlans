<h5>
    <span id="message" class="label label-success" style="display:none;"></span>
    <span id="error" class="label label-danger" style="display:none;"></span>
</h5>

<ul class="nav nav-tabs">
    <li id="li1" role="presentation" class="active">
        <a href="?task_id=">{if $task->id} Редактирование {else} Создание задачи {/if}</a>
    </li>
    <li id="li2" role="presentation">
        <a href="?list=1">Список задач</a>
    </li>
</ul>

{if $message}
    <script>
        $('#message').html("{$message}");
        $('#message').fadeIn("slow", function () {}).delay(5000).fadeOut("slow", function () {});
    </script>
{/if}

{if $error}
    <script>
        $('#error').html("{$error}");
        $('#error').fadeIn("slow", function () {}).delay(5000).fadeOut("slow", function () {});
    </script>
{/if}

      
