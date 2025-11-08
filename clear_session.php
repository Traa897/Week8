<?php
/**
 * CLEAR SESSION & COOKIES
 * Save di: week8/clear_session.php
 * Akses: http://localhost/pl/prak%20pl/week8/clear_session.php
 */

// Destroy session
session_start();
session_unset();
session_destroy();

// Delete all cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clear Session</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-body text-center p-5">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                <h2 class="mt-4 mb-3">Session Cleared!</h2>
                <p class="text-muted mb-4">All sessions and cookies have been destroyed.</p>
                
                <div class="alert alert-info text-start">
                    <strong>Next Steps:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Close this browser tab</li>
                        <li>Clear browser cache (Ctrl + Shift + Delete)</li>
                        <li>Close & reopen browser</li>
                        <li>Try login again</li>
                    </ol>
                </div>
                
                <a href="login.php" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-sign-in-alt"></i> Go to Login
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>