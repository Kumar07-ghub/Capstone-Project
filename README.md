# Indian Grocery Store – Capstone Project

An online Indian grocery store web application that allows customers to browse products, add items to their cart, and place orders. Admin users can manage inventory and users. It supports PDF invoice generation, email order confirmation, and secure sandbox payment using Stripe.

## Technologies Used

- PHP – Core backend logic
- MySQL – Relational database
- HTML5, CSS3, JavaScript – Frontend
- Bootstrap 5 – Responsive UI framework
- XAMPP – Local development environment

##  Local Development Setup

Follow these steps to set up and run the project locally:

###  Requirements

- [XAMPP](https://www.apachefriends.org/) (Install and run Apache & MySQL)
- A web browser
- A code editor like [VS Code](https://code.visualstudio.com/)

### Setup Instructions

1. Start Apache and MySQL:
   - Launch XAMPP Control Panel
   - Start both Apache and MySQL

2. Place Project in htdocs:
   - Copy the entire project folder (`Capstone-Project`) to:
   
     C:\xampp\htdocs\

3. Create and Import the Database:
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Create a database named: `grocery_store`
   - Import the provided `.sql` file (located in the root folder or `database/`)

4. Database Connection Setup:
   - Open `includes/db.php` and ensure the credentials are correct:
     php code
     $conn = new mysqli("localhost", "root", "", "grocery_store");
     

5. Configure PHPMailer (SMTP Emails):
   - Update SMTP credentials in `includes/send_mail.php`:

     php code

     $mail->isSMTP();
     $mail->Host = 'smtp.example.com';
     $mail->SMTPAuth = true;
     $mail->Username = 'your_email@example.com';
     $mail->Password = 'your_email_password';
     ```

6. Configure Stripe Sandbox Payment:
   - Set your test keys in your Stripe configuration file:
     ```php
     \Stripe\Stripe::setApiKey('your_stripe_test_secret_key');
     ```
   - Make sure the `publishable key` is correctly placed in your payment form.

7. Run the App:
   - Open your browser and go to:
     ```
     http://localhost/Capstone-Project/index.php
     ```



##  Admin Login Info

- Default Admin Credentials:
  - Username : `admin@example.com`
  - Password: `Admin@123password`

> If the password above doesn’t work, generate a new hashed password using the `password_hash.php` file in the project root. Replace the stored hash in the database with the new one.



## Libraries / Dependencies Used

- PHPMailer-master – For SMTP mail integration
- fpdfg – For generating downloadable PDF invoices
- vendor/ – Contains Stripe and related dependencies

## Features Implemented (Sprint 3)

- Deployed in Infinity free hosting site
- Stripe sandbox payment integration  
- SMTP-based email confirmation to customers  
- PDF invoice generation post order  
- Secure login using hashed passwords  
- Responsive frontend layout  
- Slideshow added for hero section images
