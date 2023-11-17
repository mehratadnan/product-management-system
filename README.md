# product-management-system

## Import Reqeust
Creating a Laravel artisan command that periodically import an XML file from a specified URL, processes its content, and updates the products of a merchant in table in the database. 

## Scenarios
- The merchant sends a request to update his items on the database
- This request contains a link to files. This link may contain an Excel, Csv, etc. file
- The command identifies and processes requests to update, add, or delete necessary products for each merchant.
- The command can also write to the history table if an error occurs or update status and import request status messages if success fails. 
- The command also writes a description into the terminal for each processing request.

## Before you start

- Create two databases "myProject" and "myProjectTest"
- DB user name = adnan
- DB password = Cda2QmLzLoWX*Kb4
- Postman collection is given right next env for just import request crud operations
- To create tables and seed data please run "php artisan migrate:refresh --seed" in command line
- To run command please run "php artisan import:products" in command line
- Use Postman collection to add new requests after you run import:products command for the first time
- To run unittest for command please run "php artisan test tests/Commands" in command line
- To run unittest for import reqeust controller please run "php artisan test tests/Unit" in command line
