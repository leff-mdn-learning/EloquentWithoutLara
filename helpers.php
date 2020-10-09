Illuminate - это движок работы с базами данных в Laravel. Он поставляется вместе с Eloquent ORM. Если вам нужно создать приложение с использованием этой ORM, но без Laravel, то эта статья поможем вам в этом.

В этой статье мы будем создавать back-end для Q&A приложения с использованием Illuminate Database и ORM Eloquent.

Зависимости проекта
PHP: 5.5+
MYSQL
Composer
Возможности приложения
Наше приложение будет выполнять десять задач:

Добавление пользователя.
Добавление вопроса.
Добавление ответа на вопрос.
Проголосовать за ответ.
Получить вопрос с ответами.
Получить все вопросы и пользователей, которые их задавали.
Получить определенные вопросы, ответы и голоса.
Подсчет количества вопросов по определенному пользователю.
Обновить ответ.
Удалить вопрос.
Для начала мы создаем директорию и структуру нашего проекта.

В главной директории проекта мы создадим папку app, а затем в ней создадим две подпапки: models и controlllers. На данной картинке наша корневая папка проекта называется eqloquent. Замените ее на реальное название вашего проекта.

Our project organization
Затем мы создадим файл index.php в корневой папке проекта, на одном уровне с папкой app.

Мы будем использовать git, поэтому нужно создать .gitignore. Этот шаг необязателен.

Затем, мы установим все необходимые для нашего проекта зависимости.

1
2
3
4
5
6
{
 "name": "illuminate-example/eloquent",
 "description": "Implementation of Database Queries with illuminate and Eloquent",
 "type": "project",
 "require": {}
}
Для установки компонента illuminate database, нужно добавить эту строчку в файл composer.json:
"illuminate/database": "5.1.8",.

Затем мы добавим psr-4 автозагруку для папок Models и controllers:

1
2
3
4
5
"autoload": {
 "psr-4": {
 "Controllers\\": "app/controllers/",
 "Models\\": "app/models/" }
 }
Теперь наш файл composer.json должен выглядеть следующим образом:

01
02
03
04
05
06
07
08
09
10
11
12
13
14
{
 "name": "illuminate-example/eloquent",
 "description": "Implementation of Database Queries with illuminate and Eloquent",
 "type": "project",
 "require": {
 "illuminate/database": "5.1.8"},
 "autoload": 
    {"psr-4": 
        { "Controllers\\": "app/controllers/",
            "Models\\": "app/models/"
             
                 }
    }
}
Теперь мы выполним эти две команды композера, находясь в той же директории, где находится файл composer.json:

1
2
composer install
composer dump-autoload -o
Будет создана папка vendor, которую мы добавим в файл gitignore (этот шаг тоже необязателен).

Создадим файл конфигурации с доступами к нашей базе данных.

В корневой директории проекта мы создадим файл config.php и определим детали DB в файле Config.php. Обратите внимание, что значения следует заменить на ваши собственные параметры подключения к базе.

1
2
3
4
5
6
7
<?php
 
defined("DBDRIVER")or define('DBDRIVER','mysql');
defined("DBHOST")or define('DBHOST','localhost');
defined("DBNAME")or define('DBNAME','eloquent-app');
defined("DBUSER")or define('DBUSER','root');
defined("DBPASS")or define('DBPASS','pass');
Следующим шагом создадим схему для нашего приложения.

Следует помнить одну вещь, перед тем как мы начнем создавать схемы таблиц в нашей базе данных - мы можем добавить таймстампы к нашей схеме.

ORM Eloquent ожидает две колонки типа timestamp: created_at и updated_at. Если включим таймстампы для моделей, то Eloquent автоматически будет обновлять эти поля, когда мы создаем или обновляем модель.

Есть также третья колонка, называется deleted_at. Временная метка deleted_at работает немного по-другому. В Eloquent есть возможность мягкого удаления записей, которая использует колонку deleted_at, чтобы определить, что запись была удалена. Если вы удаляете запись с помощью метода eloquent 'delete' и у вас включен Soft Delete, то в эту колонку будет записано время удаления. Эти удаленные записи можно будет получить в любое время.

В нашем приложении мы будем использовать эти возможности, связанные с временными метками, поэтому включим их все в нашу схему.

Создадим таблицы с помощью следующих команд в MySQL:

Вопросы
1
2
3
4
5
6
7
8
9
CREATE TABLE `questions` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `question` tinytext,
 `user_id` int(11) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
Ответы
01
02
03
04
05
06
07
08
09
10
CREATE TABLE `answers` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `answer` tinytext,
 `user_id` int(11) DEFAULT NULL,
 `question_id` int(11) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
