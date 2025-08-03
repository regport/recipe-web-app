# recipe-web-app
This repository contains a responsive, accessible recipe web app built with PHP, MySQL, HTML, CSS, and JavaScript. Developed for the End-of-Module Assignment in the Networks and Web Technologies module, part of the MSc Computer Science program at the University of Liverpool.

# Img Folder

This folder contains images, icons, and other media assets used in the web app.

Add your images here, e.g., logos, food photos, icons.


# Steps to run
- Login to mysql using root user
- Run the database script using below prompt
> mysql>source <where your app folder is>/recipe-web-app/database/recipe-database-dump.sql
- Create a folder called 'recipes' in your XAMPP htdocs directory
- Copy all contents from recipe-web-app folder to this 'recipes' htdocs directory
- Open browser and navigate to localhost/recipes -> you should see the home page of the app.


#  to run unit tests
 - Download and install composer from https://getcomposer.org/doc/00-intro.md
 - Install phpunit 10 or higher
 - Run the below command
 > vendor/phpunit/phpunit/phpunit test/loginTest.php