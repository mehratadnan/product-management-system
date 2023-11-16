# product-management-system

## Import Reqeust
Creating a Laravel artisan command that periodically import an XML file from a specified URL, processes its content, and updates the products of a merchant in table in the database. 

## Before you start

- Create two databases "myProject" and "myProjectTest"
- DB user name = adnan
- DB password = Cda2QmLzLoWX*Kb4
- Postman collection is given right next env for just import request crud operations
- To seed data please run "php artisan migrate:refresh --seed" in command line
- To run command please run "php artisan import:products" in command line
- To run unittest for command please run "php artisan test tests/Commands" in command line
- To run unittest for import reqeust controller please run "php artisan test tests/Unit" in command line
