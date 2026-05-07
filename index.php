<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Get statistics for the landing page
$stats = getOverallStatistics();
$recentPassed = [];
$db = Database::getInstance();
$recentPassed = $db->getAll("SELECT username, full_name, exam_score, exam_date FROM users WHERE status = 1 ORDER BY exam_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>NCSBN3 - Nursing Examination Portal | NCLEX-RN Preparation</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles for landing page */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .feature-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .counter-box {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .counter-box:hover {
            transform: scale(1.05);
            background: white;
        }
        
        .counter-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .testimonial-avatar i {
            font-size: 2rem;
            color: white;
        }
        
        .btn-glow {
            animation: btnPulse 2s infinite;
        }
        
        @keyframes btnPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(102, 126, 234, 0);
            }
        }
        
        .stat-item {
            text-align: center;
            padding: 1.5rem;
            border-right: 2px solid rgba(255,255,255,0.2);
        }
        
        .stat-item:last-child {
            border-right: none;
        }
        
        @media (max-width: 768px) {
            .stat-item {
                border-right: none;
                border-bottom: 2px solid rgba(255,255,255,0.2);
            }
            .stat-item:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); position: fixed; width: 100%; top: 0; z-index: 1000;">
        <div class="navbar-container">
            <a href="index.php" class="navbar-logo" style="font-size: 1.5rem; font-weight: bold;">
                <i class="fas fa-stethoscope"></i> NCSBN3
            </a>
            <div class="navbar-menu">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">Dashboard</a>
                    <a href="logout.php" class="btn btn-danger" style="padding: 0.5rem 1.5rem;">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">Login</a>
                    <a href="register.php" class="btn" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 0.5rem 1.5rem;">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero" style="padding-top: 80px;">
        <div class="container" style="padding: 100px 20px;">
            <div class="hero-content" style="text-align: center; color: white;">
                <div class="floating">
                    <i class="fas fa-graduation-cap" style="font-size: 4rem; margin-bottom: 2rem; display: inline-block;"></i>
                </div>
                <h1 style="font-size: 3.5rem; margin-bottom: 1rem; font-weight: 800;">NCSBN3 Nursing Examination</h1>
                <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.95;">Your Gateway to Nursing Excellence & NCLEX-RN Success</p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn" style="background: white; color: #667eea; padding: 1rem 2rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px;">
                            <i class="fas fa-user-plus"></i> Get Started Now
                        </a>
                        <a href="login.php" class="btn btn-glow" style="background: transparent; border: 2px solid white; color: white; padding: 1rem 2rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px;">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn" style="background: white; color: #667eea; padding: 1rem 2rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px;">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Statistics Bar -->
            <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 20px; margin-top: 4rem; padding: 2rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="stat-item">
                        <div style="font-size: 2.5rem; font-weight: bold; color: white;"><?php echo $stats['total_users']; ?>+</div>
                        <div style="color: rgba(255,255,255,0.9);">Active Students</div>
                    </div>
                    <div class="stat-item">
                        <div style="font-size: 2.5rem; font-weight: bold; color: white;"><?php echo $stats['pass_rate']; ?>%</div>
                        <div style="color: rgba(255,255,255,0.9);">Pass Rate</div>
                    </div>
                    <div class="stat-item">
                        <div style="font-size: 2.5rem; font-weight: bold; color: white;"><?php echo $stats['passed_users']; ?></div>
                        <div style="color: rgba(255,255,255,0.9);">Certified Nurses</div>
                    </div>
                    <div class="stat-item">
                        <div style="font-size: 2.5rem; font-weight: bold; color: white;">4+</div>
                        <div style="color: rgba(255,255,255,0.9);">Practice Exams</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" style="padding: 80px 0; background: #f8f9fa;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Why Choose NCSBN3?</h2>
                <p style="color: #666; font-size: 1.1rem;">Comprehensive nursing examination preparation platform</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">Practice Exams</h3>
                    <p style="color: #666;">Access hundreds of NCLEX-RN style questions with detailed explanations and rationales.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">Track Progress</h3>
                    <p style="color: #666;">Monitor your performance with detailed analytics and personalized study recommendations.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">Certification</h3>
                    <p style="color: #666;">Earn your nursing certification and get recognized for your achievement.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">Flexible Learning</h3>
                    <p style="color: #666;">Study at your own pace with 24/7 access to all examination materials.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">Community Support</h3>
                    <p style="color: #666;">Join a community of nursing professionals and share your journey.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">Secure Platform</h3>
                    <p style="color: #666;">Your data is safe with enterprise-grade security and privacy protection.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Achievements -->
    <section style="padding: 80px 0; background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Recent Success Stories</h2>
                <p style="opacity: 0.9;">Join hundreds of successful nurses who passed with NCSBN3</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
                <?php foreach ($recentPassed as $student): ?>
                <div class="testimonial-card" style="background: white; color: #333;">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user-nurse"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0;"><?php echo htmlspecialchars($student['full_name'] ?: $student['username']); ?></h4>
                            <small style="color: #10b981;">
                                <i class="fas fa-check-circle"></i> Passed with <?php echo $student['exam_score']; ?>%
                            </small>
                        </div>
                    </div>
                    <p style="color: #666;">"Thanks to NCSBN3, I successfully passed my nursing examination and achieved my dream of becoming a certified nurse!"</p>
                    <small style="color: #999;">Passed on <?php echo formatDate($student['exam_date']); ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section style="padding: 80px 0; background: white;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">How It Works</h2>
                <p style="color: #666;">Three simple steps to nursing certification</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="font-size: 2rem; font-weight: bold; color: white;">1</span>
                    </div>
                    <h3>Register</h3>
                    <p style="color: #666;">Create your free account to get started</p>
                </div>
                
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="font-size: 2rem; font-weight: bold; color: white;">2</span>
                    </div>
                    <h3>Study & Practice</h3>
                    <p style="color: #666;">Take practice exams and track your progress</p>
                </div>
                
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="font-size: 2rem; font-weight: bold; color: white;">3</span>
                    </div>
                    <h3>Get Certified</h3>
                    <p style="color: #666;">Pass the exam and receive your certification</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section style="padding: 80px 0; background: linear-gradient(135deg, #667eea, #764ba2); text-align: center; color: white;">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Ready to Start Your Journey?</h2>
            <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">Join thousands of nursing professionals who achieved their dreams with NCSBN3</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn" style="background: white; color: #667eea; padding: 1rem 2.5rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px;">
                    <i class="fas fa-rocket"></i> Get Started Today
                </a>
            <?php else: ?>
                <a href="dashboard.php" class="btn" style="background: white; color: #667eea; padding: 1rem 2.5rem; font-size: 1.1rem; font-weight: 600; border-radius: 50px;">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: #1a1a2e; color: white; padding: 3rem 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div>
                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-stethoscope"></i> NCSBN3</h3>
                    <p style="color: #aaa;">Your trusted partner in nursing examination preparation and certification.</p>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;"><a href="#home" style="color: #aaa; text-decoration: none;">Home</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="#features" style="color: #aaa; text-decoration: none;">Features</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="#about" style="color: #aaa; text-decoration: none;">About</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Contact</h4>
                    <p style="color: #aaa;"><i class="fas fa-envelope"></i> support@ncsbn3.com</p>
                    <p style="color: #aaa;"><i class="fas fa-phone"></i> 1-800-NCSBN3</p>
                </div>
                <div>
                    <h4>Follow Us</h4>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" style="color: #aaa; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: #aaa; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: #aaa; font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                        <a href="#" style="color: #aaa; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr style="margin: 2rem 0; border-color: #333;">
            <div style="text-align: center; color: #aaa;">
                <p>&copy; 2024 NCSBN3 Nursing Examination. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        
        // Counter animation for statistics
        const counters = document.querySelectorAll('.counter-number');
        const speed = 200;
        
        counters.forEach(counter => {
            const updateCount = () => {
                const target = parseInt(counter.getAttribute('data-target'));
                const count = parseInt(counter.innerText);
                const increment = target / speed;
                
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'white';
                navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            } else {
                navbar.style.background = 'rgba(255,255,255,0.95)';
                navbar.style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>