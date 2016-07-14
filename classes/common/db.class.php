<?php
/**
 * Файл объвления класса {@link _MyDB}.
 *
 * @tutorial SMWE/dev.db.pkg
 * @copyright 2004-2005 snaky
 * @author snaky <snaky@ulstu.ru>
 * @package SMWE
 * @subpackage Classes
 * 
 * ******
 * $URL: http://svn.ulstu.ru/repos/snaky/site_engine/trunk/src/libs/common/db.class.php $
 * $Id: db.class.php 1196 2006-09-05 08:42:50Z engel $
 * ******
 */

///////////////////////////////////////////////////////
/**#@+
 * Используется в методе {@link fetch_all()}.
 */

/**
 * @deprecated
 */
define("DB_RESULT_ROW_IS_ARRAY", 1);

/**
 * @deprecated
 */
define("DB_FETCH_ROWS_INDEX_KEYS",  1);
/**
 * Строки возвращаются в двумерный массив, каждая строка которого -
 * ассоциативный массив вида "поле" => "значение".
 */
define("DB_FETCH_ALL_INDEX_KEYS",  1);

/**
 * @deprecated
 */
define("DB_RESULT_FIRST_IS_KEY",  2);
/**
 * @deprecated
 */
define("DB_FETCH_ROWS_VALUE_KEYS", 2);
/**
 * Строки возвращаются в двумерный массив, каждая строка которого -
 * ассоциативный массив вида "поле" => "значение". Значения поля результата
 * участвуют в образовании ключей массива строк.
 */
define("DB_FETCH_ALL_VALUE_KEYS", 2);

/**
 * @deprecated
 */
define("DB_RESULT_KEY_VALUE",  3);
/**
 * @deprecated
 */
define("DB_FETCH_ROWS_VECTOR", 3);
/**
 * Одномерный массив вида "значение_первого_поля" => "значение_второго_поля".
 */
define("DB_FETCH_ALL_VECTOR", 3);
/**#@-*/
///////////////////////////////////////////////////////


/**
 * Функция обработки ошибок
 */
function _mydb_error_handler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() & $errno) {
        $err_text = "<br>\n";
        switch ($errno) {
            case E_USER_ERROR:
                $err_text .= "<b>FATAL</b> [$errno] $errstr<br>\n";
            break;
            case E_USER_WARNING:
                $err_text .= "<b>WARNING</b> [$errno] $errstr<br>\n";
            break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $err_text .= "<b>NOTICE</b> [$errno] $errstr<br>\n";
            break;
            default:
                $err_text .= "Unkown error type: [$errno] $errstr<br>\n";
            break;
        }
        $err_text .= "<br>\n";
        if (!array_key_exists("SERVER_NAME", $_SERVER)) {
            $err_text = strip_tags($err_text);
        }
        echo $err_text;
        if ($errno == E_USER_ERROR) {
            exit(1);
        }
    }
}

/**
 * Абстрактный класс для работы с СУБД (MySQL, PostgreSQL).
 *
 * Основные возможности:
 *  - подключение к серверу (нескольким серверам) под разными учетными записями;
 *  - легкий переход между подключениями;
 *  - ведение подробных логов запросов и ошибок;
 *  - автоматическое соствление запросов;
 *  - возврат результатов запросов в виде различных структур ассоциативных массивов 
 *  - отлов ошибок в группе запросов;
 *  - сообщения об ошибках в запросах указывают на места вызова методов.
 * 
 * Один объект класса отражает одно подключение к СУБД. 
 *
 * @tutorial SMWE/dev.db.pkg
 * @package SMWE
 * @subpackage Classes
 * @abstract
 * @author snaky <snaky@ulstu.ru>
 * @copyright 2004-2005 snaky
 */
abstract class _MyDB {
    private $_connect_name;
    private $_db_host;
    private $_db_user;
    private $_db_passwd;
    private $_db_name;

    /**
     * имена схем БД (если СУБД не поддерживает схем, то имена БД)
     * @var array
     */
    private $_schemas;

    /**
     * русерсы соединений
     * @var array
     */
    private $_db_link;

    /**
     * имя текущей БД/схемы
     * @var string
     */
    private $_cur_schema;

    private $_param_char = "%";

    private $_schema_char = "#";

    protected $_field_quote = "\"";

    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function &__fetch_assoc(&$res);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function &__fetch_num(&$res);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __select_schema($schema, $link);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __connect($host, $user, $passwd, $dbname=null);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __rdbms();
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __close($link);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function &__query($query, $link = null);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __error($link = null);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __free_result(&$res);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __escape_string($value);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __data_seek(&$res, $pos);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __num_rows(&$res);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __affected_rows(&$res);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected  function &__table_fields($table_name, $schema = null);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected  function __log_connect($db_user, $db_name = null);
    ///////////////////////////////////////////////////////////////////////////////
    abstract protected function __log_selecting_schema($schema);
    ///////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает значение автоинкрементного поля таблицы после последней вставки.
     * 
     * Виртуальный метод (описание см. в наследуемых классах).
     */
     abstract protected function insert_id($tablename = null, $fieldname = "id", $schema = null);

///////////////////////////////////////////////////////////////////////////////////
    public function __construct($connect_name, $connect_data)
    {
        $this->_connect_name = $connect_name;
        $this->_db_host      = $connect_data['host'];
        $this->_db_user      = $connect_data['user'];
        $this->_db_passwd    = $connect_data['passwd'];
        $this->_db_name      = $connect_data['db_name'];
    }
    
    ///////////////////////////////////////////////////////
    private function _get_real_schema($schema)
    {
        if (@array_key_exists($schema, $this->_schemas)) {
            $schema = $this->_schemas[$schema];
        }
        return $schema;
    }
    
