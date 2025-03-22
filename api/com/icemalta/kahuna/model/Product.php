<?php
namespace com\icemalta\kahuna\model;
require 'com/icemalta/kahuna/model/DBConnect.php'; // ha van composer autoloader, ez nem kell

use \JsonSerializable; /*conv.Product~>Json~~>Api.Love<3*/
use \PDO;
use com\icemalta\kahuna\model\DBConnect;


//$warranty->warranty===warrantyLength



Class Product implements JsonSerializable {
                    private static $db;
                    private int|string $id = 0;
                    //userId miert nem kell? usert serial szamhoz?
                    private string $name;
                    private string $serial;
                    private int $warranty = 0; //mennyi van hatra ezt hol es miert es hogy szamoljuk ki?
                    //                    private DateTime|string|null $registrationDate -> $regstrationDate===saveProduct date -el!


                    public function __construct(string $serial, string $name, int $warranty, int|string $id = 0) {
                                                $this->serial = $serial;
                                                $this->name = $name;
                                                $this->warranty = $warranty;
                                                $this->id = $id;
                                                self::$db = DBConnect::getInstance()->getConnection();
                    }
                                                  
                                                             
                                                    

public static function save(Product $product): Product { //Szimpla Table in DB - nem minden cucc.

                            if ($product->getId() === 0) {
                                        // Először nézzük meg, hogy létezik-e már ilyen termék a serial alapján
                                        $sql = 'SELECT id FROM Product WHERE serial = :serial';
                                        $sth = self::$db->prepare($sql);
                                        $sth->bindValue(':serial', $product->getSerial());
                                        $sth->execute();


                                        if ($sth->rowCount() > 0) {
                                            // Ha létezik, frissítjük az id-t a meglévő termékhez
                                            $existingProduct = $sth->fetch(PDO::FETCH_ASSOC);
                                            $product->setId($existingProduct['id']);
                                        } else {
                                        // Ha nem létezik, akkor INSERT
                                        $sql = 'INSERT INTO Product(name, serial, warranty) 
                                                VALUES(:name, :serial, :warranty)';
                                                $sth = self::$db->prepare($sql);
                                                // Bindoljuk az értékeket
                                                $sth->bindValue(':name', $product->getName());
                                                $sth->bindValue(':serial', $product->getSerial());
                                                $sth->bindValue(':warranty', $product->getWarranty());
                                        }
                            } else {
                                // Ha már létezik id, akkor frissítjük
                                $sql = 'UPDATE Product
                                            SET name = :name, serial = :serial, warranty = :warranty
                                            WHERE id = :id';
                                            $sth = self::$db->prepare($sql);
                                            // Bindoljuk az értékeket
                                            $sth->bindValue(':id', $product->getId());
                                            $sth->bindValue(':name', $product->getName());
                                            $sth->bindValue(':serial', $product->getSerial());
                                            $sth->bindValue(':warranty', $product->getWarranty());
                                }
                                // Végrehajtjuk az SQL műveletet
                                $sth->execute();
                                // Ha új rekordot hoztunk létre, beállítjuk az ID-t
                                if ($sth->rowCount() > 0 && $product->getId() === 0) {
                                    $product->setId(self::$db->lastInsertId());
                                }
                return $product;
}




    public static function load(): array {
                                    self::$db = DBConnect::getInstance()->getConnection();
                                    $sql = 'SELECT serial,
                                                    name,
                                                    warranty,
                                                    id
                                            FROM   Product';
                                            $sth = self::$db->prepare($sql);
                                            $sth->execute();
                                            $products = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Product(...$fields)); /*passing these to constructor*/                  
                            return $products;
    }






    public static function delete(int $id): bool { // ezt en irtam ide, nem tudom kell e ide. tobbi resz video alapjan
                                    self::$db = DBConnect::getInstance()->getConnection();
                                                $sql = 'DELETE FROM Product
                                                        WHERE id = :id';
                                                        $sth = self::$db->prepare($sql);
                                                        $sth->bindValue('id', $id);
                                                        $sth->execute();
                                    return $sth->rowCount() > 0;
        }



    /*az egesz objektumot konvertald!*/
    public function jsonSerialize(): array {
                                    return get_object_vars($this);
    }

/*G&S============================================================*/
    public function getId(): int {
            return $this->id;
        }

    public function setId(int $id): self {
            $this->id = $id;
            return $this;
        }


/*name*/
        public function getName(): string {
            return $this->name;
        }

        public function setName(string $name): self {
            $this->name = $name;
            return $this;
        }


/*serial*/
        public function getSerial(): string {
            return $this->serial;
            }

        public function setSerial(string $serial): self {
            $this->serial = $serial;
            return $this;
            }


/*warranty*/
        public function getWarranty(): int {
            return $this->warranty;
        }

        public function setWarranty(int $warranty): self {
            $this->warranty = $warranty;
            return $this;
        }


}