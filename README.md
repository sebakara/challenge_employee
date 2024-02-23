Laravel Technical assignment

To run the project
clone the repo
create database preferably mysql
run:
1. composer install
2. cp .env.example .env
3. php artisan migrate
4. php artisan serve

endpoints are:

/api/register  signup
/api/login  login
/api/me  get the logged in user
/api/forget_password  forgot password
/api/reset_password  reset password
/api/attendance to record attendance
/api/attendance_report to generate attendance report





