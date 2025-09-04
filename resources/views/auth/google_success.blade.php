<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Login Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Google Login Successful!
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(auth()->check())
                            <div class="text-center mb-4">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" 
                                         alt="Profile" 
                                         class="rounded-circle mb-3" 
                                         style="width: 100px; height: 100px;">
                                @else
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 100px; height: 100px;">
                                        <i class="fas fa-user text-white" style="font-size: 40px;"></i>
                                    </div>
                                @endif
                                
                                <h5 class="mb-2">{{ auth()->user()->getDisplayName() }}</h5>
                                <p class="text-muted mb-3">{{ auth()->user()->u_email }}</p>
                                
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <i class="fab fa-google text-danger mb-2" style="font-size: 24px;"></i>
                                            <div class="small text-muted">Provider</div>
                                            <div class="fw-bold">{{ auth()->user()->provider ?: 'local' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <i class="fas fa-link text-primary mb-2" style="font-size: 24px;"></i>
                                            <div class="small text-muted">Google Linked</div>
                                            <div class="fw-bold">{{ auth()->user()->google_linked ? 'Yes' : 'No' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <i class="fas fa-calendar text-success mb-2" style="font-size: 24px;"></i>
                                            <div class="small text-muted">Linked Date</div>
                                            <div class="fw-bold">
                                                {{ auth()->user()->google_linked_at ? auth()->user()->google_linked_at->format('d M Y') : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <a href="/dashboard" class="btn btn-primary me-2">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Go to Dashboard
                                </a>
                                <a href="/logout" class="btn btn-outline-secondary">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Logout
                                </a>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No user data found. Please try logging in again.
                                </div>
                                <a href="/" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Login
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
