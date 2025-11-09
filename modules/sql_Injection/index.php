<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SQL Injection Demo</title>
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
      padding: 36px 20px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
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

    .wrap {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 1200px;
    }

    header.top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
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

    .brand {
      display: flex;
      gap: 14px;
      align-items: center;
    }

    .logo {
      width: 60px;
      height: 60px;
      border-radius: 15px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      display: flex;
      align-items: center;
      justify-content: center;
      color: #001;
      font-weight: 800;
      font-size: 20px;
      box-shadow: 0 8px 24px rgba(0, 212, 255, 0.3);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 24px rgba(0, 212, 255, 0.3);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.5);
      }
    }

    .brand h1 {
      font-size: 24px;
      letter-spacing: 0.2px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 4px;
    }
    
    .brand p { 
      color: rgba(224, 230, 237, 0.7); 
      font-size: 14px; 
      margin: 0; 
    }

    .actions { 
      display: flex; 
      gap: 10px; 
      align-items: center; 
    }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      border-radius: 10px;
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      text-decoration: none;
      border: 1px solid rgba(0, 212, 255, 0.2);
      font-weight: 700;
      transition: all 0.3s ease;
    }
    
    .btn-back:hover { 
      transform: translateY(-3px); 
      box-shadow: 0 10px 20px rgba(0, 212, 255, 0.3);
      border-color: var(--primary);
      color: var(--primary);
    }

    /* panel */
    .panel {
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
      border: 1px solid rgba(0, 212, 255, 0.2);
      animation: fadeIn 0.5s ease;
    }

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

    .grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 25px;
    }

    .card {
      background: rgba(26, 31, 58, 0.6);
      border-radius: 15px;
      padding: 25px;
      border: 1px solid rgba(0, 212, 255, 0.1);
      transition: all 0.3s ease;
      box-shadow: 0 8px 22px rgba(0, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 200px;
      text-decoration: none;
      color: var(--text);
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, transparent, rgba(0, 212, 255, 0.05));
      transform: translateX(-100%);
      transition: transform 0.5s ease;
    }

    .card:hover::before {
      transform: translateX(0);
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .card.safe {
      border-color: rgba(0, 255, 136, 0.2);
    }

    .card.safe:hover {
      border-color: var(--success);
      box-shadow: 0 20px 40px rgba(0, 255, 136, 0.2);
    }

    .card.vuln {
      border-color: rgba(255, 71, 87, 0.2);
    }

    .card.vuln:hover {
      border-color: var(--danger);
      box-shadow: 0 20px 40px rgba(255, 71, 87, 0.2);
    }

    .card .head {
      display: flex;
      align-items: flex-start;
      gap: 15px;
    }
    
    .chip {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 20px;
    }

    .chip.safe {
      background: linear-gradient(135deg, var(--success), #00cc6a);
      color: #001;
    }

    .chip.vuln {
      background: linear-gradient(135deg, var(--danger), #cc0050);
      color: #fff;
    }

    .title { 
      font-size: 20px; 
      margin-bottom: 8px; 
      font-weight: 700; 
    }
    
    .title.safe {
      color: var(--success);
    }
    
    .title.vuln {
      color: var(--danger);
    }
    
    .desc  { 
      color: rgba(224, 230, 237, 0.7); 
      font-size: 15px; 
      margin-bottom: 15px; 
      line-height: 1.5;
    }

    .badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 12px;
    }
    
    .badge.safe { 
      background: rgba(0, 255, 136, 0.2); 
      color: var(--success); 
      border: 1px solid var(--success);
    }
    
    .badge.vuln { 
      background: rgba(255, 71, 87, 0.2); 
      color: var(--danger); 
      border: 1px solid var(--danger);
    }

    .card .foot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
    }
    
    .card .link {
      text-decoration: none;
      background: rgba(26, 31, 58, 0.6);
      padding: 8px 15px;
      border-radius: 8px;
      color: var(--text);
      font-weight: 700;
      border: 1px solid rgba(0, 212, 255, 0.1);
      transition: all 0.3s ease;
    }
    
    .card .link:hover {
      background: rgba(0, 212, 255, 0.1);
      border-color: var(--primary);
      color: var(--primary);
    }

    .about {
      margin-top: 30px;
      background: rgba(26, 31, 58, 0.6);
      padding: 25px;
      border-radius: 15px;
      border: 1px solid rgba(0, 212, 255, 0.1);
      color: var(--text);
      font-size: 15px;
      line-height: 1.6;
    }
    
    .about h2 { 
      color: var(--primary); 
      margin-bottom: 12px; 
      font-size: 18px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .about ul {
      margin-top: 15px;
      margin-left: 20px;
    }
    
    .about li {
      margin-bottom: 8px;
      color: rgba(224, 230, 237, 0.8);
    }

    @media (max-width: 900px){
      .grid { 
        grid-template-columns: 1fr; 
      }
      
      body { 
        padding: 20px; 
      }
      
      header.top {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <header class="top">
      <div class="brand">
        <div class="logo">SQL</div>
        <div>
          <h1>SQL Injection Demo</h1>
          <p>Praktik keamanan dan demonstrasi kerentanan</p>
        </div>
      </div>

      <div class="actions">
        <!-- Tombol kembali ke dashboard; jika file ini berada di modules/sql_injec/
             gunakan ../../dashboard.php . Jika dashboard.php ada langsung di webroot
             dan project folder bernama DVWA_LITE, kamu bisa ganti ke /DVWA_LITE/dashboard.php -->
        <a class="btn-back" href="../../dashboard.php" aria-label="Kembali ke Dashboard">← Kembali ke Dashboard</a>
      </div>
    </header>

    <main class="panel" role="main">
      <div class="grid">
        <a class="card safe" href="login_safe.php">
          <div>
            <div class="head">
              <div class="chip safe">S</div>
              <div>
                <div class="title safe">Safe Login</div>
                <div class="desc">Login page with SQL injection protection (prepared statements)</div>
              </div>
            </div>
          </div>
          <div class="foot">
            <span class="badge safe">SAFE</span>
            <span class="link">Open →</span>
          </div>
        </a>

        <a class="card vuln" href="login_vul.php">
          <div>
            <div class="head">
              <div class="chip vuln">V</div>
              <div>
                <div class="title vuln">Vulnerable Login</div>
                <div class="desc">Login page vulnerable to SQL injection (for learning)</div>
              </div>
            </div>
          </div>
          <div class="foot">
            <span class="badge vuln">VULNERABLE</span>
            <span class="link">Open →</span>
          </div>
        </a>

        <a class="card safe" href="create_user_safe.php">
          <div>
            <div class="head">
              <div class="chip safe">C</div>
              <div>
                <div class="title safe">Safe User Creation</div>
                <div class="desc">Create user with input validation and prepared statements</div>
              </div>
            </div>
          </div>
          <div class="foot">
            <span class="badge safe">SAFE</span>
            <span class="link">Open →</span>
          </div>
        </a>

        <a class="card vuln" href="create_user_vul.php">
          <div>
            <div class="head">
              <div class="chip vuln">V</div>
              <div>
                <div class="title vuln">Vulnerable User Creation</div>
                <div class="desc">Create user vulnerable to SQL injection (educational)</div>
              </div>
            </div>
          </div>
          <div class="foot">
            <span class="badge vuln">VULNERABLE</span>
            <span class="link">Open →</span>
          </div>
        </a>
      </div>

      <section class="about" aria-labelledby="aboutHeading">
        <h2 id="aboutHeading">About This Demo</h2>
        <p>This application demonstrates the importance of protecting against SQL injection attacks.</p>
        <ul>
          <li><strong>Safe versions</strong> use prepared statements and proper input validation.</li>
          <li><strong>Vulnerable versions</strong> show common mistakes so you can learn mitigations.</li>
        </ul>
        <p style="margin-top:15px;"><strong>Note:</strong> The vulnerable versions are intentionally insecure for educational use — do not deploy them publicly.</p>
      </section>
    </main>
  </div>
</body>
</html>