    ///////////////////////////////////////////////////////
    private function _log_query($entry, $type = "normal", $start_run_time = 0, $end_run_time = 0)
    {
        $logging = true;
        $params = MyDB::enable_query_log();
        if ($params[1] && !$this->_is_update_query($entry) && $type == "normal") {
            $logging = false;
        }
        if ($logging) {
            $fp = fopen($params[0], "at");
            fputs($fp, $entry.";\n");
            if ($params[2]) {
                $bt = debug_backtrace();
                $i = 0;
                while (
                    $bt[$i]["file"]  == __FILE__
                    || $bt[$i]["file"] == MyDB::_get_file_name()
                ) {
                    $i++;
                }
                $file = $bt[$i]["file"];
                $line = $bt[$i]["line"];
                fputs($fp, "-- URI: ".$_SERVER["REQUEST_URI"]."\n");
                fputs($fp, "-- File: $file (line: $line)\n");
                $gen_time = $end_run_time - $start_run_time;
                fputs($fp, "-- Run-time: ".sprintf("%.4f", $gen_time)."\n\n");
            }
            fclose($fp);
            @chmod($params[0], 0664);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////
    private function _log_connect($db_user, $db_name = null, $start_run_time = null, $end_run_time = null)
    {
        $this->_log_query($this->__log_connect($db_user, $db_name), "connect", $start_run_time, $end_run_time);
    }
    ///////////////////////////////////////////////////////////////////////////////
    private function _log_selecting_schema($schema, $start_run_time = null, $end_run_time = null)
    {
        $this->_log_query($this->__log_selecting_schema($schema), "schema", $start_run_time, $end_run_time);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function _error($errstr, $errtype = E_USER_WARNING, $step = 1)
    {
        if ($step == 1) {
            MyDB::_db_error(true);
            $bt = debug_backtrace();
            $i = 0;
            while (
                $bt[$i]["file"]  == __FILE__
                || $bt[$i]["file"] == MyDB::_get_file_name()
            ) {
                $i++;
            }
            $file = $bt[$i]["file"];
            $line = $bt[$i]["line"];
            $errstr = "$errstr<br>\nin file <b>$file</b> on line <b>$line</b><br>\n";
            return $errstr;
        }
        if ($step == 2) {
            if (MyDB::reset_error_handler()) {
                $old_error_handler = set_error_handler("_mydb_error_handler");
            }
            if (MyDB::enable_error_log()) {
                error_log("\n[".date("Y-m-d H:i:s")."]\n".strip_tags($errstr), 3, MyDB::enable_error_log());
            }
            trigger_error($errstr, $errtype);
            if (MyDB::reset_error_handler()) {
                restore_error_handler();
            }
        }
    }
    ///////////////////////////////////////////////////////////////////////////////
    private function _is_update_query($query)
    {
        $updates = 'INSERT |UPDATE |DELETE |'.'REPLACE |CREATE |DROP |'.'SET |BEGIN|COMMIT|ROLLBACK|START|END'.
                   'ALTER |GRANT |REVOKE |'.'LOCK |UNLOCK ';
        if (preg_match('/^\s*"?('.$updates.')/i', $query)) {
            return true;
        }
        return false;
    }
    /**
     * Присоединяется к серверу, используя имя соединения $connect_name, ранее определенное методом {@link config()}.
     * 
     * @see config()
     * @param string $connect_name имя соединения
     * @return bool true - все прошло успешно, false - ошибка 
     */
    private function _connect()
    {
        if (!$this->_db_link) {
            // Mark start time
            $log_params = MyDB::enable_query_log();
            if ($log_params && $log_params[2]) {
                $start_run_time = explode(" ", microtime(1));
                $start_run_time = $start_run_time[0] + $start_run_time[1];
            }

            $link = $this->__connect($this->_db_host, $this->_db_user, $this->_db_passwd, $this->_db_name);

            // Mark end time
            if ($log_params && $log_params[2]) {
                $end_run_time = explode(" ", microtime(1));
                $end_run_time = $end_run_time[0] + $end_run_time[1];
            }
            ///////////////////////////////////////////////////////////////////////////////
            // Logging
            ///////////////////////////////////////////////////////////////////////////////
            if (MyDB::enable_query_log()) {
                $this->_log_connect($this->_db_user, $this->_db_name, $start_run_time, $end_run_time);
            }
            ///////////////////////////////////////////////////////////////////////////////
            if ($link) {
                $this->_db_link = $link;
                $this->select_schema(reset($this->_schemas));
                return true;
            } else {
                $this->_error("Unable to connect to ".(MyDB::__rdbms())." server!", E_USER_WARNING);
                return false;
            }
        }
    }

    private function _is_connect_name_exists($connect_name)
    {
        $connect_data =& MyDB::config($connect_name);
        if (!$connect_data) {
            MyDB::_error("Unable to connect to ".(MyDB::__rdbms())." server: unknown connect name \"$connect_name\"", E_USER_WARNING);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @static
     */
    private static function _db_error($flag = null)
    {
        static $db_error;
        if (is_null($flag)) {
            return $db_error;
        }
        $db_error = $flag;
    }
    /**
     * последний запрос к БД
     */
    private static $_last_query = "";
    /**
     * Последняя ошибка
     */
    private static $_last_error = "";
    /**
     * Флаг, говорящий о том, экранированы ли
     * кавычки в передаваемых в запрос параметрах или нет.
     */
    private static $_params_already_quoted = false;

    /**
     * Возвращает ссылку на объект класса (синглетон).
     * 
     * Если объект не создан, создает его.
     * Удобно использовать внутри других классов и функций, не используя глобальной переменной
     * для хранения объекта:
     * 
     * <code>
     * function some_func()
     * {
     *     $db =& MyDB::get_instance("main");
     * }
     * </code>
     * 
     * Объект создается один раз при первом вызове метода, и в дальнейшем, обращение происходит
     * к одному и тому же объекту в памяти без использования глобальной переменной.
     * Один объект работает только с одним подключением.
     * Соединение с сервером происходит в момент создания объекта, т.е. при первом вызове метода.
     * 
     * @see config()
     * 
     * @param string $connect_name имя коннекта (см. {@link config()})
     * @return object ссылка объект класса
     * @static 
     */
    public static function &get_instance($connect_name = null)
    {
        
        static $instance;
        if (is_null($connect_name)) {
            $connects =& MyDB::config();
            if (is_array($connects)) {
                if (count($connects) == 1) {
                    $connect_name = key($connects);
                } else {
                    MyDB::_error('MyDB has more than one configured connections. Missing argument 1 for get_instance()');
                    return null;
                }
            }
        }
        if (!isset($instance[$connect_name])) {
            if ($connect_name && MyDB::_is_connect_name_exists($connect_name)) {
                $connect_data =& MyDB::config($connect_name);
                $instance[$connect_name] = new MyDB($connect_name, $connect_data);
            } else {
                return null;
            }
        }
       
        return $instance[$connect_name];
        
    }

    /**
     * Устанавливает или сбрасывает внутренний обработчик ошибок.
     * 
     * @param bool $enable true - установить, false - сбросить
     * @static
     */
    public function reset_error_handler($enable = null)
    {
        static $reset;
        if (is_null($enable)) {
            return !is_null($reset) ? $reset : true;
        }
        $reset = $enable;
    }

    /**
     * Возвращает последний посланный запрос к БД
     * 
     * @return string запрос в том виде, в котором он ушел на сервер
     */
    public function get_last_query()
    {
        return $this->_last_query;
    }

    /**
     * Возвращает последнюю ошибку при выполнении запроса
     * 
     * @return string сообщение об ошибке
     */
    public function get_last_error()
    {
        return $this->_last_error;
    }

    /**
     * Указывает методам, принимающим в качестве аргументов запрос с параметрами
     * ({@link query()}, {@link set()}, {@link insert()}, {@link update()} и др.), экранированы ли значения параметров во внейшней среде или нет
     * (например, в случае включенного флага конфигурации magic_quotes_gpc, когда праметрами служат данные,
     * пришедшие из $_GET, $_POST, $_COOKIE (GPC)).
     * 
     * Значения параметров будут экранироваться в любом случае, не зависимо от того, с каким значением аргумента вызывался этот метод
     * и в каком положении находится переключатель magic_quotes_gpc. Данный метод лишь сообщяет вышеупомянутым методам,
     * как правильно воспринять входные параметры запроса, чтобы избежать "двойных слешей" (двойного экранирования).
     * 
     * В случае, когда данный метод вызывается с аргументом $flag, равным true, заставляющим "думать" методы составления запроса,
     * что параметры уже экранированы, при приеме параметров сначала для каждого из них выполнятся функция stripslashes(),
     * а потом снова происходит экранирование. Совсем не "трогать" параметры нельзя, так как может возникнуть ситуация,
     * когда в запрос попадут неэкранированные значения параметров в случае неверного вызова метода
     * (параметры в действительности не экранированы, а этот метод сообщил об обратном). В этой ситуации могут пострадать лишь значения
     * параметров (удалятся неэкранирующие слеши), но экранирование в любом случае произойдет.  
     * 
     * По умолчанию, все методы, принимающие параметры запросов, "думают", что значения параметров не экранированы.
     * 
     * @param bool $flag true - параметры уже экранированы
     * @static
     */
    public function params_quoted($flag = null)
    {
        static $_params_already_quoted;
        if (is_null($flag)) {
            return !is_null($_params_already_quoted) ? $_params_already_quoted : false;
        }
        $_params_already_quoted = $flag;
    }

    /**
     * Конфигурирует класс.
     * 
     * Метод добавляет информацию о соединениях в класс для дальнейшего их использования
     * для создания объектов (см. {@link get_isntance()}).
     * Этот метод позволяет в последствии обращатся к информации о соединении по одному имени.
     * 
     * <code>
     *     $connect_data = array(
     *         'host'      => DB_SYSTEM_HOST
     *         , 'user'    => DB_SYSTEM_USER
     *         , 'passwd'  => DB_SYSTEM_PASS
     *         , 'db_name' => DB_SYSTEM_DBNAME
     *     );
     *     MyDB::config("system", $connect_data);
     *     $db =& MyDB::get_instance("system"); // здесь происходит соединение с сервером
     *     ...
     *     $db->close();
     * </code>
     * 
     * @see connect()
     * 
     * @param string $connect_name имя, под которым сохраняется информация (имя коннекта).
     * @param array  $connect_data информация о соединении
     * @static
     */
    public function &config($connect_name = null, $connect_data = null)
    {
        static $config;

        if (is_null($connect_data)) {
            if (!is_null($connect_name)) {
                return $config[$connect_name];
            } else {
                return $config;
            }
        }

        $config[$connect_name] = $connect_data;
    }

    /**
     * Добавляет информацию о схемах или БД (в зависимости от типа СУБД).
     *
     * Метод дает "свои" (упрощенные, удобные для последующего обращения) имена БД или схемам.
     * Реальные (физические) имена БД/схем могут быть неудобным для использования, когда работа постоянно ведется
     * с несколькими БД/схемами. Вместо физических имен, все методы класса могут использовать имена БД/схем, назначенные этой функцией.
     * Конечно же, упрощенное имя может совпадать с физическим.
     * Если назначения новых имен не происходило (данный методод не был вызван), то работа ведется с физическими именами.
     *   
     * Для СУБД, поддерживающих схемы, происходит добавление информации о схемах (PostgreSQL), в противном случае о БД (MySQL).
     * Во втором случае ображение к БД происходит как обращение к схеме, т.к. СУБД, не использующие схем обычно присоединяются
     * к хосту, а не к БД. Это дает возможность выбирать текущую БД также как текущую схему внутри БД.
     * Короче говоря, в терминологии этого класса нет разницы между базой данных и схемой. Что именно в действительности используется
     * зависит от типа СУБД. Примеры:
     * 
     * <code>
     *     $db = new MyDB();
     *     // имя БД НЕ указыается 5-м параметром
     *     $db->add_connect_data("main", "localhost", "root", "");
     *     $db->add_schema("main", "log", "log_db"); // это имя БД
     *     $db->connect("main");
     *     // выбираем БД с физическим именем "log_db" как текущую.
     *     $db->select_schema("log");
     *     ...
     *     $db->close("main");
     * </code>
     * 
     * <code>
     *     $db = new MyDB();
     *     // имя БД указыается, так как СУБД требует соединения именно с БД
     *     $db->add_connect_data("main", "localhost", "root", "", "portal");
     *     // здесь "log_schema" - это имя схемы внутри БД "portal"
     *     $db->add_schema("main", "log", "log_schema");
     *     $db->connect("main");
     *     $db->select_schema("log"); // выбираем текущую схему
     *     ...
     *     $db->close("main");
     * </code>
     * 
     * @see select_schema()
     * 
     * @param string $schema       имя схемы/БД в объекте (упрощенное имя, используемое в объекте, для упрощения обращения к схеме/БД);
     *    реальное имя схемы/БД может быть неудобным для использования в дальнейшем, когда работа постоянно ведется с разными схемами/БД
     * @param string $real_schema  реальное имя схемы/БД
     */
    public function add_schema($schema, $real_schema)
    {
        $this->_schemas[$schema] = $real_schema;
    }

    /**
     * Выбирает текущую схему или БД в текущем соединении.
     * 
     * @see add_schema()
     * @param string $schema упрощенное имя схемы/БД, заданное ранее с помощью {@link add_schema()}
     * @param bool   $_change_cur_schema закрытый параметр; используется только внутри класса
     * @return string упрощенное имя (см. {@link add_schema()}) предыдущей схемы/БД (той, которая была текущей до вызова метода)
     */
    public function select_schema($schema, $_change_cur_schema = true)
    {
        $this->_connect();
        $schema = $this->_get_real_schema($schema);
        if ($schema == $this->get_current_schema()) {
            return $schema;
        }
        
        // Mark start time
        $log_params = MyDB::enable_query_log();
        if ($log_params && $log_params[2]) {
            $start_run_time = explode(" ", microtime(1));
            $start_run_time = $start_run_time[0] + $start_run_time[1];
        }

        $res = @$this->__select_schema($schema, $this->_db_link);

        // Mark end time
        if ($log_params && $log_params[2]) {
            $end_run_time = explode(" ", microtime(1));
            $end_run_time = $end_run_time[0] + $end_run_time[1];
        }
        ///////////////////////////////////////////////////////////////////////////////
        // Logging
        ///////////////////////////////////////////////////////////////////////////////
        if (MyDB::enable_query_log()) {
            $this->_log_selecting_schema($schema, $start_run_time, $end_run_time);
        }
        ///////////////////////////////////////////////////////////////////////////////
        if (!$res) {
            MyDB::_error("Error on selecting schema \"$schema\": " . $this->__error($this->_db_link));
            return false;
        }
        $old_schema = $this->_cur_schema;
        if ($_change_cur_schema) {
            $this->_cur_schema = $schema;
        }
        return $old_schema ? $old_schema : true;
    }

    /**
     * Закрытие соединения с серевером БД.
     * @static
     */
    public function close()
    {
        $this->__close($this->_db_link);
        $this->_db_link = null;
    }
    ///////////////////////////////////////////////////////////////////////////////
    /**
     * Выполнение запроса к БД.
     * 
     * Формирует запрос к БД на основе строки запроса с параметрами (placeholders) и самих параметров.
     * (см. {@link params_quoted()}). Параметр $params может быть как ассоциативным массиом (параметр => значение),
     * так и просто значением, в случае, когда в запрос требуется вставить только один параметр.
     * Имена параметров (placeholders) в строке запроса обрабляются с обоих сторон знаком "%".
     * Примеры использования:
     * 
     * <code>
     *     ...
     *     $params = array(
     *         "foo1" => "'qwerty'",
     *         "foo2" => 3
     *     );
     *     $db->query("SELECT * FROM foo WHERE foo1 = %foo1% AND foo2 = %foo2%", $params);
     *     // результирующий запрос:
     *     // SELECT * FROM foo WHERE foo1 = '\'qwerty\'' AND foo2 = '3'
     *     ...
     *     $foo_id = 12;
     *     $db->query("SELECT field FROM foo WHERE foo_id = %id%", $foo_id);
     *     // результирующий запрос:
     *     // SELECT field FROM foo WHERE foo_id = '12'
     *     ...
     * </code>
     * 
     * В строке запроса допустимо явное указание принадлежности таблицы к той или иной БД/схеме (см. {@link add_schema()}).
     * Имеется ввиду, что имя БД/схемы задается как упрощенное имя, установленное ранее с помощью {@link add_schema()}
     * (указание физического имени схемы/БД как "имя_бд.имя_таблицы" никто не отменял). Выбрать текущую схему/БД для запроса
     * можно также и с помощью параметра $schema. Разница состоит лишь в том, что в последнем случае перед
     * и после выполнения запроса вызывается функция {@link select_schema()} для назначения текущей схемы/БД, а также
     * для возврата к схеме/БД, которая была текущей до вызова этого метода. Второй способ работает медленнее, но в некоторых случаях
     * удобнее (например, когда в запросе участвуют несколько таблиц из одной схемы/БД).
     * Пример:
     * 
     * <code>
     *     ...
     *     $db->add_schema("main", "db00645");
     *     $db->add_schema("log", "db00645_log");
     *     $db->select_schema("main");
     *     $db->query("SELECT * FROM #main#.foo");
     *     // результирующий запрос:
     *     // SELECT * FROM db00645.foo
     *     ...
     *     $db->query("SELECT * FROM #log#.foo");
     *     // результирующий запрос:
     *     // SELECT * FROM db00645_log.foo
     *     // равносильный вызов метода:
     *     // $db->query("SELECT * FROM foo", null, null, "log");
     *     ...
     * </code>
     * 
     * Преимущества передачи параметров как ассоциативных массивов перед простым перечислением значений в аргументах функции
     * (что часто встречается в других классах работы с БД):
     *  - не имеет значения порядок, в котором указываются параметры как в строке запроса так и в массиве значений параметров;
     *  - вызов метода выглядит компактнее;
     *  - довольно часто при программировании мы имеем дело с готовыми (не создаваемыми специально для запроса)
     *    ассоциативными массивами данных (например, $_POST и $_GET), элементы которых могут смело выступать
     *    в качестве параметров запроса (при совпадении имен ключей с именами параметров)
     * 
     * @param string $query        запрос с параметрами (placeholders)
     * @param mixed  $params       параметры запроса (см. {@link params_quoted()})
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     * 
     * @return resource результат запроса
     */
    public function &query($query, $params = null, $schema = null)
    {
        //////////////////////////////
        // Временный кусок кода.
        // Прописан из-за смены количества параметров метода.
        // (раньше был $remove_tags)
        //////////////////////////////
        if ($schema && is_bool($schema)) {
            $this->_error("Argument \$remove_tags is deprecated!", E_USER_WARNING);
        }
        //////////////////////////////

        $this->_connect();
        
        if ($schema) {
            $old_schema = $this->_cur_schema;
            $this->select_schema($schema, true);
        }

        $query = $this->get_query($query, $params);

        $this->_last_query = $this->__log_selecting_schema($this->_get_real_schema($schema ? $schema : $this->_cur_schema)).";\n".$query;

        // Mark start time
        $log_params = MyDB::enable_query_log();
        if ($log_params && $log_params[2]) {
            $start_run_time = explode(" ", microtime(1));
            $start_run_time = $start_run_time[0] + $start_run_time[1];
        }

        $result = $this->__query($query, $this->_db_link);

        // Mark end time
        if ($log_params && $log_params[2]) {
            $end_run_time = explode(" ", microtime(1));
            $end_run_time = $end_run_time[0] + $end_run_time[1];
        }

        if ($result === false) {
            $error = trim(MyDB::__error($this->_db_link));
            $this->_last_error = $error;
            $m = array();
            preg_match("/(?:\'|\")(.*)(?:\'|\")/U", $error, $m);
            if ($m[1]) {
                $error = preg_replace("/(".preg_quote($m[1]).")/U", "<font color='red'><b>\\1</b></font>", str_replace("\n", "", $error));
                $query = preg_replace("/(".preg_quote($m[1]).")/U", "<b>\\1</b>", $query);
            }
            $error_text = "SQL error: $error in query<br>\n";
            $error_text .= "<font color=\"red\">".nl2br($query)."</font>";
            $this->_error($error_text, E_USER_WARNING);
        } else {
            ////////////////////////////////////////////////////
            // Logging
            ////////////////////////////////////////////////////
            if (MyDB::enable_query_log()) {
                $this->_log_query($query, "normal", $start_run_time, $end_run_time);
            }
            ////////////////////////////////////////////////////
        }
        
        if ($schema) {
            $this->select_schema($old_schema, true);
        }
        return $result;
    }

    /**
     * Вставка строки в таблицу.
     * Пример:
     * 
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $db->insert("foo", $data);
     *     // результирующий запрос:
     *     // INSERT INTO foo (foo1, foo2) VALUES('text', '5')
     *     ...
     * </code>
     * 
     * С помощью этого метода удобно записывать в таблицу данные, пришедшие с формы, если имена полей формы совпадают
     * с именами полей таблицы:
     * 
     * <code>
     *     $db->insert("foo", $_POST);
     * </code>
     * 
     * @param string $table        имя таблицы
     * @param array  $params       ассоциативный массив ("поле" => "значение") данных для вставки
     *                             (подробнее о параметрах см. {@link query()}, {@link params_quoted()}) 
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     * 
     * @param string $returning_field - поле, которое надо вернуть апосля вставки. (ex. - id)
     * @return resource результат запроса
     */
    public function insert($table, $params, $schema = null, $returning_field = null)
    {
        $insert_fields = "";
        $insert_params = "";
        $values = array();
        foreach($params as $key => $value) {
            $insert_fields .= $this->_field_quote.$key.$this->_field_quote.", ";
            $insert_params .= $this->_param_char.$key.$this->_param_char.", ";
            $values[$key] = $value;
        }
        $insert_fields = substr($insert_fields, 0, strlen($insert_fields) - 2);
        $insert_params = substr($insert_params, 0, strlen($insert_params) - 2);
        if ($returning_field == null){
            $return = "";
        }else{
            $return = " returning ".$returning_field;
        }
        $res = $this->query("INSERT INTO $table (\n\t$insert_fields\n) VALUES (\n\t$insert_params\n)".$return, $values, $schema);
        return $res;
    }

    /**
     * Обновление строк в таблице.
     * 
     * Метод составляет запрос из переданных ему параметров и посылает его в БД на выполнение.
     * 
     * Пример 1.
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $where = array(
     *         "foo1_id" => 1,
     *         "foo2_id" => 5,
     *     );
     *     // $where - массив
     *     $db->update("foo", $data, $where);
     *     // результирующий запрос:
     *     // UPDATE foo SET foo1 = 'text', foo2 = '5' WHERE foo1_id = '1' AND foo2_id => '5'
     *     ...
     * </code>
     *
     * Пример 2. 
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $where_params = array(
     *         "foo1" => "text2",
     *         "foo2" => 1
     *     );
     *     // $where - строка с несколькими параметрами
     *     $db->update("foo", $data, "foo1 = %foo1% OR foo2 = %foo2%", $where_params);
     *     // результирующий запрос:
     *     // UPDATE foo SET foo1 = 'text', foo2 = '5' WHERE foo1 = 'text2' OR foo2 = '1' 
     *     ...
     * </code>
     *
     * Пример 3. 
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $where_param = 7;
     *     // $where, строка с одним параметром
     *     $db->update("foo", $data, "foo_id = %id%", $where_param);
     *     // результирующий запрос:
     *     // UPDATE foo SET foo1 = 'text', foo2 = '5' WHERE foo_id = '7' 
     *     ...
     * </code>
     * 
     * @param string $table        имя таблицы
     * @param array  $params       ассоциативный массив ("поле" => "значение") данных обновления
     *                             (подробнее о параметрах см. {@link query()}, {@link params_quoted()})
     * @param mixed  $where        условие WHERE для UPDATE
     *                             (может быть как ассоциативным массивом для склейки по AND, так и строкой запроса с параметрами,
     *                             значения которых берутся из $where_params)  
     * @param mixed  $where_params ассоциативный массив параметров или одно значение параметра выражения $where;
     *                             имеет смысл указывать только, когда $where - строка с параметрами 
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     * 
     * @return resource результат запроса
     */
    public function update($table, $params, $where = null, $where_params = array(), $schema = null)
    {
        $update_set = "";
        $update_values = array();
        foreach($params as $key => $value) {
            $m = array();
            if (ereg("(.+)(\[[1-9]+\])", $key, $m)) {
                $update_set .= $this->_field_quote.$m[1].$this->_field_quote.$m[2]." = ".$this->_param_char.$key.$this->_param_char.", ";
            }
            else $update_set .= $this->_field_quote.$key.$this->_field_quote." = ".$this->_param_char.$key.$this->_param_char.", ";
            $update_values[$key] = $value;
        }
        $update_set = substr($update_set, 0, strlen($update_set) - 2);

        if (is_array($where)) {
            $where_str = "";
            foreach($where as $key => $value) {
                if (ereg("(.+)(\[[1-9]+\])", $key, $m)) {
                    $where_str .= $this->_field_quote.$m[1].$this->_field_quote.$m[2]." ".($value?"=":"IS")." ".$this->_param_char.$key.$this->_param_char." AND ";
                } else {
                    $where_str .= $this->_field_quote.$key.$this->_field_quote." ".($value?"=":"IS")." ".$this->_param_char.$key.$this->_param_char." AND ";
                }
            }
            $where_str = substr($where_str, 0, strlen($where_str) - 5);
        } elseif (!is_array($where_params)) {
            if (preg_match_all("/".$this->_param_char."(.+)".$this->_param_char."/U", $where_params, $m)) {
                $placeholders = array_unique($m[1]);
                $where_params = array($placeholders[0] => $where_params);
            }
        }

        $part1 = $this->get_query(($where ? (is_array($where) ? "\nWHERE \n\t$where_str" : "\nWHERE \n\t$where") : ""), (is_array($where) ? $where : $where_params));
        $part2 = $this->get_query("UPDATE $table SET \n\t$update_set ", $update_values);
        $res = $this->query($part2.$part1, null, $schema);
        if (!$res) {
            MyDB::_db_error(true);
        }
        return $res;
    }

    /**
     * Удаление строк таблицы.
     * 
     * @param string $table        имя таблицы
     * @param mixed  $where        условие WHERE для DELETE
     *                             (может быть как ассоциативным массивом для склейки по AND, так и строкой запроса с параметрами,
     *                             значения которых берутся из $where_params)  
     * @param mixed  $where_params ассоциативный массив параметров или одно значение параметра выражения $where;
     *                             имеет смысл указывать только, когда $where - строка с параметрами 
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     * 
     * @return resource результат запроса
     */
    public function delete($table, $where = "", $where_params = array(), $schema = null)
    {
        if (is_array($where)) {
            $where_str = "";
            foreach($where as $key => $value) {
                $m = array();
                if (ereg("(.+)(\[[1-9]+\])", $key, $m)) {
                    $where_str .= $this->_field_quote.$m[1].$this->_field_quote.$m[2]." ".($value?"=":"IS")." ".$this->_param_char.$key.$this->_param_char." AND ";
                } else {
                    $where_str .= $this->_field_quote.$key.$this->_field_quote." ".($value?"=":"IS")." ".$this->_param_char.$key.$this->_param_char." AND ";
                }
            }
            $where_str = substr($where_str, 0, strlen($where_str) - 5);
        } elseif (!is_array($where_params)) {
            $m = array();
            if (preg_match_all("/".$this->_param_char."(.+)".$this->_param_char."/U", $where_params, $m)) {
                $placeholders = array_unique($m[1]);
                $where_params = array($placeholders[0] => $where_params);
            }
        }

        $res = $this->query("DELETE FROM $table ".($where ? (is_array($where) ? "\nWHERE \n\t$where_str" : "\nWHERE \n\t$where") : ""), is_array($where) ? $where : $where_params, $schema);
        return $res;
    }

    /**
     * Возвращает первую строку запроса как ассоциативный массив.
     * 
     * @see query()
     * 
     * @param string $query        запрос с параметрами (placeholders)
     * @param array  $params       параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     * 
     * @return array строка запроса в виде ассоциативного массива ("атрибут" => "значение")
     */
    public function &get_first($query, $params = null, $schema = null)
    {
        $res =& $this->query($query, $params, $schema);
        if (!$res) {
            MyDB::_db_error(true);
        } else {
            $row = $this->__fetch_assoc($res);
            $this->__free_result($res);
        }
        return $row;
    }

    /**
     * Включает/выключает ведение логов ошибок выполнения запросов.
     * 
     * @param string $log_file имя файла лога; если false, выключает ведение логов
     * @static
     */
    public function enable_error_log($log_file = null)
    {
        static $_log_file;
        if (is_null($log_file)) {
            return $_log_file;
        }
        $_log_file = $log_file;
    }
    
    /**
     * Включает ведение логов запросов.
     * 
     * @param string $log_file   имя файла лога; если false, выключает ведение логов
     * @param bool $only_updates true - вести лог только изменений БД
     * @param bool $verbose      true - выводить в лог дополнительную информацию
     *                           (имя скрипта, из которого был вызван запрос, номер строки в файле скрипта и т.п.)
     * @static
     */
    public function enable_query_log($log_file = null, $only_updates = false, $verbose = false)
    {
        static $_log_file;
        static $_only_updates;
        static $_verbose;

        if (is_null($log_file)) {
            return $_log_file ? array($_log_file, $_only_updates, $_verbose) : false;
        }

        $_log_file     = $log_file;
        $_verbose      = $verbose;
        $_only_updates = $only_updates;
    }
    
    /**
     * Начинает транзакцию.
     */
    public function begin()
    {
        $res = $this->query("BEGIN");
        if (!$res) {
            MyDB::_db_error(true);
            return false;
        }
    }

    /**
     * "Закрепляет" транзакцию.
     */
    public function commit()
    {
        $res = $this->query("COMMIT");
        if (!$res) {
            MyDB::_db_error(true);
            return false;
        }
    }

    /**
     * Отменяет текущую транзакцию.
     */
    public function rollback()
    {
        $res = $this->query("ROLLBACK");
        if (!$res) {
            MyDB::_db_error(true);
            return false;
        }
    }
    
    /**
     * Устанавливает границу начала отлова ошибок.
     * 
     * Удобно использовать при выполнении группы запросов для отлова ошибок их выполнения.
     * Чтобы не проверять каждый запрос на корректное выполнение, можно установить в начале группы
     * границу начала отлова ошибок, а затем в конце группы проверить методом {@link error_occured()},
     * произошла ли ошибка при выполнении какого-нибудь из запросов группы или нет.
     * Пример:
     * 
     * <code>
     *     $db->catch_error();
     * 
     *     $db->update("foo", $data);
     *     $db->insert("foo2", $data2);
     *     $db->query("SELECT * FROM foo");
     * 
     *     if ($db->error_occured()) {
     *         echo "Ошибка!";
     *     }
     * </code> 
     * 
     * @see error_occured()
     * @static
     */
    public function catch_error()
    {
        MyDB::_db_error(false);
    }

    /**
     * Возвращает true, если произошла ошибка в группе запросов (после вызова {@link catch_error()}).
     * 
     * @see catch_error()
     * @static
     */
    public function error_occured()
    {
        return MyDB::_db_error();
    }
    
    
    /**
     * То же, что и {@link fetch_all() fetch_all($res, DB_FETCH_ALL_VECTOR)}.
     * Отличие в том, что первым параметром может выступать как ресурс (результат запроса),
     * так и строка самого запроса.
     *
     * @param string|resource $query   запрос с параметрами (placeholders) или результат запроса
     * @param array           $params  параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     */
    public function &get_vector($query, $params = null, $schema = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $schema);
        }
        if (!$res) {
            MyDB::_db_error(true);
            return false;
        }
        $ret =& $this->fetch_all($res, DB_FETCH_ALL_VECTOR);
        if (!is_resource($query)) {
            $this->__free_result($res);
        }
        return $ret;
    }

    /**
     * То же, что и {@link fetch_all() fetch_all($res, DB_FETCH_ALL_INDEX_KEYS)}.
     * Отличие в том, что первым параметром может выступать как ресурс (результат запроса),
     * так и строка самого запроса.
     *
     * @param string|resource $query   запрос с параметрами (placeholders) или результат запроса
     * @param array           $params  параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     */
    public function &get_list($query, $params = null, $schema = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $schema);
        }
        if (!$res) {
            MyDB::_db_error(true);
            return false;
        }
        $ret =& $this->fetch_all($res, DB_FETCH_ALL_INDEX_KEYS);
        if (!is_resource($query)) {
            $this->__free_result($res);
        }
        return $ret;
    }

    /**
     * То же, что и {@link fetch_all() fetch_all($res, DB_FETCH_ALL_VALUE_KEYS)}.
     * Отличие в том, что первым параметром может выступать как ресурс (результат запроса),
     * так и строка самого запроса.
     *
     * @param string|resource $query     запрос с параметрами (placeholders) или результат запроса
     * @param array           $params    параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string          $key_field ключевое поле
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     */
    public function &get_key_list($query, $params = null, $key_field = null, $schema = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $schema);
        }
        if (!$res) {
            MyDB::_db_error(true);
            return false;
        }
        $ret =& $this->fetch_all($res, DB_FETCH_ALL_VALUE_KEYS, $key_field);
        if (!is_resource($query)) {
            $this->__free_result($res);
        }
        return $ret;
    }

    /**
     * Вытаскивает все строки из результата SELECT запроса.
     * 
     * Есть несколько типов извлечения строк в массив.
     * 
     * 1. Рузультат: массив, каждая строка которого - ассоциативный массив вида "поле" => "значение".
     * <code>
     *     // Таблица foo:
     *     // +-------------+-----------+
     *     // | id |  nick  | full_name |
     *     // +-------------+-----------+
     *     // | 10 | john   | John Doe  |
     *     // | 11 | cat    | Katrina   |
     *     // +-------------+-----------+
     * 
     *     $res = $db->query("SELECT id, nick, full_name FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_INDEX_KEYS);
     * 
     *     // $data = array(
     *     //     [0] => array(
     *     //         "id" => 10,    
     *     //         "nick" => "john",    
     *     //         "full_name" => "John Doe"
     *     //     ),
     *     //     [1] => array(
     *     //         "id" => 11,    
     *     //         "nick" => "cat",    
     *     //         "full_name" => "Katrina"
     *     //     )
     *     // )
     *     ...
     * </code>
     *  
     * 2. Результат: массив, каждая строка которого - ассоциативный массив вида "поле" => "значение".
     *    Значения поля результата, указанного в $column, участвуют в образовании ключей массива строк
     *    (в самой строке значение ключевого поля уже не присутствует).
     * <code>
     *     $res = $db->query("SELECT id, nick, full_name FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_VALUE_KEYS);
     * 
     *     // $data = array(
     *     //     [10] => array(
     *     //         "nick" => "john",    
     *     //         "full_name" => "John Doe"
     *     //     ),
     *     //     [11] => array(
     *     //         "nick" => "cat",    
     *     //         "full_name" => "Katrina"
     *     //     )
     *     // ) 
     *     ...
     * </code>
     *  
     * 3. Результат: одномерный массив типа "значение 1-го поля" => "значение 2-го поля".
     *    Если результат содержит только один столбец, ключи задаются автоматически.
     * <code>
     *     $res = $db->query("SELECT id, nick FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_VECTOR);
     *     // array(
     *     //     [10] => "john",
     *     //     [11] => "cat"
     *     // )
     * 
     *     // когда только одно поле
     *     $res = $db->query("SELECT nick FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_VECTOR);
     *     // array(
     *     //     [0] => "john",
     *     //     [1] => "cat"
     *     // )
     *     ...
     * </code>
     * 
     * @param resource $res         Результат запроса (возвращается методами {@link set()}, {@link query()}).
     * @param int      $result_type Вид полученного в результате массива<br>
     *     Возможные значения $result_type:<br>
     *     DB_FETCH_ALL_INDEX_KEYS   - двумерный массив, каждая строка которого -
     *        ассоциативный массив вида "поле" => "значение";<br>
     *     DB_FETCH_ALL_VALUE_KEYS - такой же, как DB_FETCH_ALL_INDEX_KEYS,
     *        но значения поля результата, указанного в $column, участвуют в образовании ключей массива строк;<br>
     *     DB_FETCH_ALL_VECTOR     - одномерный массив вида "значение_первого_поля" => "значение_второго_поля".
     * @param string $key_field     Поле, значения которого участвуют в образовании ключей, когда
     *     аргумент $result_type принимает значение DB_FETCH_ALL_VALUE_KEYS; если не указан, то берутся
     *     значения первого столбца результата.
     * 
     * @return array рузультат в виде массива
     */
    public function &fetch_all(&$res, $result_type = DB_FETCH_ALL_INDEX_KEYS, $key_field = null)
    {
        if ($res) {
            // сбрасываем указатель в результате на начало
            $this->__data_seek($res, 0);    
            if (is_int($result_type)) {
                $data = array();
                $i = 0;
                while ($row = $this->__fetch_assoc($res)) {
                    if ($result_type == DB_FETCH_ALL_INDEX_KEYS) {
                        $data[$i] = $row;
                    }
                    if ($result_type == DB_FETCH_ALL_VALUE_KEYS) {
                        if (!$key_field) {
                            $keys = array_keys($row);
                            $key_field = $keys[0];
                        }
                        $data[$row[$key_field]] = $row;
                        unset($data[$row[$key_field]][$key_field]);
                    }
                    if ($result_type == DB_FETCH_ALL_VECTOR) {
                        $keys = array_keys($row);
                        if ($keys[1]) {
                            $data[$row[$keys[0]]] = $row[$keys[1]];
                        } else {
                            $data[] = $row[$keys[0]];
                        }
                    }
                    $i++;
                }
                return $data;
            } else {
                $this->_error("Unknown result type: ".$result_type);
                return false;
            }
        } else {
            $this->_error("First argument in get_data() is not a valid query result!");
            return false;
        }
    }

   
    /**
     * Возвращает сгенерированный запрос без его выполнения.
     * 
     * @param string $query        запрос с параметрами (placeholders)
     * @param array  $params       параметры запроса (см. {@link query()}, {@link params_quoted()})
     * 
     * @return string рузультирующий запрос
     */
    public function get_query($query, $params)
    {
        $m = array();
        if (preg_match_all("/".$this->_param_char."([0-9a-zA-Z_.]+)".$this->_param_char."/U", $query, $m)) {
            $placeholders = array_unique($m[1]);
            if (!is_array($params)) {
                $params = array($placeholders[0] => $params);
            }
            foreach($placeholders as $value) {
                $key = $value;
                $value = $params[$key];
//                $value = str_replace("ё", "е", $value);
//                $value = str_replace("Ё", "Е", $value);
                if ($this->_params_already_quoted) {
                    $value = stripslashes($value);
                }
                $value = $this->__escape_string($value."");
                $value = str_replace("-", "-", $value);
                if (!is_numeric($value) && $value == "") {
                    $value = "NULL";
                } else {
                    $value = "'$value'";
                }

                $query = str_replace($this->_param_char."$key".$this->_param_char, $value, $query);
            }
        }

        foreach ($this->_schemas as $key => $value) {
            $query = str_replace($this->_schema_char.$key.$this->_schema_char, $value, $query);
        }
        return $query;
    }
    
    /**
     * Высвобождает памать, отведенную под результат запроса.
     * 
     * @param resource $res рузультат запроса
     */
    public function free_result(&$res)
    {
        if ($res) {
            $this->__free_result($res);
        } else {
            $this->_error("First argument in free_result() is not a valid query result!");
            return false;
        }
    }
    
    /**
     * Возвращает следующую строку результата запроса как одномерный массив.
     * 
     * @param resource $res Рузультат запроса.
     * @param int $fetch_mode вид полученного в результате массива;<br>
     *     DB_FETCH_ASSOC - ассоциативный массив,<br>
     *     DB_FETCH_NUM   - массив с числовыми ключами.
     */
    public function &fetch_row(&$res, $fetch_mode = DB_FETCH_ASSOC)
    {
        if ($res) {
            switch ($fetch_mode) {
                case DB_FETCH_ASSOC:
                    $row = $this->__fetch_assoc($res);
                    break;
                case DB_FETCH_NUM:
                    $row = $this->__fetch_num($res);
                    break;
            }
            return $row;
        } else {
            $this->_error("First argument in fetch_row() is not a valid query result!");
            return false;
        }
    }
    
    /**
     * Возвращает значение первого столбца первой строки запроса.
     * 
     * @see query()
     * 
     * @param string $query        запрос с параметрами (placeholders); может быть результатом запроса
     * @param array  $params       параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string $schema       имя схемы/БД для запроса (см. {@link add_schema()})
     */
    public function get_one($query, $params = null, $schema = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $schema);
        }
        if (!$res) {
            MyDB::_db_error(true);
            return false;
        }
        $row = $this->fetch_row($res, DB_FETCH_NUM);
        if (!is_resource($query)) {
            $this->__free_result($res);
        }
        return $row[0];
    }

    /**
     * Устанавливает внутренний указатель рузультата запроса на первую строку.
     * 
     * @param resource $res результат запроса
     */
    public function result_reset(&$res)
    {
        if ($res) {
            $this->__data_seek($res, 0);    
        } else {
            $this->_error("First argument in result_reset() is not a valid query result!");
            return false;
        }
    }
    
    /**
     * Возвращает количество строк результата запроса.
     * 
     * @param resource $res результат запроса
     * @return int количество строк результата
     */
    public function num_rows(&$res)
    {
        return $this->__num_rows($res);
    }
    
    /**
     * Возвращает количество измененных записей последнего запроса UPDATE/DELETE.
     * 
     * @param resource $res результат запроса
     * @return int количество строк результата
     */
    public function affected_rows($res = null)
    {
        return $this->__affected_rows($res);
    }

    /**
     * Экранирует содержимое переменной.
     * 
     * Может пригодится в том случае, когда нужно вставить переменную непосредственно в строку запроса (не прибегая к использованию
     * параметров). $var может быть массивом. В этом случае экранируется все содержимое массива.
     * Пример:
     * 
     * <code>
     *     $var = "qwer'ty";
     *     escape_var($var);
     *     $db->query("SELECT * FROM foo WHERE foo = '$var'");
     *     // SELECT * FROM foo WHERE foo = 'qwer\'ty'; 
     *     ...
     * </code>
     * 
     * Однако не рекомендуется использовать такой подход к построению запросов.
     * Используйте параметризованные запросы (см. {@link query()}).
     * 
     * @see params_quoted()
     * 
     * @param mixed $var ссылка на переменную, значение которой нужно экранировать
     * @return mixed экранированная переменная
     */
    public function escape_var(&$var)
    {
        if (is_array($var)) {
            foreach ($var as $key => $value) {
                $this->escape_var($var[$key]);
            }
        } else {
            if ($var) {
//                $var = str_replace("ё", "е", $var);
//                $var = str_replace("Ё", "Е", $var);
            }
            $var = $this->__escape_string($var);
//            $var = str_replace("-", "\-", $var);
        }
        return $var;
    }
    
    /**
     * Возвращает список полей таблицы.
     * 
     * @param string $schema       имя схемы/БД где, находится таблица (см. {@link add_schema()})
     */
    public function &table_fields($table_name, $schema = null)
    {
        return $this->__table_fields($table_name, $schema = null);
    }
    
    /**
     * Возвращает тукущую схему/БД.
     */
    public function get_current_schema()
    {
        return $this->_cur_schema;
    }
    
    /**
     * Возвращает информацию о соединении.
     * 
     * Возвращает массив вида:
     *     array(
     *         "host"        => хост сервера БД,
     *         "user_login"  => логин пользователя БД,
     *         "user_passwd" => пароль пользователя БД,
     *         "link"        => линк подключения к серверу 
     *         "db_name"     => имя БД (только для СУБД, для подключения к кторым указывается БД)
     *     )
     * 
     * @return array информация о соединении
     */
    public function get_connection()
    {
        $ret = array(
            "host"        => $this->_db_host,
            "user_login"  => $this->_db_user,
            "user_passwd" => $this->_db_password,
            "link"        => &$this->_db_link
        );
        if ($this->_db_name) {
            $ret["db_name"] = $this->_db_name;
        }
        return $ret; 
    }
    
};
///////////////////////////////////////////////////////////////////////////////
?>
