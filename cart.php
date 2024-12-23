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
    
    if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
        echo "Keranjang kosong.";
        exit;
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
            color:white;
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
        img.img-fluid {
        max-width: 100%;
        height: auto;
    }

    h1 {
        font-size: 1.8rem;
        font-weight: 600;
    }

    h3 {
        font-size: 1.6rem;
        font-weight: 700;
    }

    p {
        font-size: 1rem;
        line-height: 1.6;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    button.btn {
        padding: 10px 20px;
        border-radius: 5px;
    }

    button.btn-outline-primary {
        border: 1px solid #1E5B86;
        color: #1E5B86;
        transition: 0.3s ease-in-out;
    }

    button.btn-outline-primary:hover {
        background-color: #1E5B86;
        color: white;
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
    <main class="m-auto py-5">
        <div class="container bg-light p-4 rounded shadow">
            <h1 class="text" style="margin-left: 40px;">Keranjang Saya</h1>
            <div class="row mt-4" style="padding-left: 40px; padding-right: 40px;">
            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <?php 
                    $total_price = 0;
                    foreach ($_SESSION['cart'] as $index => $item):
                        $allTittle = ''; 
                        $allTittle .= $item['title'] . ', ';
                        // Hapus koma terakhir
                        $allTittle = rtrim($allTittle, ', ');
                        $item_total = $item['price'] * $item['quantity'];
                        $total_price += $item_total; ?>
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm mb-3" style="background-color: #4F98CA; color:white;">
                                <div class="card-body d-flex" style="justify-content: space-between;">
                                    <div>
                                        <p class="card-title"><?= htmlspecialchars($item['title']); ?>  ⭐ <?= number_format($item['rating'], 1); ?>/5</p>
                                        <p class="mb-1 text-muted"></p>
                                        <h4 class="text">Rp <?= number_format($item['price'], 2, ',', '.'); ?></h4>
                                        <p class="text"><?= nl2br(htmlspecialchars($item['description'])); ?></p>
                                    </div>
                                    <div>
                                        <form action="hapus_buku.php" method="post" style="display:inline;">
                                            <input type="hidden" name="index" value="<?= $index; ?>">
                                            <button type="submit" class="btn btn btn-sm" style="background-color:#4F98CA;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                            </svg></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <img src="qris.jpg" alt="qris pembayaran" style="height: 50vh; width:25vw;">
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h3>Total Harga: Rp <?= number_format($total_price, 2, ',', '.'); ?></h3>
                        <form action="confirm.php" method="post">
                            <button type="submit" class="btn btn-primary btn-lg">Checkout</button>
                        <?php foreach ($_SESSION['cart'] as $index => $item):  ?>
                            <input type="hidden" name="book_id" value="<?= $item['book_id']; ?>">
                            <input type="hidden" name="user_id" value="<?=$_SESSION['user_id'];?>">
                            <input type="hidden" name="title" value="<?= $allTittle; ?>">
                            <input type="hidden" name="price" value="<?= $total_price; ?>">
                            <input type="hidden" name="status" value="pending">
                        <?php endforeach; ?>
                        </form>
                    </div>
    </div>
<?php else: ?>
    <div class="container mt-5">
        <p class="text-center">Keranjang kosong. <a href="search.php">Belanja sekarang!</a></p>
    </div>
<?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>