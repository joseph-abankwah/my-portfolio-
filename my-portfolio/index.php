<?php
// PHP Processing Logic - MUST be at the very top before any HTML output or whitespace

// Define your email address where you want to receive messages (for email sending - currently commented out)
$receiving_email_address = 'josephabankwah36@gmail.com'; // IMPORTANT: Change this to your actual email address

// Database credentials
$servername = "localhost"; // Usually 'localhost' for local servers
$username = "root";        // Default WAMP username
$password = "";            // Default WAMP password (empty)
$dbname = "portfolio_db";  // The database name you created (as per previous instructions)

// IMPORTANT: If you're using a web host, these credentials will be different.
// Your hosting provider will give you the correct DB_HOST, DB_USER, DB_PASSWORD, DB_NAME.
// Never use 'root' and an empty password in a production environment!

// Initialize status messages
$status_message = '';
$status_type = ''; // 'success', 'error', 'warning'

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize and validate input
    // Using (string) cast for basic string sanitization as FILTER_SANITIZE_STRING is deprecated.
    // PDO prepared statements provide the primary sanitization for database insertion.
    $name = (string) ($_POST["name"] ?? '');
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = (string) ($_POST["subject"] ?? '');
    $message = (string) ($_POST["message"] ?? '');

    // Basic server-side validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        // Redirect back to this page with an error status
        header("Location: index.php?status=error&message=" . urlencode("Please fill in all fields."));
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect back to this page with an error status
        header("Location: index.php?status=error&message=" . urlencode("Invalid email format."));
        exit();
    }

    // --- Database Insertion ---
    $db_success = false;
    $conn = null; // Initialize connection variable

    try {
        // Create database connection using PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set PDO error mode

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)");

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);

        // Execute the statement
        $stmt->execute();
        $db_success = true; // Flag for successful DB insertion

    } catch(PDOException $e) {
        // In a production environment, you'd log this error instead of displaying it
        error_log("Database Error: " . $e->getMessage());
    } finally {
        // Close connection
        if ($conn) {
            $conn = null;
        }
    }

    // --- Email Sending (OPTIONAL - CURRENTLY COMMENTED OUT TO AVOID ERRORS) ---
    // Uncomment and configure if you have a mail server or use a library like PHPMailer
    $email_sent = false;
    /*
    // Prepare email content
    $email_subject = "New Contact From Your Portfolio: " . $subject;
    $email_body = "You have received a new message from your website contact form.\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Subject: " . $subject . "\n";
    $email_body .= "Message:\n" . $message . "\n";

    // Set email headers
    $headers = "From: Your Portfolio <noreply@yourdomain.com>\r\n"; // IMPORTANT: Change 'yourdomain.com'
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Prevent header injection
    $headers = str_replace(array("\n", "\r", "%0a", "%0d"), '', $headers);

    // Send the email
    if (mail($receiving_email_address, $email_subject, $email_body, $headers)) {
        $email_sent = true;
    } else {
        error_log("Email sending failed for: " . $email); // Log failures
    }
    */


    // --- Final Redirect based on outcomes ---
    // Since email is commented out, the status messages will reflect DB outcome
    if ($db_success) {
        header("Location: index.php?status=success&message=" . urlencode("Your message has been saved successfully!"));
    } else {
        header("Location: index.php?status=error&message=" . urlencode("Oops! Your message could not be saved to the database."));
    }
    exit(); // Always exit after a header redirect
}

