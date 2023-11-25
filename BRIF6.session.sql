USE ELECNACV2;
-- UPDATE Users SET username = WHERE username = username  
CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(255) UNIQUE,
    Email VARCHAR(255) UNIQUE,
    Password VARCHAR(255),
    Role ENUM('admin', 'user') DEFAULT 'user',
    Verified BOOLEAN DEFAULT FALSE
);
-- Add more users as needed
INSERT INTO Users (Username, Email, Password, Role, Verified)
VALUES (
        'Admin',
        'admin@test.com',
        'admin123',
        'admin',
        TRUE
    ),
    (
        'User',
        'user@test.com',
        'user123',
        'user',
        TRUE
    ),
    (
        'User1',
        'user1@test.com',
        'user123',
        'user',
        FALSE
    );
CREATE TABLE Categories (
    CategoryID INT PRIMARY KEY AUTO_INCREMENT,
    CategoryName VARCHAR(255)
);
-- Add more categories as needed
INSERT INTO Categories (CategoryID, CategoryName)
VALUES (1, 'Electronics'),
    (2, 'Robotics');
CREATE TABLE Products (
    ProductID INT PRIMARY KEY AUTO_INCREMENT,
    Reference VARCHAR(255) NOT NULL,
    Image VARCHAR(255) NOT NULL,
    Barcode VARCHAR(255) NOT NULL,
    Label VARCHAR(255) NOT NULL,
    PurchasePrice DECIMAL(10, 2) NOT NULL,
    FinalPrice DECIMAL(10, 2) NOT NULL,
    PriceOffer VARCHAR(255) NOT NULL,
    Description TEXT NOT NULL,
    MinQuantity INT NOT NULL,
    StockQuantity INT NOT NULL,
    Category VARCHAR(255) NOT NULL,
    Hidden BOOLEAN DEFAULT FALSE NOT NULL,
);
INSERT INTO Products (
        Image,
        Reference,
        Label,
        Barcode,
        PurchasePrice,
        FinalPrice,
        PriceOffer,
        Description,
        MinQuantity,
        StockQuantity,
        Category,
    )
VALUES (
        'img/laptopimg.jpg',
        'ABC123',
        'Product1',
        '123456789',
        10.99,
        15.99,
        '2.00',
        'Product description goes here.',
        5,
        100,
        'Electronics',
    );