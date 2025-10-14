<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Buat Janji Temu | OM's Barbershop</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      background: #111;
    }
    header nav {
      width: 100%;
      display: flex;
      justify-content: center !important;
      align-items: center;
      gap: 1.2rem;
      text-align: center;
    }
    .simple-header {
      background: rgba(255,255,255,0.08);
      box-shadow: none;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      color: #fff;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    .simple-header .logo, .simple-header nav a {
      color: #fff !important;
    }
    .simple-header nav a:hover {
      background: rgba(255,255,255,0.12);
    }
    .booking-section {
      background: rgba(255,255,255,0.7);
      color: #222;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.18);
      max-width: 500px;
      margin: 48px auto;
      padding: 48px 36px;
      display: flex;
      flex-direction: column;
      align-items: center;
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }

    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }
    header('Location: member.php');
    exit;
</body>
</html>