// Display status messages from URL parameters if present (after redirect)
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_type = htmlspecialchars($_GET['status']);
    $status_message = htmlspecialchars(urldecode($_GET['message']));
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abankwah Joseph - Creative Web Developer & Designer</title>
    <meta name="description" content="Personal portfolio of Abankwah Joseph, a web developer and designer from Ghana, specializing in modern web applications, UI/UX, graphic design, and photography.">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Basic custom styles for better font smoothing and potential overrides */
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Subtle background shapes for Hero section (More complex CSS/SVG might be needed for exact replication) */
        .hero-bg-shapes::before {
            content: '';
            position: absolute;
            top: 10%;
            left: -5%;
            width: 200px;
            height: 200px;
            background-color: rgba(59, 130, 246, 0.1); /* blue-500 with opacity */
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float1 8s infinite ease-in-out;
        }
        .hero-bg-shapes::after {
            content: '';
            position: absolute;
            bottom: 15%;
            right: -8%;
            width: 250px;
            height: 250px;
            background-color: rgba(139, 92, 246, 0.1); /* purple-500 with opacity, or another accent */
            border-radius: 50%;
            filter: blur(90px);
            z-index: 0;
            animation: float2 10s infinite ease-in-out;
        }

        @keyframes float1 {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(20px); }
        }
        @keyframes float2 {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(25px) translateX(-25px); }
        }

        /* Custom scrollbar for dark theme - optional */
        body::-webkit-scrollbar {
            width: 8px;
        }
        body::-webkit-scrollbar-track {
            background: #2d3748; /* gray-800 */
        }
        body::-webkit-scrollbar-thumb {
            background-color: #3b82f6; /* blue-500 */
            border-radius: 4px;
            border: 2px solid #2d3748;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col">

    <header class="bg-gray-900 text-gray-100 py-4 px-6 md:px-12 fixed w-full z-50 shadow-lg">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="#home" class="text-2xl font-bold text-blue-500">ABANKWAH JOSEPH</a>
            <ul class="hidden md:flex space-x-8">
                <li><a href="#home" class="hover:text-blue-400 transition duration-300">HOME</a></li>
                <li><a href="#about" class="hover:text-blue-400 transition duration-300">ABOUT ME</a></li>
                <li><a href="#projects" class="hover:text-blue-400 transition duration-300">PROJECTS</a></li>
                <li><a href="#services" class="hover:text-blue-400 transition duration-300">SERVICES</a></li>
                <li><a href="#contact" class="hover:text-blue-400 transition duration-300">CONTACT</a></li>
            </ul>
            <a href="#contact" class="hidden md:block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-full transition duration-300 border border-blue-600 hover:border-blue-700">LET'S TALK</a>
            <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none p-2 rounded-md hover:bg-gray-800">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-gray-800 py-4 mt-4 rounded-b-lg shadow-xl">
            <ul class="flex flex-col items-center space-y-4">
                <li><a href="#home" class="block py-2 text-lg hover:text-blue-400">HOME</a></li>
                <li><a href="#about" class="block py-2 text-lg hover:text-blue-400">ABOUT ME</a></li>
                <li><a href="#projects" class="block py-2 text-lg hover:text-blue-400">PROJECTS</a></li>
                <li><a href="#services" class="block py-2 text-lg hover:text-blue-400">SERVICES</a></li>
                <li><a href="#contact" class="block py-2 text-lg hover:text-blue-400">CONTACT</a></li>
                <li><a href="#contact" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-full transition duration-300 border border-blue-600 hover:border-blue-700 mt-4">LET'S TALK</a></li>
            </ul>
        </div>
    </header>

    <main class="flex-grow">
        <section id="home" class="relative h-screen flex items-center justify-center overflow-hidden pt-16 px-6 md:px-12">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-900 to-gray-800 opacity-90 z-0"></div>
            <div class="hero-bg-shapes absolute inset-0 z-0"></div>

            <div class="container mx-auto flex flex-col md:flex-row items-center justify-between z-10 text-center md:text-left">
                <div class="md:w-1/2">
                    <p class="text-blue-400 text-lg md:text-xl mb-2 uppercase tracking-wide">ABANKWAH JOSEPH</p>
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold leading-tight mb-4">
                        HAY! I'M JOSEPH<br>
                        <span class="text-blue-500">I'M A DESIGNER</span>
                    </h1>
                    <p class="text-gray-300 text-lg mb-8 max-w-lg mx-auto md:mx-0">
                        My journey began with a passion for visual arts. Today, I've transitioned into web development, combining my creative background with technical skills to create impactful digital solutions.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center md:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="#contact" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-full transition duration-300 border border-blue-600 hover:border-blue-700">GET IN TOUCH</a>
                        <div class="flex space-x-4 items-center text-gray-300">
                            <a href="https://www.instagram.com/jo_seph4564" target="_blank" aria-label="Instagram" class="hover:text-blue-400 transition duration-300 text-3xl"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.facebook.com/share/1AmmR79cJE/?mibextid=wwXIfr" target="_blank" aria-label="Facebook" class="hover:text-blue-400 transition duration-300 text-3xl"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" target="_blank" aria-label="LinkedIn" class="hover:text-blue-400 transition duration-300 text-3xl"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                    </div>
                </div>
                <div class="relative md:w-1/2 mt-12 md:mt-0 flex justify-center items-center">
                    <img src="image/pro.png" alt="Abankwah Joseph" class="rounded-lg shadow-xl max-w-full h-auto object-cover border-4 border-gray-700">
                    </div>
            </div>
        </section>

        <section class="py-12 bg-gray-800">
            <div class="container mx-auto px-6 md:px-12 flex flex-wrap justify-around items-center gap-8">
                <img src="image/hos.jpeg" alt="Client Logo 1" class="h-10 opacity-70 hover:opacity-100 transition duration-300 filter grayscale hover:grayscale-0">
                <img src="image/just.jpg" alt="Client Logo 2" class="h-10 opacity-70 hover:opacity-100 transition duration-300 filter grayscale hover:grayscale-0">
                <img src="image/tech.jpg" alt="Client Logo 3" class="h-10 opacity-70 hover:opacity-100 transition duration-300 filter grayscale hover:grayscale-0">
                <img src="image/logo .jpg" alt="Client Logo 4" class="h-10 opacity-70 hover:opacity-100 transition duration-300 filter grayscale hover:grayscale-0">
                <img src="image/vip.png" alt="Client Logo 5" class="h-10 opacity-70 hover:opacity-100 transition duration-300 filter grayscale hover:grayscale-0">
            </div>
        </section>

        <section id="about" class="py-20 bg-gray-900">
            <div class="container mx-auto px-6 md:px-12 flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2 relative flex justify-center">
                    <img src="image/me.png" alt="Abankwah Joseph - About" class="rounded-lg shadow-xl max-w-full h-auto border-4 border-gray-700">
                    </div>
                <div class="md:w-1/2 text-center md:text-left mt-8 md:mt-0">
                    <p class="text-blue-400 text-lg mb-2 uppercase tracking-wide">ABOUT ME</p>
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">
                        I AM AVAILABLE FOR <span class="text-blue-500">WEB DEVELOPMENT</span> & <span class="text-blue-500">DESIGN PROJECTS</span>
                    </h2>
                    <p class="text-gray-300 text-lg mb-6">
                        My journey began with a passion for visual arts at Kwahu Ridge Senior High and Technical. After gaining valuable real-world experience as a sales representative, I transitioned into web development, combining my creative background with technical skills to create impactful digital solutions. I enjoy touring locally and dream of global travel for creative inspiration.
                    </p>
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4 mb-8">
                        <div>
                            <p class="text-blue-400 text-4xl font-bold">280+</p>
                            <p class="text-gray-300">Positive Reviews</p>
                        </div>
                        <div>
                            <p class="text-blue-400 text-4xl font-bold">5+</p>
                            <p class="text-gray-300">Years Experience</p>
                        </div>
                        <div>
                            <p class="text-blue-400 text-4xl font-bold">10+</p>
                            <p class="text-gray-300">Projects Completed</p>
                        </div>
                        <div>
                            <p class="text-blue-400 text-4xl font-bold">3+</p>
                            <p class="text-gray-300">Awards Won</p>
                        </div>
                        </div>
                    <a href="#contact" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-full transition duration-300 border border-blue-600 hover:border-blue-700">GET IN TOUCH</a>
                </div>
            </div>
        </section>

        <section id="projects" class="py-20 bg-gray-800">
            <div class="container mx-auto px-6 md:px-12">
                <p class="text-blue-400 text-lg mb-2 text-center uppercase tracking-wide">MY WORK</p>
                <h2 class="text-4xl md:text-5xl font-bold text-center mb-12">RECENT PROJECTS</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-gray-900 rounded-xl shadow-xl overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out border border-gray-700">
                        <img src="images/project-1.jpg" alt="Project: Digital Graphic Apps" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-white mb-2">A Digital Graphic Apps</h3>
                            <p class="text-gray-400 text-sm mb-4">Under One, <span class="text-blue-400">Creative</span></p>
                            <div class="flex justify-between items-center border-t border-gray-700 pt-4">
                                <span class="text-gray-500 text-sm">Web Design, App Design</span>
                                <a href="#" aria-label="View Project" class="text-blue-400 hover:text-blue-500 text-3xl"><i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-900 rounded-xl shadow-xl overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out border border-gray-700">
                        <img src="images/project-2.jpg" alt="Project: Today's Multicurrency" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-white mb-2">Today's Multicurrency</h3>
                            <p class="text-gray-400 text-sm mb-4">Cyrles</p>
                            <div class="flex justify-between items-center border-t border-gray-700 pt-4">
                                <span class="text-gray-500 text-sm">Website Design</span>
                                <a href="#" aria-label="View Project" class="text-blue-400 hover:text-blue-500 text-3xl"><i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-900 rounded-xl shadow-xl overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out border border-gray-700">
                        <img src="images/project-3.jpg" alt="Project: Web Design & App Design" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-white mb-2">Modern Portfolio Site</h3>
                            <p class="text-gray-400 text-sm mb-4">Interactive & Responsive</p>
                            <div class="flex justify-between items-center border-t border-gray-700 pt-4">
                                <span class="text-gray-500 text-sm">Web Development</span>
                                <a href="#" aria-label="View Project" class="text-blue-400 hover:text-blue-500 text-3xl"><i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    </div>
            </div>
        </section>

        <section id="services" class="py-20 bg-gray-900">
            <div class="container mx-auto px-6 md:px-12">
                <p class="text-blue-400 text-lg mb-2 text-center uppercase tracking-wide">SERVICES</p>
                <h2 class="text-4xl md:text-5xl font-bold text-center mb-12">WHAT I PROVIDE</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <div class="bg-gray-800 p-8 rounded-xl shadow-xl flex flex-col items-center text-center transform hover:scale-105 transition duration-300 ease-in-out border border-gray-700">
                        <div class="text-blue-500 text-6xl mb-4"><i class="fas fa-code"></i></div>
                        <h3 class="text-2xl font-semibold text-white mb-4">Web Development</h3>
                        <p class="text-gray-300 mb-4">Developing modern web applications, focusing on user-friendly interfaces and robust backend solutions.</p>
                        <ul class="text-gray-400 text-left w-full space-y-2 mt-auto">
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Responsive & interactive apps</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Modern best practices</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Team collaboration</li>
                        </ul>
                    </div>
                    <div class="bg-gray-800 p-8 rounded-xl shadow-xl flex flex-col items-center text-center transform hover:scale-105 transition duration-300 ease-in-out border border-gray-700">
                        <div class="text-blue-500 text-6xl mb-4"><i class="fas fa-paint-brush"></i></div>
                        <h3 class="text-2xl font-semibold text-white mb-4">UI/UX Design</h3>
                        <p class="text-gray-300 mb-4">Creating intuitive and engaging user experiences with seamless interaction and visual appeal.</p>
                        <ul class="text-gray-400 text-left w-full space-y-2 mt-auto">
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>User research & personas</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Wireframing & prototyping</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Usability testing</li>
                        </ul>
                    </div>
                    <div class="bg-gray-800 p-8 rounded-xl shadow-xl flex flex-col items-center text-center transform hover:scale-105 transition duration-300 ease-in-out border border-gray-700">
                        <div class="text-blue-500 text-6xl mb-4"><i class="fas fa-palette"></i></div>
                        <h3 class="text-2xl font-semibold text-white mb-4">Graphic Design</h3>
                        <p class="text-gray-300 mb-4">Developing compelling visual content for various mediums, from branding to marketing materials.</p>
                        <ul class="text-gray-400 text-left w-full space-y-2 mt-auto">
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Logo & brand identity</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Marketing collateral</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Digital illustration</li>
                        </ul>
                    </div>
                    <div class="bg-gray-800 p-8 rounded-xl shadow-xl flex flex-col items-center text-center transform hover:scale-105 transition duration-300 ease-in-out border border-gray-700">
                        <div class="text-blue-500 text-6xl mb-4"><i class="fas fa-camera"></i></div>
                        <h3 class="text-2xl font-semibold text-white mb-4">Photography</h3>
                        <p class="text-gray-300 mb-4">Professional photography services for high-quality images and videos for diverse needs.</p>
                        <ul class="text-gray-400 text-left w-full space-y-2 mt-auto">
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Quality Picture taking</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Good Video shooting</li>
                            <li><i class="fas fa-check-circle text-blue-400 mr-2"></i>Portrait pictures</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="resume" class="py-20 bg-gray-800">
            <div class="container mx-auto px-6 md:px-12">
                <p class="text-blue-400 text-lg mb-2 text-center uppercase tracking-wide">MY JOURNEY</p>
                <h2 class="text-4xl md:text-5xl font-bold text-center mb-12">EDUCATION & EXPERIENCE</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <h3 class="text-3xl font-bold text-white mb-8 border-b border-gray-700 pb-4 flex items-center"><i class="fas fa-user-graduate text-blue-500 mr-4"></i>Education</h3>
                        <div class="space-y-8">
                            <div class="relative pl-8 border-l-2 border-blue-600 group">
                                <span class="absolute -left-3 top-0 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm transition-all duration-300 group-hover:scale-125">
                                    <i class="fas fa-university"></i>
                                </span>
                                <p class="text-blue-400 text-sm mb-1">2019 - Present</p>
                                <h4 class="text-xl font-semibold text-white mb-2">Graduate Student (Web Development)</h4>
                                <p class="text-gray-300 mb-2">IPMC Technology University</p>
                                <p class="text-gray-400 text-sm">Currently pursuing advanced studies in technology and development, acquiring strong web developer skills in parallel with work experience.</p>
                            </div>
                            <div class="relative pl-8 border-l-2 border-blue-600 group">
                                <span class="absolute -left-3 top-0 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm transition-all duration-300 group-hover:scale-125">
                                    <i class="fas fa-school"></i>
                                </span>
                                <p class="text-blue-400 text-sm mb-1">2015 - 2018</p>
                                <h4 class="text-xl font-semibold text-white mb-2">Visual Arts Student</h4>
                                <p class="text-gray-300 mb-2">Kwahu Ridge Senior High and Technical</p>
                                <p class="text-gray-400 text-sm">Completed secondary education with a strong focus on visual arts, learning sculpting (clay, cement, wood), color schemes, and design principles.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-3xl font-bold text-white mb-8 border-b border-gray-700 pb-4 flex items-center"><i class="fas fa-briefcase text-blue-500 mr-4"></i>Experience</h3>
                        <div class="space-y-8">
                            <div class="relative pl-8 border-l-2 border-blue-600 group">
                                <span class="absolute -left-3 top-0 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm transition-all duration-300 group-hover:scale-125">
                                    <i class="fas fa-laptop-code"></i>
                                </span>
                                <p class="text-blue-400 text-sm mb-1">2022 - Present</p>
                                <h4 class="text-xl font-semibold text-white mb-2">Web Developer (Freelance / Self-employed)</h4>
                                <p class="text-gray-300 mb-2">Accra, Ghana</p>
                                <ul class="text-gray-400 text-sm list-disc pl-5 space-y-1">
                                    <li>Building responsive and interactive web applications using modern technologies like React and Tailwind CSS.</li>
                                    <li>Implementing modern web development best practices for optimal performance and user experience.</li>
                                    <li>Collaborating with clients to understand requirements and deliver high-quality digital solutions.</li>
                                </ul>
                            </div>
                            <div class="relative pl-8 border-l-2 border-blue-600 group">
                                <span class="absolute -left-3 top-0 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm transition-all duration-300 group-hover:scale-125">
                                    <i class="fas fa-handshake"></i>
                                </span>
                                <p class="text-blue-400 text-sm mb-1">2019 - 2025</p>
                                <h4 class="text-xl font-semibold text-white mb-2">Sales Representative</h4>
                                <p class="text-gray-300 mb-2">Aduana Royals, Accra Central Market</p>
                                <ul class="text-gray-400 text-sm list-disc pl-5 space-y-1">
                                    <li>Enhanced marketing strategies and customer relationship skills through direct sales and client engagement.</li>
                                    <li>Built influence and trust among diverse customer demographics, both young and adult.</li>
                                    <li>Developed strong interpersonal and communication abilities in a fast-paced retail environment.</li>
                                </ul>
                            </div>
                            <div class="relative pl-8 border-l-2 border-blue-600 group">
                                <span class="absolute -left-3 top-0 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm transition-all duration-300 group-hover:scale-125">
                                    <i class="fas fa-camera-retro"></i>
                                </span>
                                <p class="text-blue-400 text-sm mb-1">2019 - Present</p>
                                <h4 class="text-xl font-semibold text-white mb-2">Photographer</h4>
                                <p class="text-gray-300 mb-2">Fotovillage (Freelance)</p>
                                <ul class="text-gray-400 text-sm list-disc pl-5 space-y-1">
                                    <li>Providing high-quality picture taking services for various events and individual needs.</li>
                                    <li>Skilled in good video shooting for capturing dynamic content.</li>
                                    <li>Specializing in compelling portrait photography, focusing on client satisfaction.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="skills" class="py-20 bg-gray-900">
            <div class="container mx-auto px-6 md:px-12">
                <p class="text-blue-400 text-lg mb-2 text-center uppercase tracking-wide">MY STRENGTHS</p>
                <h2 class="text-4xl md:text-5xl font-bold text-center mb-12">SKILLS & EXPERTISE</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-gray-800 p-6 rounded-xl shadow-xl border border-gray-700">
                        <h3 class="text-2xl font-semibold text-white mb-4 flex items-center"><i class="fas fa-laptop-code text-blue-500 text-3xl mr-3"></i>Technical Skills</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li><span class="text-blue-400 font-medium">Web Development:</span> HTML, CSS (Tailwind CSS), JavaScript, React</li>
                            <li><span class="text-blue-400 font-medium">Languages:</span> JavaScript, (Basic PHP for backend logic)</li>
                            <li><span class="text-blue-400 font-medium">Tools:</span> Git, VS Code</li>
                            </ul>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl shadow-xl border border-gray-700">
                        <h3 class="text-2xl font-semibold text-white mb-4 flex items-center"><i class="fas fa-lightbulb text-blue-500 text-3xl mr-3"></i>Creative Skills</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li><span class="text-blue-400 font-medium">Visual Arts:</span> Sculpting (Clay, Cement, Wood), Color Scheme, Design Principles</li>
                            <li><span class="text-blue-400 font-medium">Design:</span> UI/UX Design, Graphic Design</li>
                            <li><span class="text-blue-400 font-medium">Photography:</span> Portrait, Video Shooting, Quality Picture Taking</li>
                            <li><span class="text-blue-400 font-medium">Software:</span> Adobe Photoshop, Adobe Illustrator (or similar)</li>
                        </ul>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl shadow-xl border border-gray-700">
                        <h3 class="text-2xl font-semibold text-white mb-4 flex items-center"><i class="fas fa-users text-blue-500 text-3xl mr-3"></i>Soft Skills</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li><span class="text-blue-400 font-medium">Communication:</span> Effective Verbal & Written Communication</li>
                            <li><span class="text-blue-400 font-medium">Business Acumen:</span> Marketing Strategies, Customer Relations</li>
                            <li><span class="text-blue-400 font-medium">Problem-Solving:</span> Adaptability, Critical Thinking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="whatsapp-contact" class="py-20 bg-gray-900">
            <div class="container mx-auto px-6 md:px-12 text-center">
                <p class="text-blue-400 text-lg mb-2 uppercase tracking-wide">INSTANT CONNECT</p>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-8">Connect with me on <span class="text-green-500">WhatsApp</span></h2>
                <p class="text-gray-300 text-lg max-w-2xl mx-auto mb-10">
                    Scan the QR code below with your phone's camera to start a direct chat with me on WhatsApp. It's the quickest way to get in touch!
                </p>

                <div class="bg-gray-800 p-8 rounded-xl shadow-2xl inline-block border-2 border-green-500 transform hover:scale-105 transition-transform duration-300 relative overflow-hidden">
                    <img src="image/qrc.png" alt="WhatsApp QR Code for Mr Abankwah" class="w-64 h-64 mx-auto rounded-lg">
                    <p class="text-gray-300 mt-4 text-lg font-semibold">Scan to Chat with Mr Abankwah</p>
                    <div class="absolute inset-0 bg-green-500 opacity-10 blur-xl pointer-events-none rounded-xl animate-pulse-slow"></div>
                </div>

                <p class="text-gray-400 text-sm mt-8">Or simply call/message me at <a href="tel:+233552150758" class="text-blue-400 hover:underline">+233 552150758</a></p>
            </div>
        </section>

        <section id="contact" class="py-20 bg-gray-800">
    <div class="container mx-auto px-6 md:px-12">
        <p class="text-blue-400 text-lg mb-2 text-center uppercase tracking-wide">GET IN TOUCH</p>
        <h2 class="text-4xl md:text-5xl font-bold text-center mb-12">CONTACT ME</h2>

        <div class="flex flex-col lg:flex-row gap-12">
            <div class="lg:w-1/2 bg-gray-900 p-8 rounded-xl shadow-xl border border-gray-700">
                <h3 class="text-2xl font-semibold text-white mb-6">Send Me a Message</h3>

                <?php if (!empty($status_message)): ?>
                    <div class="status-message <?php echo ($status_type == 'success' ? 'status-success' : ($status_type == 'error' ? 'status-error' : 'status-warning')); ?> mb-6">
                        <?php echo $status_message; ?>
                    </div>
                <?php endif; ?>
                <form action="index.php" method="POST">
                    <div class="mb-4"> <label for="name" class="block text-gray-400 text-sm font-bold mb-2">Name</label>
                        <input type="text" id="name" name="name" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:border-blue-500 text-white placeholder-gray-500" placeholder="Your Name" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-400 text-sm font-bold mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:border-blue-500 text-white placeholder-gray-500" placeholder="your.email@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="block text-gray-400 text-sm font-bold mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:border-blue-500 text-white placeholder-gray-500" placeholder="Subject of your message" required>
                    </div>
                    <div class="mb-6"> <label for="message" class="block text-gray-400 text-sm font-bold mb-2">Message</label>
                        <textarea id="message" name="message" rows="6" class="w-full p-3 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:border-blue-500 text-white placeholder-gray-500" placeholder="Your message here..." required></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-full transition duration-300 border border-blue-600 hover:border-blue-700">SEND MESSAGE</button>
                </form>
            </div>

            <div class="lg:w-1/2 bg-gray-900 p-8 rounded-xl shadow-xl border border-gray-700">
                <h3 class="text-2xl font-semibold text-white mb-6">Contact Information</h3>
                <div class="space-y-6 text-gray-300">
                    <div class="flex items-start space-x-4">
                        <div class="text-blue-500 text-3xl"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <p class="font-semibold text-lg">Location:</p>
                            <p>Accra, Greater Accra Region, Ghana</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="text-blue-500 text-3xl"><i class="fas fa-envelope"></i></div>
                        <div>
                            <p class="font-semibold text-lg">Email:</p>
                            <p><a href="mailto:josephabankwah36@gmail.com" class="hover:underline text-blue-400">josephabankwah36@gmail.com</a></p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="text-blue-500 text-3xl"><i class="fas fa-phone"></i></div>
                        <div>
                            <p class="font-semibold text-lg">Phone:</p>
                            <p><a href="tel:+233552150758" class="hover:underline text-blue-400">+233 552150758</a></p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="text-blue-500 text-3xl"><i class="fas fa-link"></i></div>
                        <div>
                            <p class="font-semibold text-lg">Social Media:</p>
                            <div class="flex space-x-5 mt-2">
                                <a href="https://www.instagram.com/jo_seph4564" target="_blank" aria-label="Instagram Profile" class="text-gray-300 hover:text-blue-400 text-4xl transform hover:scale-110 transition duration-300"><i class="fab fa-instagram"></i></a>
                                <a href="https://www.facebook.com/share/1AmmR79cJE/?mibextid=wwXIfr" target="_blank" aria-label="Facebook Profile" class="text-gray-300 hover:text-blue-400 text-4xl transform hover:scale-110 transition duration-300"><i class="fab fa-facebook-f"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    </main>

    <footer class="bg-gray-900 text-gray-400 py-8 text-center border-t border-gray-700">
        <div class="container mx-auto px-6 md:px-12">
            <p>&copy; <span id="current-year"></span> Abankwah Joseph. All rights reserved.</p>
            <p class="mt-2">Built with <span class="text-red-500">&hearts;</span> by Joseph</p>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
                // Close mobile menu if open
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            });
        });

        // Toggle mobile menu
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Set current year in footer
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
</body>
</html>