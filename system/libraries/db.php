<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/db.php
 *
 * Робота з базою даних.
 * Версія 1.0.1 (25.04.2013 - додано getAllData(), getAllDataByFieldInArray(), language(), latterUAtoEN())
 * Версія 1.0.2 (16.07.2014) - додано getCount(), register(); розширено (можуть приймати в якості умови масив): getAllDataById(), getAllDataByFieldInArray(+сорутвання)
 * Версія 1.0.3 (06.11.2014) - додано getQuery(), (12.11.2014) до getAllDataById(), getAllDataByFieldInArray() додано авто підтримку до умови IN
 * Версія 2.0 (28.09.2015) - переписано код getAllDataById(), getAllDataByFieldInArray(), getCount(). Додано службову функцію makeWhere(). Додано запити по конструкції: prefix(), select(), join(), order(), limit(), get().
 * Версія 2.0.1 (26.03.2016) - до get() додано параметр debug, що дозволяє бачити кінцевий запит перед запуском, виправлено помилку декількаразового запуску get()
 * Версія 2.0.2 (01.04.2016) - до makeWhere() додано параметр сортування НЕ '!'
 * Версія 2.0.3 (26.07.2016) - адаптовано до php7
 * Версія 2.0.4 (12.09.2016) - додано getAliasImageSizes()
 * Версія 2.1 (22.09.2016) - updateRow(), deleteRow() адаптовано через makeWhere(); у makeWhere() виправлено роботу з нульовими значеннями; до getRows() додати перевірку на тип single
 * Версія 2.1.1 (27.09.2016) - до makeWhere() додано повторюване поле через "+"
 * Версія 2.2 (19.12.2016) - додано sitemap_add(), sitemap_redirect(), sitemap_update(), sitemap_index(), sitemap_remove(), cache_clear()
 * Версія 2.2.1 (08.02.2017) - додано "chaining methods";
 * Версія 2.2.2 (05.09.2017) - у випадку успіху insertRow() повернає getLastInsertedId(); fix getRows('single')
 * Версія 2.2.3 (29.10.2017) - додано count_db_queries, showDBdump, виправлено помилку у where() для пустого/нульового значення
 * Версія 2.2.4 (01.11.2017) - додано group(), оптимізовано роботу getAliasImageSizes()
 * Версія 2.2.5 (01.12.2017) - amp версію виключено з індексації
 * Версія 2.3 (17.03.2018) - додано insertRows() - мультивставка рядків
 * Версія 2.4 (10.09.2019) - додано cache_add(), cache_get(), cache_delete(), cache_delete_all() - робота з файловим кешем
 * Версія 2.4.1 (19.11.2019) - Перейменовано cache_clear() => sitemap_cache_clear(). Інтегровано всередину онулення загального індексу по сайту
 * Версія 2.4.2 (26.11.2019) - до makeWhere() додано keyValue #! ['1c_status' => '#!c.status']
 * Версія 2.4.3 (11.12.2019) - до cache_add(), cache_get(), cache_delete(), cache_delete_all() - додано мультимовність
 * Версія 2.4.5 (16.12.2019) - до getAliasImageSizes() додано кешування у сесії
 * Версія 2.4.6 (20.02.2020) - до get('count') додано (виправлено) коректно роботу параметрів $debug, $get
 * Версія 2.5 (20.02.2020) - до makeWhere() додано keyValue & (&, &&, &&&, &&&&+) ['&' => 'p.old_price > p.price || p.promo > 0'] - дозволяє додати "складні" sql умови до запиту
 * Версія 2.6 (12.06.2020) - ініціалізація першого технічного запиту до БД 'SET NAMES utf8', тільки перед першим реальним запитом. Мінімізація пустих запитів.
                            додано public $this->saveDBlog. оновлено showTime()
                            до insertRows() доопрацьовано default значення у keys
 * Версія 2.7 (19.06.2020) - оновлено sitemap_*() - зміни системної таблиці wl_sitemap:link_sha1
                            оновлено getAliasImageSizes(), sitemap_cache_clear() => html_cache_clear() - робота з файловим кешем
                            додано getHTMLCacheKey(), getCacheContentKey(), $this->version
   Версія 2.8 (05.08.2020) - додано redis_set(), redis_get(), redis_del(), redis_delByKey(), redis_ping(), redis_do(), $this->html_cache_in_redis
   2.8.1 (09.12.2020) - updateRow() values can set NULL, numeric format. select(.., .., .., clear = true) add default clear param
   2.8.2 (15.01.2021) - makeWhere() масив значень з одного елементу {key} IN ({value}) => {key} = {value}
   2.9 (15.02.2021) - makeWhere() підтримка FULLTEXT пошуку. Ключ ~
   3.0 (03.03.2021) - move reis_*, cache_* to cache.php, add setConnect(), deConnect()
   3.0.1 (24.03.2021) - add port as config db. default 3306
 */