Голоса
1
2
3
4
5
6
7
8
9
CREATE TABLE `upvotes` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `answer_id` int(11) DEFAULT NULL,
 `user_id` int(11) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
Пользователи
01
02
03
04
05
06
07
08
09
10
CREATE TABLE `users` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `username` varchar(100) DEFAULT NULL,
 `email` varchar(200) DEFAULT NULL,
 `password` varchar(200) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
Затем мы продолжим с созданием файлов моделей и контроллеров для наших таблицы в следующих местах:

project_folder/app/models/question.php
project_folder/app/models/answer.php
project_folder/app/models/upvote.php
project_folder/app/models/user.php
project_folder/app/models/database.php
project_folder/app/controllers/questions.php
project_folder/app/controllers/answers.php
project_folder/app/controllers/upvotes.php
project_folder/app/controllers/users.php
Откроем в редакторе файл models/database.php.

Сначала создадим объект класса Capsule:

01
02
03
04
05
06
07
08
09
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
<?php
 
namespace Models; 
use Illuminate\Database\Capsule\Manager as Capsule;
 
class Database {
 
    function __construct() {
    $capsule = new Capsule;
    $capsule->addConnection([
     'driver' => DBDRIVER,
     'host' => DBHOST,
     'database' => DBNAME,
     'username' => DBUSER,
     'password' => DBPASS,
     'charset' => 'utf8',
     'collation' => 'utf8_unicode_ci',
     'prefix' => '',
    ]);
    // Setup the Eloquent ORM… 
    $capsule->bootEloquent();
}
 
}
В этом файле мы инициализируем Capsule константами, определенными в файле config.php, и затем загружаем eloquent.

Следующим шагом нужно создать стартовый скрипт. Здесь будет находится все необходимое, что нужно будет выполнить перед стартом нашего приложения.

Создадим этот файл в project_folder/start.php, а затем в нем сделаем require автозагрузчика Composer:

require 'vendor/autoload.php';

После этого мы подключаем файл config.php чтобы получить доступ к константам: require 'config.php';

Затем мы инициализируем класс базы данных.

1
2
3
4
5
6
<?php
 
use Models\Database;
 
//Boot Database Connection
new Database();
Ваш файл start.php должен выглядеть следующим образом:

1
2
3
4
5
6
7
<?php
require 'config.php';
require 'vendor/autoload.php';
use Models\Database;
//Initialize Illuminate Database Connection
new Database();
?>
Подключим его в ваш index.php, так как он будет нашим основным файлом в приложении.

Теперь наш index.php выглядит следующим образом:

1
2
3
<?php
require 'start.php';
?>
Теперь мы можем начать работу над моделями и контроллерами. В файле project_folder/app/models/question.php добавим следующее:

01
02
03
04
05
06
07
08
09
10
11
12
13
<?php
 
namespace Models;
 
use \Illuminate\Database\Eloquent\Model;
 
class Question extends Model {
     
    protected $table = 'questions';
     
}
 
?>
Затем в project_folder/app/controllers/questions.php:

1
2
3
4
5
6
7
8
<?php
namespace Controllers;
 
class Questions{
     
}
 
?>
В project_folder/app/controllers/answers.php делаем тоже самое:

1
2
3
4
5
6
7
<?php
namespace Controllers;
 
class Answers{
     
}
?>
Задача 1: Добавление пользователя
В модели пользователя (project_folder/app/models/user.php) мы добавляем следющий код для определения нашего пространства имен, наследуемся от Eloquent Model, и определяем название таблицы (protected $table), а так же какие поля в таблице можно будет заполнять с помощью массового присваивания (protected fillable).

1
2
3
4
5
6
7
8
9
<?php
namespace Models;
use \Illuminate\Database\Eloquent\Model;
 
class User extends Model {
    protected $table = 'users';
    protected $fillable = ['username','email','pass'];
}
?>
В контроллере для пользователей (project_folder/app/controllers/user.php), мы также определяем наше пространство имен и класс.

1
2
3
4
5
6
7
<?php
namespace Controllers;
 
class Users{
     
}
?>
Затем для создания пользователя, мы в контроллере user импортируем пространство имен модели User: use Models\User; и добавляем метод для создания полового пользователя.

1
2
3
4
5
6
<?php
 
    public static function create_user($username, $email, $password){
        $user = User::create(['username'=>$username,'email'=>$email,'password'=>$password]);
        return $user;
    }
Наш контроллер пользователей теперь выглядит так.

01
02
03
04
05
06
07
08
09
10
11
12
13
<?php
 
namespace Controllers;
use Models\User;
 
class Users {
     
    public static function create_user($username, $email, $password){
        $user = User::create(['username'=>$username,'email'=>$email,'password'=>$password]);
        return $user;
    }
}
?>
Затем в index.php мы добавляем эти строки и запускаем приложения, чтобы создать нового пользователя.

