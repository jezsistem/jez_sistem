<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $data['title'] ?? 'Rating' }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .rating-container {
            background: linear-gradient(135deg, #1e3a5f 0%, #0a192f 100%);
            min-height: 100vh;
        }
        
        /* Star rating styles */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            cursor: pointer;
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.3);
            transition: all 0.2s ease;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #fbbf24;
            text-shadow: 0 0 20px rgba(251, 191, 36, 0.5);
        }
        
        .star-rating label:active {
            transform: scale(1.1);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
        .animate-delay-4 { animation-delay: 0.4s; opacity: 0; }
    </style>
</head>
@if (request()->segment(1) == 'rating_app')
<body class="rating-container" id="device_background_panel">
@else
<body class="rating-container hidden" id="device_background_panel">
@endif
    <div class="min-h-screen flex items-center justify-center p-4">
        <form id="f_rating" class="w-full max-w-md">
            @csrf
            <input type="hidden" value="" id="rating_mode"/>
            <input type="hidden" value="" id="ur_id"/>
            <input type="hidden" value="" id="cust_id"/>
            <input type="hidden" value="" id="rating_value"/>
            
            <div class="text-center space-y-6">
                <!-- Store Name -->
                <div class="animate-fade-in animate-delay-1">
                    <h1 class="text-3xl font-bold text-white mb-2">{{ $data['store_name'] }}</h1>
                    <div class="w-24 h-1 bg-yellow-400 mx-auto rounded-full"></div>
                </div>
                
                <!-- Welcome Message -->
                <div class="animate-fade-in animate-delay-2 space-y-2">
                    <p class="text-gray-300 text-lg">Silahkan beri penilaian Anda tentang produk dan pelayanan kami.</p>
                    <h2 class="text-2xl font-semibold text-white">Terimakasih telah berbelanja</h2>
                </div>
                
                <!-- Privacy Notice -->
                <div class="animate-fade-in animate-delay-2">
                    <p class="text-gray-400 text-sm">
                        Kasir tidak mengetahui rating yang Anda berikan.<br>
                        Silahkan beri rating <span class="text-yellow-400 font-semibold">paling jujur</span>, ya!
                    </p>
                </div>
                
                <!-- Star Rating -->
                <div class="animate-fade-in animate-delay-3 py-6">
                    <div class="star-rating">
                        <input type="radio" name="rating" id="star5" value="5">
                        <label for="star5" class="fa fa-star"></label>
                        <input type="radio" name="rating" id="star4" value="4">
                        <label for="star4" class="fa fa-star"></label>
                        <input type="radio" name="rating" id="star3" value="3">
                        <label for="star3" class="fa fa-star"></label>
                        <input type="radio" name="rating" id="star2" value="2">
                        <label for="star2" class="fa fa-star"></label>
                        <input type="radio" name="rating" id="star1" value="1">
                        <label for="star1" class="fa fa-star"></label>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-white mt-4">
                        " <span id="rating_label">Pilih Bintang</span> "
                    </h3>
                </div>
                
                <!-- Feedback Textarea -->
                <div class="animate-fade-in animate-delay-4">
                    <label class="block text-left text-white font-medium mb-2">Kritik & Saran</label>
                    <textarea 
                        id="ur_description" 
                        name="ur_description" 
                        rows="3"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent resize-none backdrop-blur-sm"
                        placeholder="Tulis alasan atau saran Anda..."
                    ></textarea>
                </div>
                
                <!-- Submit Button -->
                <div class="animate-fade-in animate-delay-4 pt-4">
                    <button 
                        type="submit" 
                        id="kt_login_signin_submit"
                        class="w-full py-4 px-6 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold text-lg rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-yellow-400/30"
                    >
                        <i class="fa fa-paper-plane mr-2"></i> Kirim Penilaian
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Thank You Modal -->
    @include('app.updated_rating_by_customer.rating_by_customer_modal_v2')
    
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @include('app.updated_rating_by_customer.rating_by_customer_js_v2')
</body>
</html>
