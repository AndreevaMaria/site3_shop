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

    static function login($login, $pass) {
        $name = trim(utf8_encode(htmlspecialchars($login)));
        $pass = trim(utf8_encode(htmlspecialchars($pass)));
    
        if ($name == "" || $pass == "") {
            echo "<h3 class='text-danger'>Заполните все поля</h3>";
            return false;
        }
        
        if(strlen($name) < 3 || strlen($name) > 30 || strlen($pass) < 3 || strlen($pass) > 30) {
            echo "<h3 class='text-danger'>От 0 до 30 символов</h3>";
            return false;
        }
    
        $pdo = Tools::connect();
        $ps = $pdo->prepare("SELECT login, pass, roleid FROM customers WHERE login='$name'");
        $ps->execute();      
        while($row = $ps->fetch()) {
            if($name == $row['login'] && $pass == $row['pass']) {
                $_SESSION['ruser'] = $name;
                if($row['roleid'] == 1) { 
                    $_SESSION['radmin'] = $name; 
                } 
                return true;
            } else {
                return false;
            } 
        }
    }
}

class Customer {
    public $id;
    public $login;
    public $pass;
    public $roleid;
    public $discount;
    public $total;
    public $imagepath;

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
    static function fromDb($id) {
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
    //получение товаров
    static function fromDb($id) {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM items WHERE id=?");
            $ps->execute(array($id));
            $row = $ps->fetch();
            $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'], $row['imagepath'], $row['rate'], $row['action'], $row['id']);
            return $item;

        } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
        }
    }

    static function getItems ($catid=0) {
        try {
            $pdo = Tools::connect();
            if($catid == 0) { //выбираем все товары (не указана категория)
                $ps = $pdo->prepare("SELECT * FROM items");
                $res = $ps->execute();
            } else { //выбираем товары по категории
                $ps = $pdo->prepare("SELECT * FROM items WHERE catid=?");
                $ps->execute([$catid]);
            }
            while($row = $ps->fetch()) {
                //создаем экземпляр класса Item
                $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'], $row['imagepath'], $row['rate'], $row['action'], $row['id']);
                //ассоциативный массив отобранных товаров (сущности или экземляры класса Item)
                $items[] = $item;
            }
                return $items; // возвращаем товары в точку вызова - станица Каталог
        } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
        }
    }

    function drawItem() {
        echo '<div class="col-sm-6 col-md-4 col-lg-3 item-card mb-3">
                <div class="card bg-light border rounded"><div class="card-body">';
        echo "<div class='row item-card__title'>
                <a href='pages/item_info.php?name=".$this->id."' class='col-7 ml-2 float-left' target='_blank'>".$this->itemname."</a>";
        echo "<span class='float-right col-4 mr-0 ml-auto'>".$this->rate."&nbsp;rate</span>";
        echo '</div>';
        echo '<p class="ml-2">'.$this->info.'</p>';
        
        echo '<div clss="row">';
        echo '<div class="col-12 my-2 item-card__img">';
        echo '<img src="'.$this->imagepath.'" class="img-fluid">';
        echo '</div></div>';
        echo '<div class="item-card__price">';
        echo '<span class="mr-3 float-right">'.$this->pricesale.'&nbsp;$</span>';
        echo '</div>';
        echo '<div class="my-1 ml-2 text-justify item-card__title">';
        
        echo '</div>';
        echo '<div class="my-1 text-justify item-card__cart">';
        $ruser = '';
        if(!isset($_SESSION['reg']) || $_SESSION['reg'] == '') {
            $ruser = 'cart_'.$this->id;
        } else {$ruser = $_SESSION['reg']."_".$this->id;}
        echo "<button class='btn btn-primary btn-lg btn-block' onclick=createCookie('".$ruser."','".$this->id."')>Add to cart</button>";
        echo '</div>';
        echo '</div></div></div>';
    }

    public function drawforCart() { 
        echo '<tr class="bg-light">';
        echo '<td><img src="'.$this->imagepath.'" class="img-fluid item-img"></td>';
        echo '<td class="align-middle">'.$this->itemname.'</td>';
        echo '<td class="align-middle">'.$this->pricesale.'</td>';
        if(!isset($_SESSION['reg']) || $_SESSION['reg'] == '') {
            $ruser = 'cart_'.$this->id;
        } else {$ruser = $_SESSION['reg']."_".$this->id;}
        echo '<td class="align-middle"><button class="btn btn-danger" onclick=eraseCookie("'.$ruser.'")>Dell</button></td>';
        echo '</tr>';
    }
}


?>