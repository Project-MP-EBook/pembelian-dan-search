<?php 
    session_start();
    include("koneksi.php");

    if(isset($_SESSION['user_id'])){
        $id = $_SESSION['user_id'];
        $query = mysqli_query($konek,"select * from users where role='user' and user_id='$id'")or die (mysqli_error($konek));
        while($data=mysqli_fetch_array($query)){
            $_SESSION['email'] = $data['email'];
            $_SESSION['name'] = $data['name'];
            $_SESSION['fullname'] = $data['fullname'];
            $_SESSION['major'] = $data['major'];
            $_SESSION['university'] = $data['university'];
            $_SESSION['profile_picture'] = $data['profile_picture'];
        }
    }else{
        header("Location: user/login.php");
    }

    if(isset($_POST['book_id'])){
        // Tangkap data dari form
        $book_id = $_POST['book_id'];
        $_SESSION['book_id'] = $_POST['book_id'];
        $user_id = $_POST['user_id'];
        $title = $_POST['title'];
        $_SESSION['book_title'] = $_POST['book_title'];
        $price = str_replace(['.', ','], ['', '.'], $_POST['price']); // Format harga ke angka float
        $status = $_POST['status'];

        // Query sederhana dengan placeholder
        $query = "INSERT INTO payments (book_id, user_id, amount, status, created_at) VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $konek->prepare($query);
            $stmt->execute([$book_id, $user_id, $price, $status]); // Eksekusi query dengan data
            header("Location: confirm.php");
        } catch (PDOException $e) {
            echo "Gagal menyimpan pesanan: " . $e->getMessage();
        }
    }

    $book_id = $_SESSION['book_id'];
    $book_title = $_SESSION['book_title'];
    $query = mysqli_query($konek,"select * from payments where book_id='$book_id' and user_id='$id'")or die (mysqli_error($konek));
        while($data=mysqli_fetch_array($query)){
            $status = $data['status'];
        }

    // Tambahkan di bagian atas setelah session start dan include koneksi
    $file_path = "";
    if($status === "success") {
        $book_id = $_SESSION['book_id'];
        $query_file = mysqli_query($konek, "SELECT file FROM books WHERE book_id='$book_id'") or die(mysqli_error($konek));
        $file_data = mysqli_fetch_assoc($query_file);
        $file_path = $file_data['file'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>Profile Pengguna</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            background-color: #F9F9F9;
        }
        .navbar{
            background-color: #1E5B86;
        }
        .nav-link{
            color:white;
        }
        .profile-nav{
            border-radius: 20%;
            width: 50px;
            height: 38px;
        }
        p{
            color: #ADA7A7;
        }
        .img-top img{
            width: 100%;
            margin-bottom: 10px;
        }
        main{
            width: 90%;
        }
        .btn{
            background-color: #1E5B86;
        }
        .profile-img{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .name-email{
            margin-left: 20px;
        }
        .d-flex .profile-pict{
            width: 80px;
            height: 80px;
            border-radius: 100%;
        }
        .name-email{
            margin-top: 14px;
        }
        .form-section {
            display: flex;
            gap: 20px;
        }
        .form-section .left, .form-section .right {
            flex: 1;
        }
        .form-label {
            color: #555;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-primary px-3">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="search.php">Buy</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="uploads/uploaded.php">Sell</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="monetisasi.php">History</a>
            </li>
        </ul>
        <form class="d-flex" role="search" action="search.php" method="get">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
            <?php if($_SESSION['profile_picture'] != "") { ?>
                <div class="dropdown" style="width : 38px;">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0;">
                        <img class="profile-nav" src="uploads/<?=$_SESSION['profile_picture']?>" alt="Profile Picture" style="width: 38px;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php } else { ?>
                <div class="dropdown" style="width : 38px;">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0;">
                        <img class="profile-nav" src="default.png" alt="Profile Picture" style="width: 38px;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php } ?>
            <a href="notifikasi.php" style="margin-left: 8px;"><img src="notif.png" alt="Notifikasi"></a>
            <a href="cart.php" style="margin-left: 8px; padding:1px; background-color:white; border-radius:8px;"><img src="cart (2).png" alt="Cart" style="height:36px"></a>
        </form>
        </div>
    </div>
    </nav>
    
    <?php if($status === "pending") { ?>
        <main class="m-auto text-center py-5">
            <div class="container">
                <h2 class="text-secondary fw-bold">TRANSAKSI PENDING</h2>
                <h4 class="text-secondary fw-bold">Menunggu Konfirmasi Pembayaran Oleh Admin</h4>
                <p class="text-muted">
                    <?= $_SESSION['book_title'] ?>
                </p>
                <div class="my-4">
                    <img src="pending.png" alt="Success Icon" class="mb-4" style="width: 200px; height: 120px;">
                </div>
                <div>
                    <a href="index.php"><button class="btn btn-secondary" style="background-color: #4F98CA;">KEMBALI</button></a>
                </div>
            </div>
        </main>
    <?php } else { ?>
        <main class="m-auto text-center py-5">
            <div class="container">
                <h2 class="text-success fw-bold">TRANSAKSI BERHASIL</h2>
                <p class="text-muted">
                    <?= $_SESSION['book_title'] ?>
                </p>
                <div class="my-4">
                    <img src="ceklis.png" alt="Success Icon" class="mb-4" style="width: 120px; height: 120px;">
                </div>
                <div>
                <a href="uploads/<?= htmlspecialchars($file_path); ?>" download>
                    <button class="btn btn-primary me-2 text-black" style="background-color: white;">UNDUH FILE</button>
                </a>
                    <button class="btn btn-secondary" style="background-color: #4F98CA;">KEMBALI</button>
                </div>
            </div>
        </main>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>