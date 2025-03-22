<?php
namespace com\icemalta\kahuna\model;

use DateTime;
use \JsonSerializable;
use \PDO;

class Sale implements JsonSerializable {
                    private static $db;
                    private int $id = 0;
                    private int $userId = 0;
                    private int $productId = 0;
                    private DateTime $regDate;

                    public function __construct(int $userId, int $productId, int $id = 0, ?DateTime $regDate = null) {
                        $this->id = $id;
                        $this->userId = $userId;
                        $this->productId = $productId;
                        $this->regDate = $regDate ?? new DateTime(); // Default to now if not provided
                        
                        self::$db = DBConnect::getInstance()->getConnection();
                    }

    public function jsonSerialize(): array {
                                    return [
                                    'id'        => $this->id,
                                    'userId'    => $this->userId,
                                    'productId' => $this->productId,
                                    'regDate'   => $this->regDate->format('Y-m-d H:i:s')
                                            ];
    }



    public static function save(Sale $sale): Sale {
                                self::$db = DBConnect::getInstance()->getConnection();
    
                                // addNewSale akkor is, ha már van azonos userId és productId
                                $sql = 'INSERT INTO Sales (userId, productId, regDate) 
                                        VALUES (:userId, :productId, :regDate)';
                                        $sth = self::$db->prepare($sql);
                                        $sth->bindValue(':userId', $sale->userId);
                                        $sth->bindValue(':productId', $sale->productId);
                                        $sth->bindValue(':regDate', $sale->regDate->format('Y-m-d H:i:s'));
                                        $sth->execute();
                                        // Ha sikerült az insert, állítsuk be az id-t a legutóbb beszúrt rekord id-jára
                                        $sale->id = self::$db->lastInsertId();
                                    return $sale;
    }
    

    // Load all sales
    public static function load(): array {
                                    self::$db = DBConnect::getInstance()->getConnection();
                                    $sql = 'SELECT id, userId, productId, regDate FROM Sales';
                                    $sth = self::$db->prepare($sql);
                                    $sth->execute();

                                return $sth->fetchAll(PDO::FETCH_FUNC, fn($id, $userId, $productId, $regDate) => 
                                    new Sale($userId, $productId, $id, new DateTime($regDate))
                                );
    }


    // Delete a sale by ID
    public static function delete(int $id): bool {
                                    self::$db = DBConnect::getInstance()->getConnection();
                                    $sql = 'DELETE FROM Sales WHERE id = :id';
                                    $sth = self::$db->prepare($sql);
                                    $sth->bindValue(':id', $id);
                                    $sth->execute();
                                return $sth->rowCount() > 0;
    }



    public static function getWarrantyInfo(int $userId, string $serialNbr): ?array {
                                        self::$db = DBConnect::getInstance()->getConnection();
                                        $sql = "SELECT 
                                                    S.regDate, 
                                                    P.id AS productId,
                                                    P.name AS productName, 
                                                    P.serial AS productSerial, 
                                                    P.warranty, 
                                                    U.id AS userId, 
                                                    U.email AS userEmail
                                                FROM Sales S
                                                JOIN Product P ON S.productId = P.id
                                                JOIN User U ON S.userId = U.id
                                                WHERE S.userId = :userId AND P.serial = :serialNbr";

                                                $sth = self::$db->prepare($sql);
                                                $sth->bindValue(':userId', $userId);
                                                $sth->bindValue(':serialNbr', $serialNbr);
                                                $sth->execute();
    
                                                $data = $sth->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll instead of fetch
                            
                                if (!$data) return null;
                                $results = []; // Initialize an array to store results
    
                                foreach ($data as $row) {
                                        // Calculate expiry date for each row
                                        $regDate = new DateTime($row['regDate']);
                                        $expiryDate = (clone $regDate)->modify("+{$row['warranty']} seconds");
                                        $now = new DateTime();
                    
                                        // Calculate remaining time
                                        $interval = $now->diff($expiryDate);
                                        $remainingTimeFormatted = ($now < $expiryDate) 
                                                                ? sprintf('%d nap, %d óra, %d perc, %d másodperc', 
                                                                        $interval->d, $interval->h, $interval->i, $interval->s)
                                                                : 'Garanty over!';
            
                                        // Store each result
                                        $results[] = [
                                                'userId'    => $row['userId'],
                                                'userEmail' => $row['userEmail'],
                                                'productId' => $row['productId'],
                                                'productName' => $row['productName'],
                                                'productSerial' => $row['productSerial'],
                                                'regDate'   => $regDate->format('Y-m-d H:i:s'),
                                                'expiryDate' => $expiryDate->format('Y-m-d H:i:s'),
                                                'remainingTime' => $remainingTimeFormatted
                                                    ];
                                }
                            return $results; // Return an array of results
    }
    
    
    
}