1
2
3
4
5
6
<?php
 
use Controllers\Users; 
 
// Import user controller
$user = Users::create_user("user1","user1@example.com","user1_pass");
Задача 2: Добавление вопроса
Для добавления вопроса мы импортируем модель Question в контроллер questions, и пишем метод create_question:

use Models\Question;

Затем:

1
2
3
4
5
6
7
<?php
 
public static function create_question($question,$user_id){
 
    $question = Question::create(['question'=>$question,'user_id'=>$user_id]);
    return $question;
}
Мы использовали массовое присваивание Eloquent для создания этой записи, но для того чтобы это заработало, нам нужно разрешить заполнение этих полей, так как по умолчанию Eloquent защищает модели от массового присваивания.

Так мы идем в модель question и добавляем в класс свойство $fillable.

protected $fillable = ['question','user_id'];

Чтобы выполнить это, импортируем контроллер questions в файл index.php и вызовем функцию create_question статически:

use Controllers\Question;

Затем создадим модель Question с вопросом и id User в качестве параметров:

$question = Questions::create_question("Have you ever met your doppelganger?",1);

Если все успешно, то будет возвращен объект модели.

Сейчас мы выполним скрипт index.php с различными данными, чтобы добавить больше вопросов в базу данных.

Задача 3: Добавить ответ на вопрос.
В модели ответа мы повторяем предыдущие шаги, которые мы использовали при создании моделей вопросов и пользователей:

01
02
03
04
05
06
07
08
09
10
11
<?php
namespace Models;
use \Illuminate\Database\Eloquent\Model;
 
class Answer extends Model {
     
    protected $table = 'answers';
    protected $fillable = ['answer','user_id','question_id'];
     
}
?>
Затем в контроллере answers мы пишем следующее:

01
02
03
04
05
06
07
08
09
10
11
12
13
14
<?php
 
namespace Controllers;
use Models\Answer;
 
 
class Answers {
 
    public static function add_answer($answer,$question_id,$user_id){
        $answer = Answer::create(['answer'=>$answer,'question_id'=>$question_id,'user_id'=>$user_id]);return $answer;
    }
}
 
?>
Затем в index.php мы можем создать ответ на вопрос с id, который мы добавили ранее, и user id 2. Только не забудьте импортировать контроллер answers в index.php.

1
2
3
4
5
<?php
 
use Controllers\Answers;
 
    $answers = Answers::add_answer("This is an answer",1,2);
Чтобы предотвратить создание новых записей, закомментируем все другие вызовы в index.php.

Задача 4: Голосование за ответ
Снова выполняем уже знакомые нам шаги.

Так что мы копируем это в модель Upvote в файл project_folder/app/models/upvote.php.

01
02
03
04
05
06
07
08
09
10
11
12
13
<?php 
namespace Models;
 
use \Illuminate\Database\Eloquent\Model;
 
 
class Upvote extends Model {
 
    protected $table = 'upvotes';
    protected $fillable = ['answer_id','user_id'];
      
}
 ?>
Затем в контроллере answers мы импортируем модель Upvote.

use Models\Upvote;

И создаем метод upvote_answer.

1
2
3
4
5
6
<?php
 
    public static function upvote_answer($answer_id,$user_id){
        $upvote = Upvote::create(['answer_id'=>$answer_id,'user_id'=>$user_id]);
        return $upvote;
    }
В index.php мы теперь можем вызвать метод с любым user id, чтобы проголосовать за ответ с id 1.

$upvote = Answers::upvote_answer(1,14);

Задача 5: Получение вопроса с ответами
Для задач, подобных этой, мы можем использовать связи Eloquent.

Связи бывают следующих типов: один к одному, один ко многим, много ко многим и другие.

Когда мы используем эти типы связей, Eloquent предполагает наличие внешнего ключа в моделях вида modelname_id. Для этой задачи тип связи - один ко многим, так как один вопрос может содержать любое количество ответов.

Сначала мы определяем эту связь, добавляя этот метод в нашу модель вопросов.

1
2
3
4
5
6
<?php
 
public function answers()
{
    return $this->hasMany('\Models\Answer');
}
Затем в контроллере question мы добавляем метод для получения вопросов вместе с ответами.

1
2
3
4
5
6
7
<?php
 
public static function get_questions_with_answers(){
  
    $questions = Question::with('answers')->get()->toArray();
    return $questions;
}
Он возвращает вопросы и соответствующие им ответы.

В index.php мы комментируем все другие вызовы и вызываем:

$all = Questions::get_questions_with_answers();

Мы можем выполнить var_dump или print_r с переменной $all, чтобы увидеть результаты.