class Db {

    public $alias;

    private $connects = array();
    private $current = 0;
    private $result;
    public $version = '3.0';
    public $count_db_queries = 0;
    public $showDBdump = false;
    public $saveDBlog = false; // to db.log

    /*
     * Отримуємо дані для з'єднання з конфігураційного файлу
     */
    function __construct($cfg)
    {
        if(isset($cfg['dev']) && isset($cfg['prod']) && is_array($cfg['dev']) && is_array($cfg['prod']))
        {
            $port = $cfg[WL_MODE]['port'] ?? 3306;
            $this->newConnect($cfg[WL_MODE]['host'], $cfg[WL_MODE]['user'], $cfg[WL_MODE]['password'], $cfg[WL_MODE]['database'], $port);
        }
        else if(isset($cfg['host']))
        {
            $port = $cfg['port'] ?? 3306;
            $this->newConnect($cfg['host'], $cfg['user'], $cfg['password'], $cfg['database'], $port);
        }
        else
            exit('APP_ERROR: db not find correct configs');
    }

    /**
     * Створюємо з'єднання
     *
     * @param <string> $host назва серверу
     * @param <string> $user ім'я користувача
     * @param <string> $password пароль
     * @param <string> $database назва бази даних
     */
    function newConnect($host, $user, $password, $database, $port = 3306)
    {
        $this->connects[] = new mysqli($host, $user, $password, $database, $port);
        $this->current = count($this->connects) - 1;
    }

    // можна змінити підключення до бд
    function setConnect($index = 0)
    {
        if(count($this->connects) > 1 && isset($this->connects[$index]))
            $this->current = $index;
    }

    // можна відключитися від додаткового з'єднання з базою
    function deConnect($index = -1)
    {
        if(count($this->connects) > 1)
        {
            if($index < 0)
                $index = $this->current;

            unset($this->connects[$index]);

            if($index == $this->current)
                $this->current = array_key_first($this->connects);
        }
    }

    /**
     * Виконуємо запит
     *
     * @param <string> $query запит
     */
    function executeQuery($query)
    {
        if($this->count_db_queries === 0)
        {
            $this->connects[$this->current]->query('SET NAMES utf8');
            
            if ($this->saveDBlog)
                file_put_contents('db.log', PHP_EOL.$this->count_db_queries.': SET NAMES utf8'.PHP_EOL, FILE_APPEND);
            if ($this->showDBdump)
                echo $this->count_db_queries.': SET NAMES utf8 <hr>';

            $this->count_db_queries++;
        }

        if ($this->showDBdump || $this->saveDBlog)
        {
            $this->time_start = microtime(true);
            $this->mem_start = memory_get_usage();
            
            if ($this->saveDBlog)
                file_put_contents('db.log', $this->count_db_queries.': '.$query.PHP_EOL, FILE_APPEND);
            if ($this->showDBdump)
                echo $this->count_db_queries.': '.$query;
            // if($this->count_db_queries == 11)
            // {
            //     echo "<pre>";
            //     debug_print_backtrace();
            //     echo "</pre>";
            // }
        }

        $result = $this->connects[$this->current]->query($query);
        if(!$result)
            echo $this->connects[$this->current]->error;
        else
            $this->result = $result;
        $this->count_db_queries++;

        if ($this->saveDBlog)
            file_put_contents('db.log', $this->showTime(true).PHP_EOL, FILE_APPEND);
        if($this->showDBdump)
            $this->showTime();
    }

