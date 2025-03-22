USE kahuna;

CREATE TABLE Product (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    serial VARCHAR(250) NOT NULL UNIQUE,
    warranty INT NOT NULL
);

CREATE TABLE Sales (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    productId INT NOT NULL,
    regDate DATETIME NOT NULL
);

CREATE TABLE User (
    id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(250) NOT NULL,
    password        VARCHAR(255) NOT NULL,
    accessLevel     CHAR(10) NOT NULL DEFAULT 'user'
);

CREATE TABLE AccessToken (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    birth TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT c_accesstoken_user
        FOREIGN KEY (userId) REFERENCES User (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

ALTER TABLE Sales
    ADD CONSTRAINT FK_User_TO_Sales
        FOREIGN KEY (userId)
        REFERENCES User (id);

ALTER TABLE Sales
    ADD CONSTRAINT FK_Product_TO_Sales
        FOREIGN KEY (productId)
        REFERENCES Product (id);