Задача 6: Получение всех вопросов и пользователей, которые их задавали 
Это связь вида один к одному, так как один вопрос принадлежит одному пользователю, так что мы добавим это в модель вопроса.

1
2
3
4
5
6
<?php
 
public function user()
{
    return $this->belongsTo('\Models\User');
}
Затем мы создаем метод в контроллере вопросов и используем метод with модели question.

1
2
3
4
5
6
7
<?php
 
public static function get_questions_with_users(){
 
    $questions = Question::with('user')->get()->toArray();
    return $questions; 
}
В index.php комментируем все остальное и выполняем это:

$all_with_users = Questions::get_questions_with_users();

Задача 7: Получить один вопрос с ответами и голосами
Сначала мы определяем связь между ответами и голосами. Ответ может иметь много голосов, поэтому связь будет вида один ко многим.

Так что мы добавляем следующий метод в модель Answer:

1
2
3
4
5
6
<?php
 
public function upvotes()
{
    return $this->hasMany('\Models\Upvote');
}
Затем в контроллере вопросов мы создаем следующий метод:

1
2
3
4
5
6
7
<?php
 
public static function get_question_answers_upvotes($question_id){
 
    $questions = Question::find($question_id)->answers()->with('upvotes')->get()->toArray();
    return $questions;
}
Как и в прошлых шагах, мы снова комментируем все вызовы в index.php и выполняем:

$one_question = Questions::get_question_answers_upvotes(1);

Мы можем вывести переменную $one_question чтобы увидеть результаты.

Задача 8: Посчитать все вопросы по определенному пользователю
Сначала мы импортируем модель вопросов в контроллер Users:

use Models\Question;

Затем пишем следующий метод:

1
2
3
4
5
6
7
<?php
 
public static function question_count($user_id){
 
    $count = Question::where('user_id',$user_id)->count();
    return $count;
}
В index.php мы снова все комментируем и выполняем эту строчку:

$user_question_count = Users::question_count(1);

Будет возвращено количество вопросов, которые были добавлены пользователем с id 1.

Мы можем вывести переменную $user_question_count, чтобы увидеть результаты.

Задача 9: Обновление ответа пользователем
Процесс обновления записи в ORM Eloquent довольно прост. Сначала мы находим запись, затем изменяем и сохраняем.

Сейчас в контроллере для ответов мы добавим этот метод:

1
2
3
4
5
6
7
8
<?php
 
public static function update_answer($answer_id,$new_answer){
    $answer = Answer::find($answer_id);
    $answer->answer = $new_answer;
    $updated = $answer->save();
    return $updated;
}
В index.php мы можем закомментировать все прошлые вызовы и обновить ответ с id 1 следующим образом:

$update_answer = Answers::update_answer(1,"This is an updated answer");

Будет возвращено булево значение - true - если обновление прошло успешно.

Задача 10: Удаление вопроса (мягкое удаление)
В этой последней задаче мы реализуем мягкое удаление в Eloquent.

Сначала в модели вопросов мы используем трейт SoftDeletes.

use Illuminate\Database\Eloquent\SoftDeletes;

Затем после объявления класса мы добавляем эту строчку:

use SoftDeletes;

Затем мы добавляем deleted_at в свойство модели protected $dates. Это шаги обязательны.

protected $dates = ['deleted_at'];

Наша модель вопроса теперь выглядит следующим образом:

01
02
03
04
05
06
07
08
09
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
<?php 
namespace Models;
use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Question extends Model {
 
use SoftDeletes; 
    protected $table = 'questions';
    protected $fillable = ['question','user_id'];
 
 
    public function answers()
    {
        return $this->hasMany('\Models\Answer');
    }
     
     
    public function user()
    {
        return $this->belongsTo('\Models\User');
    }
 
 }
 
 ?>
Затем мы добавляем метод delete_question в контроллер вопросов.

1
2
3
4
5
6
7
8
9
<?php
 
public static function delete_question($question_id){
 
    $question = Question::find($question_id);
    $deleted = $question->delete();
    return $deleted; 
 
}
И запускаем index.php:

$delete = Questions::delete_question(1);

Поздравляю! Вы только что создали полностью функциональный бек-енд с использование компонента Illuminate Eloquent. При этом не потребовалось писать много кода.

Код для этой статьи можно найти на GitHub.

Заключение
Illuminate так же поставляется с Query Builder, который вы можете использовать для более сложных запросов к базе данных и вам наверняка стоит поэкспериментировать с ним.

Единственная вещь, которая отсутствует в компоненте Illuminate Database - это миграции, которые являются приятной возможностью в Laravel и Lumen. Попробуйте использовать оба этих фреймворка в своих приложениях, чтобы понять их приемущества.

Вы можете узнать больше об Eloquent на странице с официальной документацией.
