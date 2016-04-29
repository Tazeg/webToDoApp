<?php
//----------------------------------------------------------------------
// JeffProd - Web ToDo app
//----------------------------------------------------------------------
// AUTHOR	: Jean-Francois GAZET
// WEB 		: http://www.jeffprod.com
// TWITTER	: @JeffProd
// MAIL		: jeffgazet@gmail.com
// LICENCE	: GNU GENERAL PUBLIC LICENSE Version 3, June 2007
//----------------------------------------------------------------------

define('_SQLITE_DB', SQLITE_DB);

/**
 * Class Database for SQLite
 */
class Database
    {
    const SQLITE_DB = _SQLITE_DB;
    private static $instanceSQLite;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO('sqlite:'.SQLITE_DB);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS "tags" (
                `id_tag`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                `libelle`	TEXT NOT NULL UNIQUE
                )');
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS "taches_tags" (
                `id_tache`	INTEGER NOT NULL,
                `id_tag`	INTEGER NOT NULL,
                PRIMARY KEY(id_tache,id_tag),
                FOREIGN KEY(`id_tache`) REFERENCES taches(id_tache),
                FOREIGN KEY(`id_tag`) REFERENCES tags ( id_tag )
                )');
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS "taches" (
                 `id_tache`  INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                 `libelle`   TEXT NOT NULL,
                 `note`  TEXT,
                 `date_start`  TEXT,
                 `date_deadline`  TEXT,
                 `date_done` TEXT,
                 `date_creation` TEXT,
                 `priorite`  INTEGER DEFAULT 0,
                 `done`  INTEGER DEFAULT 0
                 )');
            $this->pdo->query('PRAGMA foreign_keys = ON');
            $this->pdo->query('PRAGMA auto_vacuum = FULL');
            }
        catch(PDOException $e) {
            echo 'class Database: '.$e->getMessage();
            die();
            }
        }

    function __destruct() {
        $this->close();
        }        

    public static function getInstance()
        {
        if (!self::$instanceSQLite instanceof self) {
            self::$instanceSQLite = new self();
            }
        return self::$instanceSQLite;
        }
        
    public function select($query,$params=array()) {
        // Requête de type SELECT
        // ENTREE : $query = la requête SQL en texte (ex : 'SELECT * FROM table WHERE id=?')
        // ENTREE : $params (facultatif si pas de ? dans la requête) = un tableau PHP des paramètres (ex : array('1'))
        // SORTIE : un tableau associatifs des résultats
        $cursor = $this->pdo->prepare($query);
        $cursor->execute($params);
        $tab = $cursor->fetchAll();
        $cursor->closeCursor();
        return $tab;
        }
        
    public function execute($query,$params=array()) {
        // Pour tout type de requête "préparée" (sauf SELECT)
        // ENTREE : $query = la requête SQL en texte (ex : 'DELETE FROM table WHERE id=? AND url=?')
        // ENTREE : $params (facultatif si pas de ? dans la requête) = un tableau PHP des paramètres (ex : array(1,'www.test.com'))
        // SORTIE : retourne TRUE en cas de succès ou FALSE si une erreur survient.
        $cursor = $this->pdo->prepare($query);
        return @$cursor->execute($params);
        }        
        
    public function exec($query) {
        // Pour tout type de requête non préparée (sauf SELECT)
        // ENTREE : $query = la requête SQL
        // SORTIE : le nombre de lignes affectées
        return $this->pdo->exec($query);
        }

    public function getLastError() {
        // SORTIE : le dernier message d'erreur produit par SQLite
        $msg = $this->pdo->errorInfo();
        return $msg[2];
        }
		
	public function beginTransaction() {
		$this->pdo->beginTransaction();
		}
		
	public function commit() {
		$this->pdo->commit();
		}		

    public function lastInsertId(){
        return $this->pdo->lastInsertId();
        }
	
    public function close() {
        unset($this->pdo);
        self::$instanceSQLite=null;
        }

    } // class Database
