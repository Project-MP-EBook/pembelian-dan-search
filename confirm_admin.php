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

    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $payment_id = $_POST['payment_id'];
        $new_status = "success"; // Mengubah status ke 'success'
        $update_query = "UPDATE payments SET status='$new_status' WHERE payment_id='$payment_id'";
        if (mysqli_query($konek, $update_query)) {
            echo "<script>alert('Status berhasil diubah!'); </script>";
        } else {
            echo "<script>alert('Gagal mengubah status: " . mysqli_error($konek) . "');</script>";
        }
    }

    $query_all_payments = mysqli_query($konek, "SELECT * FROM payments") or die(mysqli_error($konek));
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
            <a class="nav-link" aria-current="page" href="contentfiltering.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="confirm_admin.php">Payment</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="monetisasi_admin.php">Monetization</a>
            </li>
        </ul>
        <form class="d-flex" role="search" action="search.php" method="get">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
            <?php if($_SESSION['profile_picture'] != "") { ?>
                <div class="dropdown" style="width : 38px;">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0;">
                        <img class="profile-nav" src="../uploads/<?=$_SESSION['profile_picture']?>" alt="Profile Picture" style="width: 38px;">
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

    <main class="m-auto text-center py-5">
        <div class="container">
            <h2 class="text-secondary fw-bold">DAFTAR PEMBAYARAN</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Judul Buku</th>
                            <th>ID Pengguna</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($payment = mysqli_fetch_assoc($query_all_payments)) {
                            $book_id = $payment['book_id'];
                            $query_book = mysqli_query($konek, "SELECT * FROM books WHERE book_id = '$book_id'") or die(mysqli_error($konek));
                            while ($books = mysqli_fetch_assoc($query_book)) { ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($books['title']); ?></td>
                                    <td><?= htmlspecialchars($payment['user_id']); ?></td>
                                    <td><?= $payment['amount']; ?></td>
                                    <td><?= ucfirst(htmlspecialchars($payment['status'])); ?></td>
                                    <td><?= htmlspecialchars($payment['created_at']); ?></td>
                                    <td>
                                        <?php if ($payment['status'] === 'pending') { ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="payment_id" value="<?= $payment['payment_id']; ?>">
                                                <button type="submit" name="update_status" class="btn btn-success btn-sm">Mark as Success</button>
                                            </form>
                                        <?php } else { ?>
                                            <span class="text-success">Completed</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>