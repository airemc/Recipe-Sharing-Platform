# Recipe Sharing Platform 🍳

This project is an interactive web application based on PHP and MySQL. It's a dynamic platform where users can sign up, share their own recipes with images, and review and rate other recipes.
A dynamic, full-stack web application built with PHP and MySQL. This platform allows users to share their culinary creations, discover new recipes, search by ingredients, and engage with the community through ratings and reviews.

**🚀 Features**
This project implements a complete CRUD (Create, Read, Update, Delete) system with the following key features:

**👤 User System**

Secure Authentication: User registration and login system using password_hash() (Bcrypt).

Session Management: Secure session handling for logged-in users.

Role-Based Access: Users can only edit or delete their own recipes.

**🍲 Recipe Management**

Create: Users can submit recipes with a title, ingredients, step-by-step instructions, categories, and image uploads.

Read:

Dashboard: Displays latest recipes with a card-based layout.

Detail View: A clean, two-column layout for ingredients and instructions, featuring the recipe image.

Categorization: Filter recipes by categories (e.g., Soups, Desserts) with dynamic icons.

Update: Full editing capability for existing recipes (including image replacement).

Delete: Secure deletion process with confirmation prompts.

**🔍 Search & Discovery**

Search Engine: Real-time search functionality that queries both recipe names and ingredient lists.

Navigation: Modern sidebar navigation for easy access to all sections.

**⭐ Interaction**

Rating System: 5-star rating system.

Comments: Users can leave reviews and comments on recipes.

**🛠️ Tech Stack**
Backend: PHP (Native, using PDO for secure database interactions).

Database: MySQL (Relational database with Foreign Key constraints).

Frontend: HTML5, CSS3 (Custom responsive design with Flexbox/Grid), JavaScript (DOM manipulation & validation).

Icons: Font Awesome 6.

Server Environment: Apache (Compatible with XAMPP/WAMP/LAMP).
