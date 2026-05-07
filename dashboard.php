<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

requireLogin();

$userId = $_SESSION['user_id'];
$user = getUserById($userId);
$hasPassed = ($user['status'] == 1);
$examHistory = getUserExamHistory($userId);
$stats = getOverallStatistics();

// Get user rank
$rank = 1;
if ($hasPassed && $user['exam_score']) {
    $db = Database::getInstance();
    $rankResult = $db->getOne("SELECT COUNT(*) + 1 as rank FROM users WHERE status = 1 AND exam_score > ?", [$user['exam_score']]);
    $rank = $rankResult['rank'];
}

// Get motivational quote
$quotes = [
    ["The beautiful thing about learning is that no one can take it away from you.", "B.B. King"],
    ["Success is not final, failure is not fatal: it is the courage to continue that counts.", "Winston Churchill"],
    ["The only limit to our realization of tomorrow is our doubts of today.", "Franklin D. Roosevelt"],
    ["Believe you can and you're halfway there.", "Theodore Roosevelt"],
    ["The future belongs to those who believe in the beauty of their dreams.", "Eleanor Roosevelt"]
];
$randomQuote = $quotes[array_rand($quotes)];

logUserActivity($userId, 'Viewed dashboard');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NCSBN3 Nursing Examination</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-logo"><i class="fas fa-stethoscope"></i> NCSBN3</a>
            <div class="navbar-menu">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <span><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-danger" style="padding: 0.5rem 1rem;"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="dashboard-container">
        <!-- Welcome Card with Quote -->
        <div class="welcome-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1><i class="fas fa-waveform"></i> Welcome, <?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?>! 👋</h1>
                    <p><i class="fas fa-calendar"></i> <?php echo date('l, F j, Y'); ?></p>
                </div>
                <div style="text-align: right; max-width: 300px;">
                    <small style="color: #666;">
                        <i class="fas fa-quote-left"></i> "<?php echo $randomQuote[0]; ?>"
                        <br>- <?php echo $randomQuote[1]; ?>
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Status Card with Enhanced Glowing Indicator -->
        <div class="status-card">
            <h2><i class="fas fa-clipboard-list"></i> Examination Status</h2>
            <div class="glowing-circle <?php echo $hasPassed ? 'passed' : 'not-passed'; ?>" id="statusCircle" data-tooltip="<?php echo $hasPassed ? 'Congratulations on passing!' : 'Keep working hard!'; ?>">
                <?php echo $hasPassed ? '<i class="fas fa-check"></i>' : '<i class="fas fa-hourglass-half"></i>'; ?>
            </div>
            <div class="status-text <?php echo $hasPassed ? 'passed' : 'not-passed'; ?>" id="statusText">
                <?php echo $hasPassed ? 'PASSED' : 'NOT PASSED YET'; ?>
            </div>
            <div class="status-message" id="statusMessage">
                <?php 
                if ($hasPassed) {
                    echo '<i class="fas fa-trophy"></i> 🎉 Congratulations! You have successfully passed the NCSBN3 Nursing Examination! 🎉 <i class="fas fa-trophy"></i>';
                    if ($user['exam_score']) {
                        echo '<br><strong>📊 Your Score: ' . $user['exam_score'] . '%</strong>';
                        echo '<br><i class="fas fa-medal"></i> Rank: #' . $rank . ' among all students';
                    }
                } else {
                    echo '<i class="fas fa-book-open"></i> 📖 Keep studying! Your examination status is pending. Review the materials and try again.';
                }
                ?>
            </div>
            <?php if (!$hasPassed): ?>
                <div style="margin-top: 2rem;">
                    <button class="btn btn-primary" onclick="startPracticeExam()" style="width: auto; padding: 0.75rem 2rem;">
                        <i class="fas fa-play"></i> Start Practice Exam
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><i class="fas fa-chart-line"></i> Overall Pass Rate</h3>
                <div class="stat-number" data-target="<?php echo $stats['pass_rate']; ?>">0%</div>
                <small>of all students</small>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-users"></i> Total Students</h3>
                <div class="stat-number" data-target="<?php echo $stats['total_users']; ?>">0</div>
                <small>registered</small>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-check-circle"></i> Students Passed</h3>
                <div class="stat-number" data-target="<?php echo $stats['passed_users']; ?>">0</div>
                <small>certified</small>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-chart-simple"></i> Average Score</h3>
                <div class="stat-number" data-target="<?php echo $stats['average_score']; ?>">0%</div>
                <small>among passed students</small>
            </div>
        </div>
        
        <!-- Exam History -->
        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 2rem;">
            <h2 style="margin-bottom: 1rem;"><i class="fas fa-history"></i> Your Exam History</h2>
            <?php if (empty($examHistory)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <i class="fas fa-folder-open" style="font-size: 4rem; color: #ccc;"></i>
                    <p style="margin-top: 1rem; color: #666;">No exam attempts yet. Start practicing today!</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-file-alt"></i> Exam Title</th>
                                <th><i class="fas fa-chart-simple"></i> Score</th>
                                <th><i class="fas fa-percent"></i> Percentage</th>
                                <th><i class="fas fa-flag-checkered"></i> Result</th>
                                <th><i class="fas fa-calendar"></i> Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examHistory as $exam): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($exam['exam_title']); ?></td>
                                <td><strong><?php echo $exam['score'] . '/' . $exam['total_questions']; ?></strong></td>
                                <td><?php echo $exam['percentage']; ?>%</td>
                                <td><?php echo $exam['passed'] ? '<span class="badge badge-success"><i class="fas fa-check"></i> Passed</span>' : '<span class="badge badge-danger"><i class="fas fa-times"></i> Failed</span>'; ?></td>
                                <td><?php echo $exam['completed_date']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Study Tips -->
        <div style="background: linear-gradient(135deg, #667eea20, #764ba220); border-radius: 0.75rem; padding: 1.5rem; margin-top: 2rem;">
            <h3><i class="fas fa-lightbulb"></i> Study Tips</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div><i class="fas fa-clock"></i> Study 2-3 hours daily</div>
                <div><i class="fas fa-notes-medical"></i> Focus on weak areas</div>
                <div><i class="fas fa-bed"></i> Get adequate sleep before exam</div>
                <div><i class="fas fa-coffee"></i> Take regular breaks</div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script>
        // Counter animation for statistics
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;
        
        const animateCounters = () => {
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const current = parseInt(counter.innerText);
                const increment = target / speed;
                
                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(animateCounters, 20);
                } else {
                    counter.innerText = target;
                }
            });
        };
        
        // Start animation when element is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        const statsGrid = document.querySelector('.stats-grid');
        if (statsGrid) {
            observer.observe(statsGrid);
        }
        
        // Practice exam function
        function startPracticeExam() {
            alert('Practice exam feature coming soon! Stay tuned.');
        }
        
        // Add hover effect to status card
        const statusCard = document.querySelector('.status-card');
        if (statusCard) {
            statusCard.addEventListener('mouseenter', () => {
                const circle = document.querySelector('.glowing-circle');
                if (circle && circle.classList.contains('passed')) {
                    circle.style.transform = 'scale(1.05)';
                }
            });
            statusCard.addEventListener('mouseleave', () => {
                const circle = document.querySelector('.glowing-circle');
                if (circle) {
                    circle.style.transform = 'scale(1)';
                }
            });
        }
        
        <?php if ($hasPassed): ?>
        // Confetti effect on load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if (typeof window.NCSBN3 !== 'undefined' && window.NCSBN3.addCelebrationEffect) {
                    window.NCSBN3.addCelebrationEffect();
                }
            }, 500);
        });
        <?php endif; ?>
    </script>
</body>
</html>