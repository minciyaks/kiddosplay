# 🤖 KiddosPlay - Early Childhood Learning Hub

> **BCA Semester 5 Mini Project**

### 🌐 LIVE DEMO: [kiddosplay.infinityfreeapp.com](http://kiddosplay.infinityfreeapp.com/)

## Compatibility

> **This project is optimized for laptop and desktop devices.**

## Demo Login (Evaluation Only)

The application is customized based on the child’s age selected during registration.
To explore each age-specific interface, use the following demo accounts:

- Age 2–3  
  Username: Demo_23  
  Password: Demo123

- Age 4  
  Username: Demo_4  
  Password: Demo123

- Age 5  
  Username: Demo_5  
  Password: Demo123
  
**Mini Project for [BCA/Semester 5]**

## 🌟 Project Overview

KiddosPlay is a secure, interactive full-stack web application designed for **early childhood education (Ages 2-5)**. It provides a safe, gamified learning environment for children while demonstrating a robust **tiered security model** required for sensitive child data applications.

## 🧩 Website Navigation & Functional Modules

The core learning activities are structured into five distinct modules, all accessible from the main **Room Area (Home Page)**.

| Functional Module Name | User-Facing Activity | Home Page Location | Learning Goal |
| :--- | :--- | :--- | :--- |
| **TuneTown** | Music & Rhymes | **Radio** Hotspot | Auditory learning and simple language structure. |
| **LetterBeats** | Phonics & Letters | **Toy Box** Hotspot | Early literacy and sound association. |
| **BrainPlay** | Quiz Section | **Computer** Hotspot | Logic, counting, and general knowledge quizzes. |
| **ColorFun** | Drawing & Coloring | **Whiteboard** Hotspot | Fine motor skills, creativity, and color recognition. |
| **StoryLand** | Interactive Stories | **Bookshelf** Hotspot | Language comprehension and narrative sequencing. |

***

## 🔐 System Architecture & Tiered Access Control

The project is built around **three distinct user roles** and a mandatory **Admin Approval** gate, ensuring no data is accessible until the parent account is verified. 

### Role Breakdown and Access Flow

| Role | Access Control & Verification | Key Responsibilities & Features |
| :--- | :--- | :--- |
| **1. Admin** | **Separate Login** (`admin/handle_admin_login.php`) | **System Gatekeeper.** Reviews uploaded **Parent/Child certificates** and manually sets new user accounts to **'approved'** status. Manages overall system health. |
| **2. Parent/Teacher** | **Initial Registration** $\rightarrow$ **Admin Approval Required** $\rightarrow$ **Login** | **Main User.** Manages child profile, provides the **Parental Control Password**, and monitors progress. **Cannot log in until accepted by Admin.** |
| **3. Child/User** | **Access via Parent Login** (after approval) | **Core Learner.** Accesses the 5 Core Modules. The learning environment is **Age-Customized** based on their registered age (e.g., age 3 sections). |

### Advanced Monitoring & Security Features

* **Screen Time Limit:** A hard limit of **30 minutes** of continuous play is enforced.
* **Screen Lock:** Upon hitting the time limit, the current page locks and requires the **Parental Control Password** to unlock, ensuring parental awareness.
* **Progress Calendar:** Parents can view detailed daily activity, including **time spent on each section**, using a calendar interface.

***

## 🛠️ Tech Stack & Security

### Technologies Used

| Category | Component |
| :--- | :--- |
| **Backend** | **PHP** (MySQLi/PDO) |
| **Database** | **MySQL** |
| **Frontend** | **HTML5, CSS3, JavaScript** |

### Critical Security Measures

* **Credential Protection:** Database connection credentials are secured in a separate **`db_config.php`** file and excluded from this public repository using **`.gitignore`**.
* **SQL Injection Prevention:** All data handling utilizes **Prepared Statements** to prevent database exploits.
* **Authentication:** All administrative and learning pages enforce **session authentication** and **role validation**.

***

## ⚙️ Local Setup Instructions (For Reviewers)

To run this project locally using **XAMPP** or a similar stack:

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/minciyaks/kiddosplay.git
    ```
2.  **Database Setup:**
    * Create a new MySQL database named `kiddosplay`.
    * **Import the Project Schema:** Import the provided database structure file (e.g., `kiddosplay.sql`) via phpMyAdmin.
3.  **Configuration File:**
    * Create a file named **`db_config.php`** in the project root.
    * Fill it with your local connection details (`localhost`, `root`, etc.).
4.  **Run:** Navigate to the project's entry point (`http://localhost/kiddosplay/index.html`).

    **Note:** To test a working user account, you must manually set a registered user's status to 'approved' in the MySQL database.


## 👤 Authors / Project Team

This project was developed by the following students :

* Minciya K S
* Akhilesh K Asokan 
* Sreema Manoj 


## 🙏 Acknowledgements

* **Font Awesome:** Used for all icons in the web application.

## 👥 Authorship

This repository contains the original work developed collaboratively by the listed contributors as part of a BCA Semester 5 mini project.

## 📄 License

This project is intended for educational and portfolio purposes.

  

