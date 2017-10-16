# Stock Control System: PHP/MySQL/JavaScript/AJAX/JQuery

Author: C. Gwatkin

* [GitHub](https://github.com/CaiGwatkin)
* [LinkedIn](https://www.linkedin.com/in/caigwatkin/)

## Specifications

Application for Toolshed Inc.

The application is a simple look-up of products in the backend stock control system.

The system consists of 4 parts: Login, Registration, Homepage/Welcome, and Search screens.

## Design choices

At the backend, the server uses PHP in an MVC framework. This provides reasonable flexibility and expandability 
whilst being easy to read and maintain.

At the frontend, the web app uses JS, AJAX, and JQuery to perform client-side validation and data manipulation. This 
allows for dynamic and responsive design which the user can interact with easily. Pages don't reload as often, 
offering a more seamless user experience.

## Database schema and relations

The MySQL database consists of two relations:

1. user
    * id - User ID number
    * username - Username
    * pwd - User password
    * name - User's name
1. product
    * id - Product ID number
    * sku - Stop Keeping Unit (SKU) identifier
    * name - Product name
    * cost - Product cost
    * category - Product category
    * stock - Stock quantity

## Installation instructions

### Requirements

Docker Toolbox

### Instructions

1. Download all files
1. Use "Docker Quickstart Terminal" in root folder (containing `docker-compose.yml` file) to install and run server
    1. Enter command `docker-compose up`
    1. Wait for packages to be installed and containers to run
1. For web-app use
    1. Navigate to `192.168.99.100:8000`
    1. Log in with username 'TheToolman' and password 'TheToolman'
1. For database view
    1. Navigate to `192.168.99.100:8888`

## Instructions for end-user

1. Navigate to webpage
1. Log in:
    1. If it's your first time, click the "Register" button:
        1. Type your details into the fields shown
            * Username must only contain alphanumeric characters
            * Password must be between 7 and 15 (exclusive) alphanumeric characters and contain at least one upper case 
                letter (no special characters allowed)
            * Password and repeated password must match
        1. Click the "Register" button
    1. If you've already registered, log in with your username and password
1. To search:
    1. Click the "Search" menu item at the top left of the page
    1. Begin to type the name of a product into the "Search" field
        * Products containing that keyword will be shown below
    1. To re-order the products
        1. Click the table header corresponding to the column you would like to order by
        1. Click again to order descending
1. You can log out at any time using the menu item at the top right of the page
