### Guide
This is application that stores users. Users consists of name, surname, email, pesel and set of skills like php, css, html etc...

There are 3 ways of creating new user:
1. CLI 
2. Web http://127.0.0.1:8000/users/add
3. REST API

### Installation
1. Mysql database needed
2. Setup database in .env file DATABASE=
3. run commands:
4.     php bin/console make:migration
5.     php bin/console doctrine:migrations:migrate
6. For Web app and REST request run:
7.     symfony serve

If everything woks ok site should be available at http://127.0.0.1:8000

### CLI

#### 1. Add User
Use this command in terminal in project directory to create user:
    
    php bin/console app:add-user

Or pass parameters with it

    php bin/console app:add-user John Doe johnd@example.com 12345678901 php,css

Skills should be separated by comma ',' ex: 
    
    php, css, java

#### 2. Activate User

For interactive mode use:

    php bin/console app:user-activate

Or pass user identifier as argument:

    php bin/console app:user-activate johnd@gg.pl

For help:

    php bin/console help app:user-activate


### Sending REST request

POST http://127.0.0.1:8000/user/add

Fields list:
    - name
    - surname
    - email 
    - pesel
    - skills

Example request:

Use terminal to execute

    curl -d '{"name":"John", "surname":"Doe", "email":"jd@gg.com", "pesel":"12345678901", "skills":"php,css,java"}' -H "Content-Type: application/json" -X POST http://127.0.0.1:8000/user/add