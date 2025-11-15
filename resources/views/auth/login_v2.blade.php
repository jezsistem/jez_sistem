<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>JEZ PRO | Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"> 
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .h-100vh {
            height: 100vh;
        }
        .max-h-100vh {
            max-height: 100vh;
        }
    </style>
</head>
<body class="bg-white">
    <div class="min-h-screen flex p-8 gap-5">
        <!-- Left Section - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center rounded-xl overflow-hidden h-100vh">
            <img src="{{ asset('app/assets/media/misc/login_jezpro2.jpg') }}" alt="Login Illustration" class="w-full max-h-100vh object-cover rounded-xl">
        </div>
        
        <!-- Right Section - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 rounded-xl">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <img src="{{ asset('logo') }}/jez_pro.png" class="h-9 w-auto mx-auto" alt="Logo" />
                </div>          
                
                <!-- Title -->
                <h2 class="text-2xl font-semibold text-gray-900 text-center mb-8">
                    Login first to your account
                </h2>
                
                <!-- Login Form -->
                <form id="login-form" class="space-y-6" novalidate>
                    @csrf
                    
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email Address *</label>
                        <div class="relative">
                            <input type="email" 
                                   id="email" 
                                   name="u_email" 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full px-4 py-3 pl-10" 
                                   placeholder="pristia@gmail.com" 
                                   required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password *</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full px-4 py-3 pl-10 pr-10" 
                                   placeholder="••••••••" 
                                   required>
                            <button type="button" 
                                    id="toggle-password" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.88l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2">
                            <label for="remember-me" class="ml-2 text-sm font-medium text-gray-900">Remember Me</label>
                        </div>
                        <a href="#" class="text-sm text-red-600 hover:text-red-700 font-medium">Forgot Password</a>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" 
                            class="w-full text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-3 text-center transition-colors">
                        Login
                    </button>
                    
                    <!-- Separator -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or login with</span>
                        </div>
                    </div>
                    
                    <!-- Google Login Button -->
                    <button type="button" 
                            id="google-login-btn"
                            class="w-full flex items-center justify-center gap-3 text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-3 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Google</span>
                    </button>
                    
                    <!-- Create Account Link -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            You're new in here? 
                            <a href="#" class="text-red-600 hover:text-red-700 font-medium">Create Account</a>
                        </p>
                    </div>
                </form>
                
                <!-- Copyright -->
                <div class="text-center mt-8">
                    <p class="text-sm text-gray-400">© 2025 Zona Karya Nusantara</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Toastr for notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#toggle-password').on('click', function() {
                const passwordInput = $('#password');
                const eyeIcon = $('#eye-icon');
                const eyeSlashIcon = $('#eye-slash-icon');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    eyeIcon.addClass('hidden');
                    eyeSlashIcon.removeClass('hidden');
                } else {
                    passwordInput.attr('type', 'password');
                    eyeIcon.removeClass('hidden');
                    eyeSlashIcon.addClass('hidden');
                }
            });
            
            // Form submission
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    u_email: $('#email').val(),
                    password: $('#password').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                $.ajax({
                    url: '/user_login',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === '200') {
                            toastr.success('Login berhasil!', 'Success');
                            setTimeout(function() {
                                window.location.href = '/redirect';
                            }, 1000);
                        } else {
                            toastr.error('Email atau password salah!', 'Error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message, 'Error');
                        } else {
                            toastr.error('Terjadi kesalahan saat login!', 'Error');
                        }
                    }
                });
            });
            
            // Google Login
            $('#google-login-btn').on('click', function() {
                window.location.href = '/auth/google';
            });
        });
    </script>
</body>
</html>

