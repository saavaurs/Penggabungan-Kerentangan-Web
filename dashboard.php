<?php
require 'auth.php'; // harus tersedia di project
$pdo = pdo_connect();

$user = current_user();
if (!$user) {
    // jika belum login, redirect ke login
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Lab - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="container">
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
                <h1>Cyber Lab</h1>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="modules/sql_injection/index.php" class="nav-link">
                        <i class="fas fa-database"></i>
                        <span>SQL Injection</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="modules/xss/dashboard.php" class="nav-link">
                        <i class="fas fa-code"></i>
                        <span>XSS Lab</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="modules/broken_access_control/" class="nav-link">
                        <i class="fas fa-lock-open"></i>
                        <span>Broken Access Control</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="modules/kerentanan_upload/dashboard.php" class="nav-link">
                        <i class="fas fa-file-upload"></i>
                        <span>Kerentanan Upload</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Laboratorium Keamanan Siber</h1>
                <p>Tingkat Kesulitan & Alasan Setiap Modul</p>
            </div>

            <main class="stats-container">
                <!-- Stat Card 1: SQL Challenges -->
                <article class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon sql">S</div>
                        <div class="stat-title">SQL Challenges</div>
                    </div>
                    <div class="stat-value">12</div>
                    <div class="stat-label">Tantangan Tersedia</div>

                    <div class="difficulty-section">
                        <div class="difficulty-label">Tingkat Kesulitan</div>
                        <div class="difficulty-indicator medium">
                            <div class="difficulty-stars">
                                <span></span><span></span><span></span>
                            </div>
                            <span class="difficulty-text">Sedang</span>
                        </div>
                        <div class="reasoning">
                            <details>
                                <summary>Alasan</summary>
                                <p>Membutuhkan pemahaman sintaks kueri database dan cara memanipulasinya. Kesulitannya
                                    bervariasi dari sederhana hingga kompleks, menjadikannya tantangan menengah yang
                                    ideal.</p>
                            </details>
                        </div>
                    </div>
                </article>

                <!-- Stat Card 2: XSS Scenarios -->
                <article class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon xss">X</div>
                        <div class="stat-title">XSS Scenarios</div>
                    </div>
                    <div class="stat-value">8</div>
                    <div class="stat-label">Skenario Tersedia</div>

                    <div class="difficulty-section">
                        <div class="difficulty-label">Tingkat Kesulitan</div>
                        <div class="difficulty-indicator medium">
                            <div class="difficulty-stars">
                                <span></span><span></span><span></span>
                            </div>
                            <span class="difficulty-text">Sedang</span>
                        </div>
                        <div class="reasoning">
                            <details>
                                <summary>Alasan</summary>
                                <p>Membutuhkan pemahaman sintaks kueri database dan cara memanipulasinya. Kesulitannya
                                    bervariasi dari sederhana hingga kompleks, menjadikannya tantangan menengah yang
                                    ideal.</p>
                            </details>
                        </div>
                    </div>
                </article>

                <!-- Stat Card 3: Access Control Tests -->
                <article class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon access">A</div>
                        <div class="stat-title">Access Control Tests</div>
                    </div>
                    <div class="stat-value">6</div>
                    <div class="stat-label">Uji Coba Tersedia</div>

                    <div class="difficulty-section">
                        <div class="difficulty-label">Tingkat Kesulitan</div>
                        <div class="difficulty-indicator hard">
                            <div class="difficulty-stars">
                                <span></span><span></span><span></span>
                            </div>
                            <span class="difficulty-text">Sulit</span>
                        </div>
                        <div class="reasoning">
                            <details>
                                <summary>Alasan</summary>
                                <p>Sering kali melibatkan celah logika bisnis yang kompleks dan eskalasi hak akses.
                                    Membutuhkan analisis mendalam terhadap alur kerja aplikasi dan peran pengguna.</p>
                            </details>
                        </div>
                    </div>
                </article>

                <!-- Stat Card 4: Upload Vulnerabilities -->
                <article class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon upload">U</div>
                        <div class="stat-title">Upload Vulnerabilities</div>
                    </div>
                    <div class="stat-value">10</div>
                    <div class="stat-label">Vulnerabilitas Tersedia</div>

                    <div class="difficulty-section">
                        <div class="difficulty-label">Tingkat Kesulitan</div>
                        <div class="difficulty-indicator easy">
                            <div class="difficulty-stars">
                                <span></span><span></span><span></span>
                            </div>
                            <span class="difficulty-text">Mudah</span>
                        </div>
                        <div class="reasoning">
                            <details>
                                <summary>Alasan</summary>
                                <p>Konsep dasarnya sederhana: memanipulasi server untuk menerima file berbahaya.
                                    Dampaknya langsung dan mudah didemonstrasikan, menjadikannya titik awal yang sangat
                                    baik.</p>
                            </details>
                        </div>
                    </div>
                </article>
            </main>

            <section class="modules-section">
                <h2 class="section-title">
                    <i class="fas fa-flask"></i>
                    Modul Praktikum
                </h2>

                <div class="modules-grid">
                    <a href="modules/sql_injec/" class="module-card">
                        <div class="module-header">
                            <div class="module-icon">
                                <i class="fas fa-database"></i>
                            </div>
                        </div>
                        <div class="module-title">SQL Injection Lab</div>
                        <div class="module-desc">
                            Pelajari berbagai teknik SQL Injection dari basic hingga advanced. Praktikkan bypass WAF dan
                            exploitation.
                        </div>
                        <div class="module-footer">
                            <div class="difficulty medium">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <button class="start-btn">Start Lab</button>
                        </div>
                    </a>

                    <a href="modules/xss_main/" class="module-card">
                        <div class="module-header">
                            <div class="module-icon">
                                <i class="fas fa-code"></i>
                            </div>
                        </div>
                        <div class="module-title">Cross-Site Scripting (XSS)</div>
                        <div class="module-desc">
                            Jelajahi Stored, Reflected, dan DOM-based XSS. Pelajari cara mendeteksi dan mencegah
                            serangan XSS.
                        </div>
                        <div class="module-footer">
                            <div class="difficulty easy">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <button class="start-btn">Start Lab</button>
                        </div>
                    </a>

                    <a href="modules/broken_acces/" class="module-card">
                        <div class="module-header">
                            <div class="module-icon">
                                <i class="fas fa-lock-open"></i>
                            </div>
                        </div>
                        <div class="module-title">Broken Access Control</div>
                        <div class="module-desc">
                            Uji kelemahan dalam kontrol akses seperti IDOR, privilege escalation, dan authorization
                            bypass.
                        </div>
                        <div class="module-footer">
                            <div class="difficulty hard">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <button class="start-btn">Start Lab</button>
                        </div>
                    </a>

                    <a href="modules/kerentanan_upload/" class="module-card">
                        <div class="module-header">
                            <div class="module-icon">
                                <i class="fas fa-file-upload"></i>
                            </div>
                        </div>
                        <div class="module-title">File Upload Vulnerabilities</div>
                        <div class="module-desc">
                            Praktikkan bypass file upload restrictions, RCE melalui upload, dan implementasi secure
                            upload.
                        </div>
                        <div class="module-footer">
                            <div class="difficulty medium">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <button class="start-btn">Start Lab</button>
                        </div>
                    </a>
                </div>
            </section>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !menuToggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.module-card, .stat-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
    </script>
</body>

</html>