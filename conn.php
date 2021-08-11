   <?php
try
{
$conn=new PDO("mysql:host=localhost;dbname=lhp","root","");
} catch (PDOException $ex) {
echo 'Exception'.$ex->getMessage();
}