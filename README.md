# Stock Control System: PHP/MySQL/JavaScript/AJAX/JQuery

Author: C. Gwatkin

* [GitHub](https://github.com/CaiGwatkin)
* [LinkedIn](https://www.linkedin.com/in/caigwatkin/)

## Installation instructions

### Requirements

Docker Toolbox

### Instructions

1. Download all files
1. Use "Docker Quickstart Terminal" in root folder (containing `docker-compose.yml` file) to install and run server
    1. Enter command `docker-compose up`
    1. Wait for packages to be installed and containers to run
1. For web-app use
    1. For URL: on Windows with Docker, use `192.168.99.100:8000`; on Linux/Mac, use `localhost:8000`
    1. Log in with username 'TheToolman' and password 'TheToolman'
1. For database view
    1. Use port `:8888`

## Instructions for end-user

1. Navigate to webpage (`192.168.99.100:8000` or `localhost:8000`)
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
