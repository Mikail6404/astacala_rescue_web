@echo off
echo Creating astacalarescue database...

rem Try to create database using different MySQL paths
if exist "C:\xampp\mysql\bin\mysql.exe" (
    "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS astacalarescue;"
    echo Database created successfully via XAMPP
    "C:\xampp\mysql\bin\mysql.exe" -u root astacalarescue < "database\astacalarescue.sql"
    echo Database imported successfully
) else if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" (
    "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p -e "CREATE DATABASE IF NOT EXISTS astacalarescue;"
    echo Database created successfully via MySQL
    "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p astacalarescue < "database\astacalarescue.sql"
    echo Database imported successfully
) else (
    echo MySQL not found. Please ensure XAMPP or MySQL is installed and running.
    echo Manual steps:
    echo 1. Open phpMyAdmin or MySQL Workbench
    echo 2. Create database 'astacalarescue'
    echo 3. Import the database/astacalarescue.sql file
    pause
)
