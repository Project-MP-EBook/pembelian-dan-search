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

    if(isset($_GET['search'])){
        $search = $_GET['search'];
        $query_books = mysqli_query($konek, "SELECT book_id, title, description, cover FROM books where status='Diterima' & title like '%$search%'") or die(mysqli_error($konek));
        $books = [];
        while ($row = mysqli_fetch_assoc($query_books)) {
            $books[] = $row;
        }
    }else{
        $query_books = mysqli_query($konek, "SELECT book_id, title, description, cover FROM books where status='Diterima'") or die(mysqli_error($konek));
        $books = [];
        while ($row = mysqli_fetch_assoc($query_books)) {
            $books[] = $row;
        }
    }

    if(isset($_GET['category'])){
        $category = $_GET['category'];
        $query_books = mysqli_query($konek, "SELECT book_id, title, description, cover FROM books where category ='$category' & status='Diterima'") or die(mysqli_error($konek));
        $books = [];
        while ($row = mysqli_fetch_assoc($query_books)) {
            $books[] = $row;
        }
    }

    // Mengambil rating dan review
    $query_reviews = mysqli_query($konek, "SELECT * FROM rating") or die(mysqli_error($konek));
    $reviews = [];
    while ($row = mysqli_fetch_assoc($query_reviews)) {
        $reviews[$row['book_id']] = [
            'rating' => $row['rating'],
            'review' => $row['review']
        ];
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
        .carousel-control-prev, .carousel-control-next {
            opacity: 0;
            transition: opacity 0.3s;
        }
        #carouselExampleControls:hover .carousel-control-prev,
        #carouselExampleControls:hover .carousel-control-next {
            opacity: 1;
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

    <main class="m-auto py-5">
    <div class="container">
        <form action="" method="get">
            <div class="d-flex justify-content-center mb-4">
                <?php if(isset($_GET['search'])) { ?>
                    <input style="background-color: #4F98CA; color:white;" type="text" class="form-control me-2 w-50" placeholder="Type here for search" aria-label="Search" name="search" value="<?=$_GET['search']?>">
                <?php }else{ ?>
                    <input style="background-color: #4F98CA; color:white;" type="text" class="form-control me-2 w-50" placeholder="Type here for search" aria-label="Search" name="search">
                <?php } ?>
            </div>
        </form>
        <div class="d-flex justify-content-center mb-4">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" style="background-color: #4F98CA; width: 300px; text-align:left;" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="?">All</a></li>
                    <li><a class="dropdown-item" href="?category=Informatics">Informatics</a></li>
                    <li><a class="dropdown-item" href="?category=Industrial Engineering">Industrial Engineering</a></li>
                    <li><a class="dropdown-item" href="?category=Information Systems">Information Systems</a></li>
                    <li><a class="dropdown-item" href="?category=Agriculture">Agriculture</a></li>
                    <li><a class="dropdown-item" href="?category=Social and Political Science">Social and Political Science</a></li>
                    <li><a class="dropdown-item" href="?category=Economics">Economics</a></li>
                </ul>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($books as $book): ?>
                <div class="col">
                    <div class="card h-100" style="border: none;">
                        <img src="uploads/<?= htmlspecialchars($book['cover']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text text-truncate"><?= htmlspecialchars($book['description']) ?></p>
                            <a href="book_detail.php?id=<?= $book['book_id'] ?>" class="btn btn-primary">Read more</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>