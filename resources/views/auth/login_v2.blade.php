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
    <link href="{{ asset('app/assets/fonts/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app/assets/fonts/style-solid.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .h-login {
            height: calc(100vh - 4rem);
        }
        .max-h-100vh {
            max-height: 100vh;
        }
        .bg-login {
            background: #f8f8f8;
        }
    </style>
</head>
<body class="bg-white">
    <div class="min-h-screen flex p-8 gap-6">
        <!-- Left Section - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center rounded-xl overflow-hidden">
            <img src="{{ asset('app/assets/media/misc/login_jezpro.png') }}" alt="Login Illustration" class="w-full h-login object-cover rounded-xl">
        </div>
        
        <!-- Right Section - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-login rounded-xl">
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
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 6L8.91302 9.91697C11.4616 11.361 12.5384 11.361 15.087 9.91697L22 6" stroke="#6b7280" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M2.01577 13.4756C2.08114 16.5412 2.11383 18.0739 3.24496 19.2094C4.37608 20.3448 5.95033 20.3843 9.09883 20.4634C11.0393 20.5122 12.9607 20.5122 14.9012 20.4634C18.0497 20.3843 19.6239 20.3448 20.7551 19.2094C21.8862 18.0739 21.9189 16.5412 21.9842 13.4756C22.0053 12.4899 22.0053 11.5101 21.9842 10.5244C21.9189 7.45886 21.8862 5.92609 20.7551 4.79066C19.6239 3.65523 18.0497 3.61568 14.9012 3.53657C12.9607 3.48781 11.0393 3.48781 9.09882 3.53656C5.95033 3.61566 4.37608 3.65521 3.24495 4.79065C2.11382 5.92608 2.08114 7.45885 2.01576 10.5244C1.99474 11.5101 1.99475 12.4899 2.01577 13.4756Z" stroke="#6b7280" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <input type="email" 
                                   id="email" 
                                   name="u_email" 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full px-4 py-3 pl-10" 
                                   placeholder="your.email@example.com" 
                                   required>
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.491 15.5H14.5M9.5 15.5H9.50897" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4.26781 18.8447C4.49269 20.515 5.87613 21.8235 7.55966 21.9009C8.97627 21.966 10.4153 22 12 22C13.5847 22 15.0237 21.966 16.4403 21.9009C18.1239 21.8235 19.5073 20.515 19.7322 18.8447C19.879 17.7547 20 16.6376 20 15.5C20 14.3624 19.879 13.2453 19.7322 12.1553C19.5073 10.485 18.1239 9.17649 16.4403 9.09909C15.0237 9.03397 13.5847 9 12 9C10.4153 9 8.97627 9.03397 7.55966 9.09909C5.87613 9.17649 4.49269 10.485 4.26781 12.1553C4.12105 13.2453 4 14.3624 4 15.5C4 16.6376 4.12105 17.7547 4.26781 18.8447Z" stroke="#6b7280" stroke-width="1.5"/>
                                    <path d="M7.5 9V6.5C7.5 4.01472 9.51472 2 12 2C14.4853 2 16.5 4.01472 16.5 6.5V9" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>  
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
                    <div class="flex items-center justify-between pb-4 md:pb-8">
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
                            class="w-full text-white bg-gray-900 hover:bg-gray-900 focus:ring-4 focus:ring-gray-300 font-semibold rounded-lg text-base px-5 py-3.5 text-center transition-colors">
                        Login
                    </button> 
                    
                    <!-- Separator -->
                    <!-- <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or login with</span>
                        </div>
                    </div> -->
                    
                    <!-- Google Login Button -->
                    <!-- <button type="button" 
                            id="google-login-btn"
                            class="w-full flex items-center justify-center gap-3 text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-3 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Google</span>
                    </button> -->
                    
                    <!-- Create Account Link -->
                    <!-- <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            You're new in here? 
                            <a href="#" class="text-red-600 hover:text-red-700 font-medium">Create Account</a>
                        </p>
                    </div> -->
                </form>
                
                <!-- Copyright -->
                <div class="text-left ml-0 absolute bottom-12 md:bottom-16">
                    <p class="text-sm text-gray-400">© 2025 Zona Karya Nusantara</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // ==================== CUSTOM TOAST (POS V2 STYLE) ====================
            
            function showToast(message, type = 'info') {
                const toastId = 'toast-' + Date.now();
                const colors = {
                    success: { bg: 'bg-green-50', border: 'border-green-500', text: 'text-green-700', icon: 'cft-check' },
                    error: { bg: 'bg-red-50', border: 'border-red-500', text: 'text-red-700', icon: 'cft-cancel' },
                    warning: { bg: 'bg-orange-100', border: 'border-orange-500', text: 'text-orange-700', icon: 'cft-warning' },
                    info: { bg: 'bg-blue-50', border: 'border-blue-500', text: 'text-blue-700', icon: 'cft-info' }
                };
                const color = colors[type] || colors.info;
                
                const toastHtml = `
                    <div id="${toastId}" class="flex items-center w-full max-w-xs p-4 mb-4 ${color.bg} rounded-lg shadow border-l-4 ${color.border}" role="alert">
                        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${color.text} rounded-lg">
                            <i class="cft-standard-solid ${color.icon} text-xl"></i>
                        </div>
                        <div class="ml-3 text-sm font-medium ${color.text}">${message}</div>
                        <button type="button" class="ml-auto -mx-1.5 -my-1.5 ${color.bg} ${color.text} hover:${color.text} rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8" onclick="document.getElementById('${toastId}').remove()">
                            <span class="sr-only">Close</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                `;
                
                let container = document.getElementById('toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toast-container';
                    container.className = 'fixed top-5 right-5 z-50 space-y-4';
                    document.body.appendChild(container);
                }
                
                container.insertAdjacentHTML('beforeend', toastHtml);
                
                setTimeout(() => {
                    const toast = document.getElementById(toastId);
                    if (toast) {
                        toast.style.transition = 'opacity 0.3s';
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 4000);
            }
            
            // ==================== TOGGLE PASSWORD VISIBILITY ====================
            
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
            
            // ==================== FORM SUBMISSION ====================
            
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                
                // Disable button and show loading state
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...');
                
                $.ajax({
                    url: '/user_login',
                    method: 'POST',
                    data: $(this).serialize(), // This will include CSRF token from the form
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Login response:', response);
                        
                        // Handle both JSON object and JSON string
                        if (typeof response === 'string') {
                            try {
                                response = JSON.parse(response);
                            } catch (e) {
                                console.error('Failed to parse response:', e);
                            }
                        }
                        
                        if (response.status === '200') {
                            showToast('Login berhasil! Mengalihkan...', 'success');
                            setTimeout(function() {
                                window.location.href = '/redirect';
                            }, 1000);
                        } else if (response.status === '500') {
                            submitBtn.prop('disabled', false).html(originalText);
                            showToast('Akun Anda tidak aktif. Hubungi administrator.', 'error');
                        } else if (response.status === '400') {
                            submitBtn.prop('disabled', false).html(originalText);
                            showToast('Email atau password salah!', 'error');
                        } else {
                            submitBtn.prop('disabled', false).html(originalText);
                            showToast(response.message || 'Login gagal. Silakan coba lagi.', 'error');
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        
                        let errorMessage = 'Terjadi kesalahan saat login!';
                        
                        if (xhr.status === 419) {
                            errorMessage = 'Session expired. Silakan refresh halaman.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join(', ');
                        }
                        
                        showToast(errorMessage, 'error');
                        console.error('Login error:', xhr);
                    }
                });
            });
            
            // ==================== GOOGLE LOGIN ====================
            
            $('#google-login-btn').on('click', function() {
                window.location.href = '/auth/google';
            });
        });
    </script>
</body>
</html>

