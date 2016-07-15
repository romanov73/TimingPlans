{include file="header.tpl"}

<div class="panel panel-default" style="width:100%; margin-left: auto; margin-right: auto;">
  <div class="panel-heading">DB</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h4><span class="label label-danger" style="{if $db_configured} display:none;{/if}">&#9746 Подключение не сконфигурировано (config.php: define DB_SYSTEM_HOST, DB_SYSTEM_USER, DB_SYSTEM_PASS, DB_SYSTEM_DBNAME)</span></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4><span class="label label-success" style="{if !$db_configured} display:none;{/if}">&#9745 Подключение сконфигурировано</span></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4><span class="label label-danger" style="{if $db_connection_exists} display:none;{/if}">&#9746 Невозможно подключиться к БД с заданными параметрами</span></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4><span class="label label-success" style="{if !$db_connection_exists} display:none;{/if}">&#9745 Подключение успешно выполняется</span></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <h4><span id="message_check_db" class="label" style="display:none;"></span></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <input id="clear_db_button" class="btn btn-danger" type="button" value="Очистить структуру БД"/>
            </div>
            <div class="col-md-3">
                <input id="create_db_button" class="btn btn-danger" type="button" value="Пересоздать структуру БД"/>
            </div>
            <div class="col-md-6">
                <h4><span id="message_db" class="label label-success" style="display:none;"></span></h4>
                
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default" style="width:100%; margin-left: auto; margin-right: auto;">
  <div class="panel-heading">Тестовые данные</div>
    <div class="panel-body">
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                
            </div>
        </div>
    </div>
</div>
<script>
    function checkDBStructure() {
        $.ajax({
            url: "setup.php",
            type: "GET",
            data: "check_db_structure=1",
            dataType: 'text',
            success: function(data) {
                $('#message_check_db').html(data);
                console.log(data);
                if (data == '&#9745  Структура соответствует') {
                    $('#message_check_db').addClass('label-success');
                    $('#message_check_db').removeClass('label-danger');
                    $('#create_db_button').hide();
                    $('#clear_db_button').hide();
                    $('#message_db').hide();
                } else {
                    $('#message_check_db').addClass('label-danger');
                    $('#message_check_db').removeClass('label-success');
                }
                $('#message_check_db').show();
            },
            error: function(xhr, status, error){
                 alert(error);
            }
        });
    }
    
    
    $('#clear_db_button').click(function() {
        $.ajax({
            url: "setup.php",
            type: "GET",
            data: "clear_db=1",
            dataType: 'text',
            success: function(data) {
                $('#message_db').html(data);
                checkDBStructure();
                $('#message_db').fadeIn( "slow", function() {
                    // Animation complete.
                })/*.delay( 5000 ).fadeOut( "slow", function() {
                    // Animation complete.
                })*/;
            },
            error: function(xhr, status, error){
                 alert(error);
            }
        });
    });
    $('#create_db_button').click(function() {
        $.ajax({
            url: "setup.php",
            type: "GET",
            data: "create_db=1",
            dataType: 'text',
            success: function(data) {
                $('#message_db').html(data);
                checkDBStructure();
                $('#message_db').fadeIn( "slow", function() {
                    // Animation complete.
                })/*.delay( 5000 ).fadeOut( "slow", function() {
                    // Animation complete.
                })*/;
            },
            error: function(xhr, status, error){
                 alert(error);
            }
        });
    });
       
    
    $( document ).ready(function() {
        checkDBStructure();
    });
    
</script>
{include file="footer.tpl"}