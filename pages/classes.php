<?php

class Tools {
    static function connect($host="localhost:3306", $user="root", $pass="123456", $dbname="shop") {
        // PDO (PHP data object) - механизм взаимодействия с СУБД, позволяет облегчить рутинные задачи
        // при выполнении запросов и содержит защитные механизмы приработе с СУБД

        // формирование строки для создания объекта PDO - DSN (Data Sourse Name), 
        // сведения для подключения к базе, представленные в виде строки
        $cs = 'mysql:host='.$host.';dbname='.$dbname.';charset=utf8';

        //массив опций для создания PDO
        $options = array(
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8');
        
        try {
            $pdo = new PDO($cs, $user, $pass, $options);
            return $pdo;
        } catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    static function register($login, $pass, $path) {
        $login = trim(htmlspecialchars($login));
        $pass = trim(htmlspecialchars($pass));
        $imagepath = $path;

        if ($login == "" || $pass == "") {
            echo "<h5 class='text-danger'>Заполните все поля</h5>";
            return false;
        }
        
        if(strlen($login) < 3 || strlen($login) > 30 || strlen($pass) < 3 || strlen($pass) > 30) {
            echo "<h5 class='text-danger'>От 0 до 30 символов</h5>";
            return false;
        }
        
        Tools::connect();
        // создаем экземпляяр классаи Customer, передаем в его конструктор значения переменных
        // пароль, логин и путь изображения, они записываются в свойства класса и после этого вызвать метод intoDb
        $customer = new Customer($login, $pass, $imagepath);
        $customer->intoDb();
        return true;
    }
}

class Customer {
    protected $id;
    protected $login;
    protected $pass;
    protected $roleid;
    protected $discount;
    protected $total;
    protected $imagepath;

    function __construct($login, $pass, $imagepath, $id=0) {
        $this->login = $login;
        $this->pass = $pass;
        $this->imagepath = $imagepath;
        $this->id = $id;

        $this->total = 0;
        $this->discount = 0;
        $this->roleid = 2;
    }

    // orm object relation mapping - объектно реляционное отображение. это механизм работы сущности в связи с бд. 
    // внести покупателя в таблицу

    function intoDb() {
        try {
            $pdo = Tools::connect();
            //выполняем запрос
            $ps = $pdo->prepare('INSERT INTO `customers`(`login`, `pass`, `roleid`, `discount`, `total`, `imagepath`) VALUES (:login, :pass, :roleid, :discount, :total, :imagepath)');
            $ar = (array) $this;
            array_shift($ar);//удаляем первый элемент массива, т.е id
            $ps->execute($ar);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    //получаем данные о созданном пользователе из таблицы
    static function fromDb ($id) {
        $customer = null;
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM customers WHERE id=?");
            // выполняем выбор всех данных о пользователе по id получаемому в качестве параметра в ф-ю fromDB
            // и заносим его в массив, ибо execute этого требует. 
            // при выполнении execute $id будет подставлен вмеcто символа ? при подготовке (метод prepare)
            $res = $ps->execute(array($id)); // == [$id]
            // перебираем данные о полученном пользователе и заносим его в ассоциативный массив $row
            $row = $res->fetch();
            $customer = new Customer($row['login'], $row['pass'], $row['imagepath'], $row['id']);
            return $customer;

        } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
        }
    }
}

class Item {
    public $id;
    public $itemname;
    public $catid;
    public $pricein;
    public $pricesale;
    public $info;
    public $rate;
    public $imagepath;
    public $action;

    function __construct($itemname, $catid, $pricein, $pricesale, $info, $imagepath, $rate=0, $action=0, $id=0) {
        $this->id = $id;
        $this->itemname = $itemname;
        $this->catid = $catid;
        $this->pricein = $pricein;
        $this->pricesale = $pricesale;
        $this->info = $info;
        $this->rate = $rate;
        $this->imagepath = $imagepath;
        $this->action = $action;
    }

    function intoDb() {
        try {
            $pdo = Tools::connect();
            //выполняем запрос
            $ps = $pdo->prepare('INSERT INTO items(itemname, catid, pricein, pricesale, info, rate, imagepath, action) VALUES (:itemname, :catid, :pricein, :pricesale, :info, :rate, :imagepath, :action)');
            $ar = (array) $this;
            array_shift($ar);//удаляем первый элемент массива, т.е id
            $ps->execute($ar);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    //получаем данные о созданном продукте из таблицы
    static function fromDb ($id) {
        $customer = null;
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM customers WHERE id=?");
            $res = $ps->execute(array($id)); // == [$id]
            // перебираем данные о полученном пользователе и заносим его в ассоциативный массив $row
            $row = $res->fetch();
            $customer = new Customer($row['login'], $row['pass'], $row['imagepath'], $row['id']);
            return $customer;

        } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
        }
    }
}
?>