
## About Project

This repository contains the backend of the LearnZi project — an educational platform designed to help users learn Chinese characters through categorized flashcards, quizzes, OCR, and audio pronunciation.

**for frontend of this project,see:**
    https://github.com/RazieRezaali/app-frontend-learnZi

-------------
## Set up Project

    Follow the steps below to set up the Laravel backend and Python services required for the project.

**Clone the repository**

    git clone https://github.com/RazieRezaali/app-backend-learnZi.git
    cd app-backend-learnZi

**Install the dependencies**

    composer install 

**Copy .env.example to .env:**

    cp .env.example .env 

**Then configure your database in the .env file:**

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password

**Run Migrations and Seeders**

    php artisan migrate
    php artisan db:seed

**Start Laravel Development Server**

    php artisan serve

**To generate the Swagger documentation, run the following command:**
    php artisan l5-swagger:generate
 
## Install Python Dependencies

**Navigate to the pythonFiles/ directory:**

    cd pythonFiles

**Install required packages:**

    pip install -r requirements.txt

**Open two terminals and run each of the following separately:**

    python3 ocr.py
    python3 audio.py

## Notes

    -Ensure MySQL and Python 3 are installed and configured on your system.
    -The Laravel backend communicates with the Python servers via HTTP for OCR and audio features.
    -Authentication is handled via Laravel Sanctum.
    -after generating swagger, you could see the documentation in: http://localhost:8000/api/documentation
