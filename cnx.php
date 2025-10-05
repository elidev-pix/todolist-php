
<?php 

/**
 * Connexion à la base de données (PhPmyAdmin)
 */

$host='localhost';
$dbname = 'todo';
$username = 'root';
$password= '';

$options = array(
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
);
 

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=UTF8", $username,$password,$options);
} catch(PDOException $e){
    echo 'Erreur de connexion:'.$e->getMessage();
}



?>