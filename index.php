<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AspirasiKu v3.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --admin-color: #ef4444;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass: rgba(255, 255, 255, 0.95);
        }

        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { 
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow: hidden;
        }

        /* Dekorasi Background Bulat Animasi */
        .circle {
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
            animation: move 10s infinite alternate;
        }
        @keyframes move {
            from { transform: translate(-10%, -10%); }
            to { transform: translate(10%, 10%); }
        }

        .login-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255,255,255,0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .login-card:hover { transform: scale(1.02); }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo h2 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(to right, #4f46e5, #9333ea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .brand-logo p { color: #64748b; font-size: 0.9rem; margin-top: 5px; }

        .form-group { margin-bottom: 20px; position: relative; }

        label { 
            display: block; 
            font-size: 0.8rem; 
            font-weight: 700; 
            color: #475569; 
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input, select {
            width: 100%;
            padding: 14px 16px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            outline: none;
            color: #1e293b;
        }

        input:focus, select:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Toggle Password Style */
        .pw-wrapper { position: relative; }
        .btn-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.7rem;
            cursor: pointer;
            padding: 5px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        button[type="submit"]:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Role Switcher Color */
        .admin-active { border-color: var(--admin-color) !important; }
        .admin-active:focus { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important; }
    </style>
</head>
<body>

    <div class="circle" style="top: -5%; left: -5%;"></div>
    <div class="circle" style="bottom: -10%; right: -5%; background: rgba(0,0,0,0.05);"></div>

    <div class="login-card">
        <div class="brand-logo">
            <h2>AspirasiKu</h2>
            <p>Sistem Informasi Sarana Sekolah</p>
        </div>

        <form action="proses_login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label>Masuk Sebagai</label>
                <select name="role" id="roleSelector" required>
                    <option value="siswa">Siswa / Pelapor</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nomor Identitas / NIS</label>
                <input type="text" name="username_login" placeholder="Contoh: 1001" required>
            </div>

            <div class="form-group">
                <label>Kata Sandi</label>
                <div class="pw-wrapper">
                    <input type="password" name="password_login" id="pwInput" placeholder="••••••••" required>
                    <button type="button" class="btn-toggle" id="btnToggle">LIHAT</button>
                </div>
            </div>

            <button type="submit" id="submitBtn">
                <span>Masuk Sekarang</span>
            </button>
        </form>

        <div class="footer">
            &copy; 2026 &bull; Junior Assistant Programmer <br>
            ujikom Paket 3 - eldiaz mahesa
        </div>
    </div>

    <script>
        const roleSelector = document.getElementById('roleSelector');
        const submitBtn = document.getElementById('submitBtn');
        const pwInput = document.getElementById('pwInput');
        const btnToggle = document.getElementById('btnToggle');
        const loginForm = document.getElementById('loginForm');

        // 1. Role Theme Switcher (Efek Visual saat pilih Admin)
        roleSelector.addEventListener('change', function() {
            if(this.value === 'admin') {
                document.documentElement.style.setProperty('--primary', '#ef4444');
                document.documentElement.style.setProperty('--primary-hover', '#dc2626');
            } else {
                document.documentElement.style.setProperty('--primary', '#3b82f6');
                document.documentElement.style.setProperty('--primary-hover', '#2563eb');
            }
        });

        // 2. Toggle Password
        btnToggle.addEventListener('click', function() {
            const isPassword = pwInput.type === 'password';
            pwInput.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'SEMBUNYI' : 'LIHAT';
        });

        // 3. Form Loading Animation
        loginForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px;" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memproses...</span>
            `;
        });

        // CSS Keyframe for Spinner
        const style = document.createElement('style');
        style.innerHTML = `@keyframes spin { to { transform: rotate(360deg); } }`;
        document.head.appendChild(style);
    </script>
</body>
</html>