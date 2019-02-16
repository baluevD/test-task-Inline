
<?

// include files with classes
require_once 'classes/MyLogger.php';
require_once 'classes/PDOAdapter.php';

// just for debugging
function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}

// connection parameters
$host = 'localhost';
$db = 'test';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db";

// create new logger object
$logger = new MyLogger("logs/my_logger.log");

// create new pdo object
$pdoObject = new PDOAdapter($dsn, $user, $pass, $logger);

// get max age from person
$maxAgeQuery = $pdoObject->execute('selectOne','SELECT MAX(age) FROM person');
$maxAge = $maxAgeQuery->{'MAX(age)'};

// select and get random person with age < MAX(age)
$stmtAnyPerson = $pdoObject->prepare('SELECT * FROM person WHERE mother_id IS NULL AND age < ? ORDER BY rand() LIMIT 1');

// Another way to get random person
// $pdoObject->execute('selectAll','SELECT * FROM person
// WHERE mother_id IS NULL
// AND age < (SELECT MAX(age) FROM person)  ORDER BY RAND() LIMIT 1');

$anyPersonQuery = $pdoObject->selectPrepared($stmtAnyPerson,[$maxAge]);
// get id of random person with age < MAX(age)
$anyPersonId =  $anyPersonQuery[0]->{'id'};

// update raw with person which was selected earlier
$stmtUpdAnyPerson = $pdoObject->prepare('UPDATE person SET age = ? WHERE id = ?');
$pdoObject->executePrepared($stmtUpdAnyPerson, [$maxAge,$anyPersonId]);

// get persons with max age
$maxAgePersons = $pdoObject->execute('selectAll','SELECT lastname, firstname FROM person
WHERE age = (SELECT MAX(age) FROM person)');

// checkeng queries and values
// print_r2($pdoObject);
//
// print_r2($maxAgeQuery);
// print_r2($maxAge);
// print_r2($stmt);
// print_r2($anyPersonQuery);
// print_r2($anyPersonId);
// print_r2($maxAgePersons);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <title>Test Task Inline</title>
</head>
<body>
  <div class="container">
    <h1>Список персон максимального возраста <?php echo $maxAge;?></h1>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <td>Фамилия</td>
            <td>Имя</td>
            <td>Возраст</td>
          </tr>
        </thead>
        <tbody>
          <?php
            $maxAgePersonsWithAge = $pdoObject->execute('selectAll','SELECT lastname, firstname, age FROM person
            WHERE age = (SELECT MAX(age) FROM person)');
            foreach ($maxAgePersonsWithAge as $row) {
          ?>
          <tr>
            <td><?php echo $row->{'lastname'}; ?></td>
            <td><?php echo $row->{'firstname'}; ?></td>
            <td><?php echo $row->{'age'}; ?></td>
          </tr>
          <?php  }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
