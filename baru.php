<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tingkat Kesulitan Lab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #00d4ff;
            --secondary: #ff00ff;
            --dark: #0a0e27;
            --darker: #060818;
            --light: #1a1f3a;
            --text: #e0e6ed;
            --danger: #ff4757;
            --success: #00ff88;
            --warning: #ffa502;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Animated Background */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(
                circle at 20% 50%,
                rgba(0, 212, 255, 0.1) 0%,
                transparent 50%
            ),
            radial-gradient(
                circle at 80% 80%,
                rgba(255, 0, 255, 0.1) 0%,
                transparent 50%
            ),
            radial-gradient(
                circle at 40% 20%,
                rgba(0, 255, 136, 0.05) 0%,
                transparent 50%
            );
            pointer-events: none;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
        }

        header {
            text-align: center;
            margin-bottom: 40px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .subtitle {
            color: rgba(224, 230, 237, 0.7);
            font-size: 1.1rem;
        }

        /* Stats Container */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        /* Stat Card */
        .stat-card {
            background: rgba(26, 31, 58, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(0, 212, 255, 0.1);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 250px; /* Tambah tinggi untuk menampung difficulty */
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease backwards;
        }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            color: #001;
        }

        .stat-icon.sql { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.xss { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-icon.access { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-icon.upload { background: linear-gradient(135deg, #43e97b, #38f9d7); }

        .stat-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--text);
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(224, 230, 237, 0.6);
            font-size: 0.9rem;
        }

        /* Difficulty Section */
        .difficulty-section {
            margin-top: 20px;
            border-top: 1px solid rgba(0, 212, 255, 0.1);
            padding-top: 15px;
        }

        .difficulty-label {
            font-size: 0.8rem;
            color: rgba(224, 230, 237, 0.7);
            margin-bottom: 8px;
        }

        .difficulty-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .difficulty-stars {
            display: flex;
            gap: 5px;
        }

        .difficulty-stars span {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(224, 230, 237, 0.2);
            transition: background 0.3s ease;
        }

        /* Difficulty Colors */
        .difficulty-indicator.easy .difficulty-stars span:nth-child(-n+1) { background: var(--success); }
        .difficulty-indicator.medium .difficulty-stars span:nth-child(-n+2) { background: var(--warning); }
        .difficulty-indicator.hard .difficulty-stars span:nth-child(-n+3) { background: var(--danger); }
        
        .difficulty-text {
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .difficulty-indicator.easy .difficulty-text { color: var(--success); }
        .difficulty-indicator.medium .difficulty-text { color: var(--warning); }
        .difficulty-indicator.hard .difficulty-text { color: var(--danger); }
        
        /* Reasoning (Collapsible) */
        .reasoning {
            margin-top: 15px;
        }

        .reasoning details {
            cursor: pointer;
        }

        .reasoning summary {
            font-size: 0.85rem;
            color: var(--primary);
            font-weight: 600;
            list-style: none; /* Hides default triangle */
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid var(--primary);
            transition: all 0.3s ease;
        }
        .reasoning summary:hover {
            background: rgba(0, 212, 255, 0.1);
        }

        .reasoning details[open] summary {
            background: var(--primary);
            color: var(--darker);
        }

        .reasoning p {
            margin-top: 10px;
            padding: 10px;
            background: rgba(26, 31, 58, 0.4);
            border-radius: 8px;
            font-size: 0.85rem;
            color: rgba(224, 230, 237, 0.8);
            line-height: 1.5;
            border-left: 3px solid var(--primary);
        }

    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Lab Keamanan Siber</h1>
            <p class="subtitle">Tingkat Kesulitan & Alasan Setiap Modul</p>
        </header>

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
                            <p>Membutuhkan pemahaman sintaks kueri database dan cara memanipulasinya. Kesulitannya bervariasi dari sederhana hingga kompleks, menjadikannya tantangan menengah yang ideal.</p>
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
                            <p>Melibatkan pemahaman eksekusi kode di browser. Meskipun dasarnya mudah, menguasai semua jenis (Stored, Reflected, DOM-based) dan teknik bypass filter adalah tantangan yang kompleks.</p>
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
                            <p>Sering kali melibatkan celah logika bisnis yang kompleks dan eskalasi hak akses. Membutuhkan analisis mendalam terhadap alur kerja aplikasi dan peran pengguna.</p>
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
                            <p>Konsep dasarnya sederhana: memanipulasi server untuk menerima file berbahaya. Dampaknya langsung dan mudah didemonstrasikan, menjadikannya titik awal yang sangat baik.</p>
                        </details>
                    </div>
                </div>
            </article>
        </main>
    </div>

</body>
</html>