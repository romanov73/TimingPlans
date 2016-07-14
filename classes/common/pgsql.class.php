<?php
/**
 * Файл объвления класса {@link MyDB} для работы с PostgreSQL.
 *
 * @tutorial SMWE/dev.db.pkg
 * @copyright 2004-2005 snaky
 * @author snaky <snaky@ulstu.ru>
 * @package SMWE
 * @subpackage Classes
 *
 * ******
 * $URL: http://svn.ulstu.ru/repos/snaky/site_engine/trunk/src/libs/common/pgsql.class.php $
 * $Id: pgsql.class.php 1232 2007-03-16 10:03:25Z engel $
 * ******
 */

///////////////////////////////////////////////////////
require_once("db.class.php");
///////////////////////////////////////////////////////

///////////////////////////////////////////////////////
/**#@+
 * Используется в методе {@link fetch_row()}.
 */
/**
 * Столбцы возвращаются как массив вида "поле" => "значение".
 */
define("DB_FETCH_ASSOC", PGSQL_ASSOC);
/**
 * Столбцы возвращаются как массив значений с числовыми ключами.
 */
define("DB_FETCH_NUM", PGSQL_NUM);
/**#@-*/
///////////////////////////////////////////////////////

/**
 * Класс для работы с PostgreSQL.
 *
 * Основные возможности:
 *  - подключение к серверу (нескольким серверам) под разными учетными записями;
 *  - легкий переход между подключениями;
 *  - зеркалирование (дублирование) запросов в другую БД;
 *  - ведение подробных логов запросов и ошибок;
 *  - автоматическое соствление запросов;
 *  - отлов ошибок в группе запросов;
 *  - сообщения об ошибках в запросах указывают на места вызова методов.
 *
 * @tutorial SMWE/dev.db.pkg
 * @package SMWE
 * @subpackage Classes
 */
class MyDB extends _MyDB { // PostgreSQL
///////////////////////////////////////////////////////////////////////////////////
    protected function __connect($host, $user, $passwd, $dbname=null)
    {
        $connect_string = "host=$host user=$user password=$passwd dbname=$dbname port=5432";
        return pg_connect($connect_string);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __select_schema($schema, $link)
    {
        $query = "SET search_path TO $schema, pg_catalog";
        return $this->__query($query, $link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __rdbms()
    {
        return "PostgreSQL";
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __close($link)
    {
        return pg_close($link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function &__query($query, $link = null)
    {
        try {
            $result= pg_query($link, $query);
            return $result;
        } catch (Exception $ex) {
            var_dump($ex);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function &__fetch_assoc(&$res)
    {
         $result= pg_fetch_assoc($res);
return $result;
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function &__fetch_num(&$res)
    {
        return pg_fetch_row($res);
    }
    ///////////////////////////////////////////////////////////////////////////////
    public function __error($link = null)
    {
        return pg_last_error($link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __free_result(&$res)
    {
        return pg_free_result($res);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __escape_string($value)
    {
        return pg_escape_string($value);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __data_seek(&$res, $pos)
    {
        return pg_result_seek($res, $pos);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __num_rows(&$res)
    {
        return pg_num_rows($res);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __affected_rows(&$res)
    {
        return pg_affected_rows($res);
    }
///////////////////////////////////////////////////////////////////////////////////
    protected function _error($errstr, $errtype = E_USER_WARNING)
    {
        $error_text = parent::_error($errstr, $errtype, 1);
        $error_text .= "<b>Details:</b><br>\nConnect name: {$this->_connect_name}<br>\nHost: {$this->_db_host}<br>\nUser: {$this->_db_user}<br>\nDB name: {$this->_db_name}<br>\n";
        if ($this->_db_link) {
            //$res = @pg_query($this->_db_links[$this->_cur_connect_name], "SHOW search_path;");
            //$row = @pg_fetch_assoc($res);
            //$spath = $row["search_path"];
            //@pg_free_result($res);
            $spath = $this->get_current_schema();
            $error_text .= "search_path: $spath<br>\n";
        }
        parent::_error($error_text, $errtype, 2);
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function __log_connect($db_user, $db_name = null)
    {
        return "\c $db_name $db_user";
    }
    protected function __log_selecting_schema($schema)
    {
        return "SET search_path TO $schema";
    }
    ///////////////////////////////////////////////////////////////////////////////
    protected function &__table_fields($table_name, $schema = null)
    {
        $sql = "
            SELECT
                a.attname as name
                , format_type(a.atttypid, a.atttypmod) as type
                , a.attnotnull as not_null
                , a.atthasdef as default
            FROM
                pg_class c
                , pg_attribute a
                , pg_namespace n
            WHERE
                c.relname = %table_name%
                AND n.nspname = %schema%
                AND a.attnum > 0
                AND a.attrelid = c.oid
                AND n.oid = c.relnamespace
            ORDER BY
                a.attnum
        ";
        $result = $this->get_first($sql, array("table_name" => $table_name, "schema" => $schema), null, $schema);
        return $result;
    }

    protected function _get_file_name()
    {
        return __FILE__;
    }
///////////////////////////////////////////////////////////////////////////////

    /**
     * Возвращает значение автоинкрементного поля таблицы после последней вставки.
     *
     * Примечание: работает только тогда, когда имя последовательности (sequence) для таблицы задано
     * как "{имя_таблицы}_{имя_поля}_seq" (имя "по умолчанию" при создании последовательности).
     *
     * @param string $tablename    имя таблицы
     * @param string $fieldname    имя автоинкрементного поля (по умолчанию "id")
     * @param string $schema       имя схемы/БД где, находится таблица (см. {@link add_schema()})
     * @return int идентификатор вставленной строки
     */
    public function insert_id($tablename = null, $fieldname = "id", $schema = null)
    {
        $tablename = addslashes($tablename);
        $fieldname = addslashes($fieldname);
        $last_id = $this->get_one("SELECT last_value FROM ${tablename}_${fieldname}_seq");
        return $last_id;
    }
};
///////////////////////////////////////////////////////////////////////////////
?>
