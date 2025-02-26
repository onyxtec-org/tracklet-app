# boilerplate
## After cloning the Project:
 #### Composer Update.
 #### Copy .env.example file to .env on the root folder.
 #### Npm Install.
 #### Npm Run Dev
 #### Npm Run Watch
 #### Run Command php artisan key:generate.
 #### Run php artisan migrate.
 #### Run php artisan serve.
 #### Laravel version 10 that requires PHP version 8.2 

 #### Run Required Seeders
    To set up default users, devices, and colors, run the following seeders:

    php artisan db:seed --class=UserSeeder
    This creates two users:
    Admin User → Can see all device requests and submit new requests.
    Regular User → Can only fill the device request form.

    php artisan db:seed --class=DeviceSeeder
    This seeder populates devices, device versions, and colors for users to select from.

 #### Default Credentials
    Admin Account
    Email: admin@truview.com
    Password: asdfasdf
    User Account
    Email: user@truview.com
    Password: asdfasdf

 #### Permissions & Access
    Admin (admin@truview.com)
    ✅ Can view all requests
    ✅ Can create a device request
    ✅ Can manage devices & settings

    User (user@truview.com)
    ✅ Can only fill the device request form