    function updateRow($table, $changes, $key, $row_key = 'id')
    {
        $where = $this->makeWhere($key, $row_key);
        if($where != '')
        {
            $update = "UPDATE `".$table."` SET ";
            foreach ($changes as $key => $value) {
                if($value === NULL)
                    $update .= "`{$key}` = NULL,";
                elseif(is_numeric($value))
                    $update .= "`{$key}` = {$value},";
                else
                {
                    $value = $this->sanitizeString($value);
                    $update .= "`{$key}` = '{$value}',";
                }
            }
            $update = substr($update, 0, -1);
            $update .= " WHERE ".$where;
            $this->executeQuery($update);
            if($this->affectedRows() > 0)
                return true;
        }
        return false;
    }

    function insertRow($table, $changes)
    {
        $update = "INSERT INTO `".$table."` ( ";
        $values = '';
        foreach ($changes as $key => $value) {
            $value = $this->sanitizeString($value);
            $update .= '`' . $key . '`, ';
            $values .= "'{$value}', ";
        }
        $update = substr($update, 0, -2);
        $values = substr($values, 0, -2);
        $update .= ' ) VALUES ( ' . $values . ' ) ';
        $this->executeQuery($update);
        if($this->affectedRows() > 0)
            return $this->getLastInsertedId();
        return false;
    }

    function insertRows($table, $keys = array(), $data = array(), $perQuery = 50)
    {
        if(empty($keys) || empty($data))
            return false;

        $insert = "INSERT INTO `".$table."` ( ";
        foreach ($keys as $key => $default) {
            if(is_numeric($key))
                $key = $default;
            if(is_numeric($key))
                continue;
            $insert .= '`' . $key . '`, ';
        }
        $insert = substr($insert, 0, -2);
        $insert .= ' ) VALUES ';
        $inserted = $i = 0; $query = '';
        foreach ($data as $row) { 
            $values = '';
            foreach ($keys as $key => $default) {
                $value = '';
                if(is_numeric($key))
                    $key = $default;
                else
                    $value = $default;
                if(is_numeric($key))
                    continue;
                if(isset($row[$key]))
                    $value = $this->sanitizeString($row[$key]);
                if(is_numeric($value))
                    $values .= "{$value}, ";
                else
                    $values .= "'{$value}', ";
            }
            $values = substr($values, 0, -2);
            $query .= '( ' . $values . ' ), ';
            if(++$i > $perQuery)
            {
                $i = 0;
                $query = $insert . substr($query, 0, -2) . ';';
                $this->executeQuery($query);
                $inserted += $this->affectedRows();
                $query = '';
            }
        }
        if($i > 0)
        {
            $query = $insert . substr($query, 0, -2) . ';';
            $this->executeQuery($query);
            $inserted += $this->affectedRows();
        }
        return true;
    }

    function getLastInsertedId()
    {
        return $this->connects[$this->current]->insert_id;
    }

    function deleteRow($table = '', $id, $row_key = 'id')
    {
        $where = $this->makeWhere($id, $row_key);
        if($where != '')
        {
            $this->executeQuery("DELETE FROM `{$table}` WHERE {$where}");
            if($this->affectedRows() > 0)
                return true;
        }
        return false;
    }

    /**
     * Отримуємо рядки
     *
     * @return <array>
     */
    function getRows($type = '')
    {
        if($type == 'single' && $this->result->num_rows != 1)
            return false;
        if($this->result->num_rows > 1 || $type == 'array')
        {
            $objects = array();
            while($obj = $this->result->fetch_object()){
                array_push($objects, $obj);
            }
            return $objects;
        }
        return $this->result->fetch_object();
    }

