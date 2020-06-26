<?php

class Tools {
    static function connect($host="localhost:3306", $user="root", $pass="123456", $dbname="shop") {
    //static function connect($host="sql307.epizy.com", $user="epiz_26083269", $pass="F4IitMs85x3wScZ", $dbname="epiz_26083269_site3") {
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
                <a href='pages/item_info.php?name=".$this->id."' class='ml-2 float-left' target='_blank'>".$this->itemname."</a>";
        echo "<span class='float-right mr-0 ml-auto'>".$this->rate."&nbsp;rate</span>";
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
        if(!isset($_SESSION['ruser'])) {
            $ruser = 'cart_'.$this->id;
        } else {$ruser = $_SESSION['ruser']."_".$this->id;}
        echo "<button class='btn btn-primary btn-lg btn-block' onclick=createCookie('".$ruser."','".$this->id."')>Add to cart</button>";
        echo '</div>';
        echo '</div></div></div>';
    }

    public function drawforCart() { 
        echo '<tr class="bg-light">';
        echo '<td><img src="'.$this->imagepath.'" class="img-fluid item-img"></td>';
        echo '<td class="align-middle">'.$this->itemname.'</td>';
        echo '<td class="align-middle">'.$this->pricesale.'</td>';
        if(!isset($_SESSION['ruser'])) {
            $ruser = 'cart_'.$this->id;
        } else {$ruser = $_SESSION['ruser']."_".$this->id;}
        echo '<td class="align-middle"><button class="btn btn-danger" onclick=eraseCookie("'.$ruser.'")>Dell</button></td>';
        echo '</tr>';
    }

    function sale() {
        try {
            $pdo = Tools::connect();
            $ruser = 'cart';
            if(isset($_SESSION['ruser'])) {
                $ruser = $_SESSION['ruser'];
            }
            $upd = "UPDATE customers SET total=total+? WHERE login=?";
            $ps = $pdo->prepare($upd);
            $res = $ps->execute([$this->pricesale, $ruser]);

            //создаем данные о покупке товара с занесением в таблицу sales
            $ins = "INSERT INTO sales(customername, itemname, pricein, pricesale, datesale) VALUES(?,?,?,?,?)";
            $ps = $pdo->prepare($ins);
            $zone = date_default_timezone_set('Asia/Novosibirsk');
            $res = $ps->execute([$ruser, $this->itemname, $this->pricein, $this->pricesale, @date("d/M/Y, H:i:s", $zone)]);
            return $this->id;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    function SMTP($id_result) {
        require_once("PHPMailer/PHPMailerAutoload.php");
        require_once("private/private_data.php");

        $mail = new PHPMailer;
        $mail->Charset = "UTF-8";

        //настраиваем MHTP - почтовый протокол передачи данных
        $mail->isSMTP();
        $mail->SMTPAuth = true;

        $mail->Host = 'ssl://smtp.mail.ru';
        $mail->Port = 465;
        $mail->Username = MAIL;
        $mail->Password = PASS;

        $mail->setFrom('152152m@mail.ru', 'SHOP NATALI by Andreeva Maria, https://github.com/AndreevaMaria/site3_shop');
        //$mail->addAddress('152152m@mail.ru', 'ADMIN');
        //$mail->addAddress('petrovski_a@itstep.org', 'SUPER');
        
        $mail->Subject = 'New order on site SHOP NATALI';

        $body = "<table cellspacing='0' cellpadding='0' border='2' width='800' style='background-color: bisque!important'>";
        $i = 0;
        $arrItem = [];
        foreach($id_result as $id) {
            $item = self::fromDb($id);
            array_push($arrItem, $item->itemname, $item->pricesale, $item->info);
            $path = $item->imagepath;
            $cid = md5($path);
            $mail->AddEmbeddedImage($path, $cid, 'item_'.$i);
            $body .= "<tr>
                        <th style='width: 200px;'>$item->itemname</th>
                        <td style='width: 150px;text-align: center;'>$item->pricesale</td>
                        <td style='text-align: center;'>$item->info</td>
                        <td style='width: 100px;'><img src='cid:$cid' alt='item_$i' style='width: 100px;'></td>
                    </tr>";
            ++$i;
        }
        $body .= "</table>";
        $mail->msgHTML($body);
        $mail->send();

        //CSV - запись в Excel-файл
        try {
            $csv = new CSV("private/excel_file.csv"); //Открываем наш csv
            $csv->setCSV($arrItem);
        } catch (Exception $e) { //Если csv файл не существует, выводим сообщение
                echo "Ошибка: " . $e->getMessage();
            }

    }

}

class CSV {
    private $csv_file = null;

    public function __construct($csv_file) {
        $this->csv_file = $csv_file; // private/excel_file.csv
    }

    function setCSV($arrItem) {
        $items = array_chunk($arrItem, 3);
        // открываем csv-файл до записи
        $file = fopen($this->csv_file, 'a+'); // добавить в файл, + - это если он еще не создан, то создать его
        foreach($items as $item) {     
            $itemCSV = implode("; ", $item);  
            var_dump($itemCSV);   
            fputcsv($file, $item);
        }
        fclose($file);
    }
}
?>