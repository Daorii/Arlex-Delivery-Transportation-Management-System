<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADTMS - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
        }

        .container {
            background-color: #fff;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            
            /* 5 inches = 480px (at 96 DPI) */
            width: 480px;
            height: 480px;
            
            padding: 10px 0;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .sign-up {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .container.active .sign-in {
            transform: translateX(100%);
        }

        .container.active .sign-up {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: move 0.6s;
        }

        @keyframes move {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .toggle-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: all 0.6s ease-in-out;
            border-radius: 125px 0 0 125px;
            z-index: 1000;
        }

        .container.active .toggle-container {
            transform: translateX(-100%);
            border-radius: 0 150px 100px 0;
        }

        .toggle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            background-size: 200% 200%;
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }

        .container.active .toggle {
            transform: translateX(50%);
        }

        .toggle-panel {
            position: absolute;
            width: 50%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 30px;
            text-align: center;
            top: 0;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }

        .toggle-left {
            transform: translateX(-200%);
        }

        .container.active .toggle-left {
            transform: translateX(0);
        }

        .toggle-right {
            right: 0;
            transform: translateX(0);
        }

        .container.active .toggle-right {
            transform: translateX(200%);
        }

        .form-container form {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            height: 100%;
        }

        .form-container input {
            background-color: #eee;
            border: none;
            margin: 8px 0;
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 8px;
            width: 80%;
            max-width: 280px;
            outline: none;
        }

        .form-container button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 12px;
            padding: 10px 45px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .form-container button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">
    
    <div class="container" id="container">
        
        <!-- Driver Sign In Form (Left Side Initially) -->
        <div class="form-container sign-up">
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="h-40 w-auto mb-4">
                <h1 class="text-2xl font-bold mb-4">Driver Sign In</h1>
                
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username"
                    value="{{ old('username') }}"
                    required
                >
                
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password"
                    required
                >
                
                <input type="hidden" name="user_type" value="driver">
                
                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <button type="submit">Sign In</button>
            </form>
        </div>

        <!-- Admin Sign In Form (Right Side Initially) -->
        <div class="form-container sign-in">
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="h-32 w-auto mb-4">
                <h1 class="text-2xl font-bold mb-4">Admin Sign In</h1>
                
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username"
                    value="{{ old('username') }}"
                    required
                >
                
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password"
                    required
                >
                
                <input type="hidden" name="user_type" value="admin">
                
                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <button type="submit">Sign In</button>
            </form>
        </div>

        <!-- Toggle Panel -->
        <div class="toggle-container">
            <div class="toggle">
                <!-- Driver Panel (Shows when Admin is active) -->
                <div class="toggle-panel toggle-left">
                    <h1 class="text-4xl font-bold mb-4">Welcome Drivers! 🚗</h1>
                    <p class="text-sm mb-6">Enter your credentials to access the driver portal</p>
                    <button 
                        class="bg-transparent border-2 border-white rounded-lg px-8 py-2 font-semibold hover:bg-white hover:text-purple-600 transition"
                        type="button"
                        id="driver"
                    >
                        Driver Portal
                    </button>
                </div>
                
                <!-- Admin Panel (Shows when Driver is active) -->
                <div class="toggle-panel toggle-right">
                    <h1 class="text-4xl font-bold mb-4">Welcome Back! 👋</h1>
                    <p class="text-sm mb-6">Enter your credentials to access the admin dashboard</p>
                    <button 
                        class="bg-transparent border-2 border-white rounded-lg px-8 py-2 font-semibold hover:bg-white hover:text-purple-600 transition"
                        type="button"
                        id="admin"
                    >
                        Admin Portal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const adminBtn = document.getElementById('admin');
        const driverBtn = document.getElementById('driver');

        adminBtn.addEventListener('click', () => {
            container.classList.add('active');
        });

        driverBtn.addEventListener('click', () => {
            container.classList.remove('active');
        });
    </script>
</body>
</html>