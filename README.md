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

1. api/register  signup
2. api/login  login
3. api/me  get the logged in user
4. api/forget_password  forgot password
5. api/reset_password  reset password
6. api/attendance to record attendance
7. api/attendance_report to generate attendance report
8. api/logout  logout





