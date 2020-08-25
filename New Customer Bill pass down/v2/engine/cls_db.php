<?php
Class clsDB{
  private $Host;
  private $Database;
  private $Port;
  private $User;
  private $Password;
  protected $objDB;
  
  function clsDB(){
    require("conf/db.php");
    $this->Host=$DBHost;
    $this->Database=$DBDatabase;
    $this->Port=$DBPort;
    $this->User=$DBUser;
    $this->Password=$DBPassword;
    
    $this->objDB=new PDO('mysql:host='.$this->Host.';port='.$this->Port.';dbname='.$this->Database.'', $this->User, $this->Password);
  }
  
  function Query($sql){
    $stmt=$this->objDB->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetchAll();
    $stmt->closeCursor();
    return $result;
  }
}
?>