    /**
     * Отримуємо кількість рядків
     *
     * @return <int>
     */
    function numRows()
    {
        return $this->result->num_rows;
    }

    /**
     * Отримуємо кількість задіяних рядків
     *
     * @return <int>
     */
    function affectedRows()
    {
        return $this->result;
    }

    /**
     * Очистити рядок
     *
     * @param <string> $data дані
     *
     * @return <string>
     */
    function sanitizeString($data)
    {
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            if(get_magic_quotes_gpc())
                $data = stripslashes($data);
        }
        return $this->connects[$this->current]->escape_string($data);
    }

    function mysql_real_escape_string($q)
    {
        return $this->connects[$this->current]->real_escape_string($q);
    }

    public function getQuery($query = false, $getRows = '')
    {
        if($query)
        {
            $this->executeQuery($query);
            if($this->numRows() > 0)
                return $this->getRows($getRows);
        }
        return false;
    }

    /**
     * Допоміжні функції
     */
    function getAllData($table = false, $order = '')
    {
        if($table)
        {
            if($order != '') $order = ' ORDER BY '.$order;
            $this->executeQuery("SELECT * FROM `{$table}` {$order}");
            if($this->numRows() > 0)
                return $this->getRows('array');
        }
        return false;
    }

    function getAllDataById($table = '', $key, $row_key = 'id')
    {
        if($table != '')
        {
            $where = $this->makeWhere($key, $row_key);
            if($where != '')
            {
                $this->executeQuery("SELECT * FROM `{$table}` WHERE {$where}");
                if($this->numRows() == 1)
                    return $this->getRows();
            }
        }
        return false;
    }

    function getAllDataByFieldInArray($table = '', $key, $row_key = 'id', $order = '')
    {
        if($table != '')
        {
            $where = $this->makeWhere($key, $row_key);
            if($where != '')
            {
                if(is_array($key) && $row_key != '') $where .= ' ORDER BY '.$row_key;
                elseif($order != '') $where .= ' ORDER BY '.$order;
                $this->executeQuery("SELECT * FROM `{$table}` WHERE {$where}");
                if($this->numRows() > 0)
                    return $this->getRows('array');
            }
        }
        return false;
    }

    function getCount($table = '', $key = '', $row_key = 'id')
    {
        if($table != ''){
            $where = $this->makeWhere($key, $row_key);
            if($where != '')
                $where = "WHERE {$where}";
            $this->executeQuery("SELECT count(*) as count FROM `{$table}` {$where}");
            if($this->numRows() == 1)
            {
                $count = $this->getRows();
                return $count->count;
            }
        }
        return null;
    }

    private function makeWhere($data, $row_key = 'id', $prefix = false)
    {
        $where = '';
        if(is_array($data))
        {
            foreach ($data as $key => $value) {
                if($key != '' && $key[0] == '&')
                {
                    if($value[0] != '(' && $value[0] != '#')
                        $value = "({$value})";
                    if($value[0] == '#')
                        $value = substr($value, 1);
                    $where .= $value.' AND ';
                }
                else if(!is_numeric($key) && $key != '')
                {
                    if($key[0] == '+')
                        $key = substr($key, 1);
                    if(is_string($value) && !empty($value) && $value[0] == '~')
                    {
                        $value = substr($value, 1);
                        $words = explode(' ', $value);
                        if(count($words) == 1)
                            $value = '%'.$value;
                        else
                        {
                            foreach ($words as &$w)
                                $w = '+'.$w;
                            $words = implode(' ', $words);
                            $where .= "MATCH ({$key}) AGAINST ('$words' IN BOOLEAN MODE) AND ";
                            continue;
                        }
                    }

                    if($prefix && $key[0] != '#')
                        $where .= "{$prefix}.{$key}";
                    elseif($key[0] == '#')
                    {
                        $key = substr($key, 1);
                        $where .= $key;
                    }
                    else
                        $where .= "`{$key}`";

                    if(is_array($value))
                    {
                        if(count($value) == 1)
                            $where .= " = {$value[0]} AND ";
                        else
                        {
                            $where .= " IN ( ";
                            foreach ($value as $v) {
                                $where .= "'{$v}', ";
                            }
                            $where = substr($where, 0, -2);
                            $where .= ') AND ';
                        }
                    }
                    elseif(is_numeric($value))
                        $where .= " = {$value} AND ";
                    elseif($value != '' || $value == 0)
                    {
                        $value = $this->sanitizeString($value);
                        if($value == '0')
                            $where .= " = 0 AND ";
                        elseif($value == '')
                            $where .= " = '' AND ";
                        elseif($value[0] == '%')
                            $where .= " LIKE '{$value}%' AND ";
                        elseif($value[0] == '@')
                        {
                            $value = substr($value, 1);
                            $where .= " LIKE '{$value}%' AND ";
                        }
                        elseif($value[0] == '>')
                        {
                            if($value[1] == '=')
                            {
                                $value = substr($value, 2);
                                $where .= " >= {$value} AND ";
                            }
                            else
                            {
                                $value = substr($value, 1);
                                $where .= " > {$value} AND ";
                            }
                        }
                        elseif($value[0] == '<')
                        {
                            if($value[1] == '=')
                            {
                                $value = substr($value, 2);
                                $where .= " <= {$value} AND ";
                            }
                            else
                            {
                                $value = substr($value, 1);
                                $where .= " < {$value} AND ";
                            }
                        }
                        else
                        {
                            if($value[0] == '#' && $value[1] != '!')
                            {
                                $value = substr($value, 1);
                                $where .= " = {$value} AND ";
                            }
                            elseif($value[0] == '#' && $value[1] == '!')
                            {
                                $value = substr($value, 2);
                                $where .= " != {$value} AND ";
                            }
                            elseif($value[0] == '!')
                            {
                                $value = substr($value, 1);
                                if(is_numeric($value))
                                    $where .= " != {$value} AND ";
                                else
                                    $where .= " != '{$value}' AND ";
                            }
                            else
                                $where .= " = '{$value}' AND ";
                        }
                    }
                    else
                        $where .= " = '' AND ";
                }
            }
            if($where != '')
                $where = substr($where, 0, -4);
        }
        else
        {
            $data = (string) $data;
            if($data != '')
            {
                if($prefix)
                    $row_key = "{$prefix}.{$row_key}";
                else
                    $row_key = "`{$row_key}`";
                $data = $this->sanitizeString($data);
                if($data[0] == '#')
                {
                    $data = substr($data, 1);
                    $where = "{$row_key} = {$data}";
                }
                elseif(is_numeric($data))
                    $where .= "{$row_key} = {$data}";
                else
                    $where = "{$row_key} = '{$data}'";
            }
        }
        return $where;
    }

    private $query_table = false;
    private $query_prefix = false;
    private $query_fields = '*';
    private $query_where = false;
    private $query_join = array();
    private $query_group = false;
    private $query_group_prefix = false;
    private $query_order = false;
    private $query_order_prefix = true;
    private $query_limit = false;

    public function prefix($prefix)
    {
        if($this->query_prefix == false)
            $this->query_prefix = $prefix;
        else
            exit('Work with DB. Prefix of table name has to be set before function select!');
    }

    public function select($table, $fields = '*', $key = '', $row_key = 'id', $clear = true)
    {
        if($clear)
            $this->clear();
        $table = preg_replace("|[\s]+|", " ", $table);
        $table = explode(' ', $table);
        if(count($table) == 3 && ($table[1] == 'as' || $table[1] == 'AS' || $table[1] == 'As'))
            $this->query_prefix = $table[2];
        $this->query_table = $table[0];
        $this->query_fields = $fields;
        if($this->query_prefix == false)
            $this->query_prefix = $table[0];
        $this->query_where = $this->makeWhere($key, $row_key, $this->query_prefix);
        return $this;
    }

    public function join($table, $fields, $key = '', $row_key = 'id', $type = 'LEFT')
    {
        $table = preg_replace("|[\s]+|", " ", $table);
        $table = explode(' ', $table);
        $prefix = $table[0];
        if(count($table) == 3 && ($table[1] == 'as' || $table[1] == 'AS' || $table[1] == 'As'))
            $prefix = $table[2];
        $join = new stdClass();
        $join->table = $table[0];
        $join->prefix = $prefix;
        $join->fields = $fields;
        $join->where = $this->makeWhere($key, $row_key, $prefix);
        $join->type = $type;
        $this->query_join[] = $join;
        return $this;
    }

    public function order($order, $prefix = true)
    {
        $this->query_order_prefix = $prefix;
        $this->query_order = $order;
        return $this;
    }

    public function group($group, $prefix = false)
    {
        $this->query_group_prefix = $prefix;
        $this->query_group = $group;
        return $this;
    }

    public function limit($limit, $offset = 0)
    {
        if ($limit > 0)
        {
            $this->query_limit = 'LIMIT '.$limit;
            if($offset > 0)
                $this->query_limit .= ', '.$offset;
        }
        return $this;
    }

    /**
     * Виконати запит до БД
     *
     * @param <string> $type - тип запиту:
     *                       auto   якщо один рядок об'єкт, якщо декілька - масив об'єктів
     *                       single тільки один об'єкт. Якщо більше ніж один - false
     *                       array  завжди масив об'єктів
     *                       count  повертає кількість знайдених рядків згідно запиту
     * @param <bool> $clear очистити дані запиту (для нового)
     *
     * @return <object>
     */
    public function get($type = 'auto', $clear = true, $debug = false, $get = true)
    {
        if($this->query_table)
        {
            $data = NULL;
            if($type == 'count')
            {
                $data = 0;
                $where = '';
                if($this->query_prefix)
                    $where = "AS {$this->query_prefix} ";
                //join
                if(!empty($this->query_join))
                    foreach ($this->query_join as $join) {
                        $where .= "{$join->type} JOIN `{$join->table}` ";
                        if($join->prefix != $join->table)
                            $where .= "AS {$join->prefix} ";
                        $where .= "ON {$join->where} ";
                    }
                if($this->query_where != '')
                    $where .= 'WHERE '.$this->query_where;

                //group
                if($this->query_group)
                {
                    if($this->query_prefix || $this->query_group_prefix)
                    {
                        if($this->query_group_prefix == false)
                            $this->query_group_prefix = $this->query_prefix;
                        $where .= "GROUP BY {$this->query_group_prefix}.{$this->query_group} ";
                    }
                    else
                        $where .= "GROUP BY {$this->query_group} ";
                }

                $query = "SELECT count(*) as count FROM `{$this->query_table}` {$where}";

                if($debug)
                    echo($query);

                if($get)
                {
                    $row = $this->getQuery($query);
                    if(is_object($row))
                        $data = $row->count;
                }
            }
            else
            {
                $query = "SELECT ";
                // fields
                if(!empty($this->query_join))
                {
                    if(!is_array($this->query_fields))
                        $this->query_fields = explode(',', $this->query_fields);
                    $prefix = $this->query_table;
                    if($this->query_prefix)
                        $prefix = $this->query_prefix;
                    foreach ($this->query_fields as $field) {
                        if($field != '')
                        {
                            $field = trim($field);
                            $query .= $prefix.'.'.$field.', ';
                        }
                    }
                    foreach ($this->query_join as $join) {
                        if(!is_array($join->fields))
                            $join->fields = explode(',', $join->fields);
                        foreach ($join->fields as $field) {
                            if($field != '')
                            {
                                $field = trim($field);
                                $query .= $join->prefix.'.'.$field.', ';
                            }
                        }
                    }
                    $query = substr($query, 0, -2);
                }
                else
                    $query .= $this->query_fields;

                //from
                $query .= " FROM `{$this->query_table}` ";
                if($this->query_prefix && !empty($this->query_join))
                    $query .= "AS {$this->query_prefix} ";

                //join
                if(!empty($this->query_join))
                    foreach ($this->query_join as $join) {
                        $query .= "{$join->type} JOIN `{$join->table}` ";
                        if($join->prefix != $join->table)
                            $query .= "AS {$join->prefix} ";
                        $query .= "ON {$join->where} ";
                    }

                //where
                if($this->query_where)
                    $query .= "WHERE {$this->query_where} ";

                //group
                if($this->query_group)
                {
                    if($this->query_prefix || $this->query_group_prefix)
                    {
                        if($this->query_group_prefix == false)
                            $this->query_group_prefix = $this->query_prefix;
                        $query .= "GROUP BY {$this->query_group_prefix}.{$this->query_group} ";
                    }
                    else
                        $query .= "GROUP BY {$this->query_group} ";
                }

                //order
                if($this->query_order)
                {
                    if($this->query_order_prefix)
                    {
                        if($this->query_order_prefix === true)
                            $this->query_order_prefix = $this->query_prefix;
                        $query .= "ORDER BY {$this->query_order_prefix}.{$this->query_order} ";
                    }
                    else
                        $query .= "ORDER BY {$this->query_order} ";
                }

                //limit
                if($this->query_limit)
                    $query .= $this->query_limit;

                if($debug)
                    echo($query);

                if($get)
                    $data = $this->getQuery($query, $type);
            }
            if($clear)
                $this->clear();

            return $data;
        }
        return false;
    }

    public function clear()
    {
        $this->query_table = false;
        $this->query_prefix = false;
        $this->query_fields = '*';
        $this->query_where = false;
        $this->query_join = array();
        $this->query_group = false;
        $this->query_order = false;
        $this->query_limit = false;
    }

    public function getAllDataWithWlUsers($table, $where = [], $order = false, $limit = 0, $offset = 0)
    {
        return $this->select($table.' as t', '*', $where)
                    ->join('wl_users as u', 'name as user_name', '#t.created_at')
                    ->order($order)
                    ->limit($limit, $offset)
                    ->get('array');
    }

    public function showTime($return = false)
    {
        $mem_end = memory_get_usage();
        $time_end = microtime(true);

        if ($this->showDBdump || $this->saveDBlog)
        {
            $time = $time_end - $this->time_start;
            $mem = $mem_end - $this->mem_start;
            $mem = round($mem/1024, 5);
            if($mem > 1024)
            {
                $mem = round($mem/1024, 5);
                $mem = (string) $mem . ' Мб';
            }
            else
                $mem = (string) $mem . ' Кб';
        }

        $timeGlobe = $time_end - $GLOBALS['time_start'];
        $memGlobe = $mem_end - $GLOBALS['mem_start'];
        $memGlobe = round($memGlobe/1024, 5);
        if($memGlobe > 1024)
        {
            $memGlobe = round($memGlobe/1024, 5);
            $memGlobe = (string) $memGlobe . ' Мб';
        }
        else
            $memGlobe = (string) $memGlobe . ' Кб';

        if($return)
        {
            $text = '';
            if(isset($this->result->num_rows))
                $text = "Результатів: ".$this->result->num_rows;
            $text .= ' Час виконання: '.round($time, 5).' сек. Використано памяті: '.$mem.'. Від старту: Час виконання: '.round($timeGlobe, 5).' сек. Використано памяті: '.$memGlobe;
            return $text;
        }
        else
        {
            if($this->showDBdump && isset($this->result->num_rows))
                echo "<br> Результатів: ".$this->result->num_rows;
            else
                echo '<br>';
            if ($this->showDBdump || $this->saveDBlog)
                echo ' Час виконання: '.round($time, 5).' сек. Використано памяті: '.$mem.'. Від старту: Час виконання: '.round($timeGlobe, 5).' сек. Використано памяті: '.$memGlobe.' <hr>';
            else
                echo ' Від старту: Час виконання: '.round($timeGlobe, 5).' сек. Використано памяті: '.$memGlobe.'. Запитів до БД: '.$this->count_db_queries.' <hr>';
        }
    }

}

?>