<link rel="ICON" href="/logos/Emblem.ico" type="image/ico" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">




<style>

    table {
        border-collapse: collapse;
        border: none;
        background: white;
    }
    
    td, th {
        padding: 5px 5px;
        font-size: 18px;
        text-align: left;
    }
    
    tr:hover {
        background-color: #ddd; /* Changes the background color on hover */
    }
    
    
    .table-button {
        padding: 5px 5px;
        border-radius: 10px;
    }
    
    .edit-field {
        padding: 5px 5px;
        border: none;
        border-radius: 10px;
        word-wrap: break-word;
    }
    
    .editing-row {
        background-color: lightblue; 
    }
    
    .body {
        font-family: Arial, sans-serif;
    }
    
    .home {
        color:brown;
    }
    .deposits {
        color: lightblue dark;
    }
    .rewards {
        color: red;
    }
    .book {
        color: lavender dark;
    }
    .inventory {
        color: green;
    }
    .expenses {
        color:purple;
    }
    .members {
        color: grey;
    }
    .loans {
        color: blue;
    }
    .marketing {
        color: navy;
    }
    .withdraw {
        color: indigo;
    }
    .webmanager {
        color: violet;
    }
    .performance {
        color: pink;
    }
    .profile {
        color: grey;
    }
    .logout {
        color: aqua;
    }
    
    .late {
        color: red;
    }
    .navbar {
        background-image: url('/logos/header-img-horizontal.jpg'); /* Replace 'path/to/your/image.jpg' with the actual path to your image */
        background-size: cover;
        background-position: center;
    }
    .navbar-toggler {
        background-color: white;
    }
</style>


<nav class="navbar bg-body-secondary fixed-top border border-info rounded">
    <div class="container-fluid " style="font-size:18px;">
        <a class="navbar-brand" href="/">
            <img src="/logos/Logo.jpg" alt="Logo" width="100px" height="100px" />
        </a>
        <button <?php if(!isset($_SESSION['username'])){ echo'hidden'; } ?> class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border rounded bg-success text-light">
                <h5 class="offcanvas-title " id="offcanvasNavbarLabel">Essentialapp</h5>
                <div>
                    <span>Welcome </span>
                    <span class="text-capitalize"> <?php if(isset($_SESSION['username'])){ echo " " . $_SESSION['username']; } else { echo 'User';  } ?></span>
                </div>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="/"><i class="bi bi-house-fill home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customers"> <i class="bi bi-people-fill members"></i> Customer Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/loans"> <i class="bi bi-tag-fill loans"></i> Loan Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/due_today"> <i class="bi bi-calendar-check book"></i> Loans Due Today</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/past_due"> <i class="bi bi-alarm-fill late"></i> Loans Past Due</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/cleared"> <i class="bi bi-building "></i> Cleared Loans</a> 
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/portfolio"> <i class="bi bi-people-fill members"></i> Portfolio Management</a>
                    </li>
                    <li <?php if($admin !== 2){ echo 'hidden'; } ?> class="nav-item">
                        <a class="nav-link" href="/inventory"> <i class="bi bi-cart-plus-fill inventory"></i> Inventory Management</a>
                    </li>
                    <li <?php if($admin !== 2){ echo 'hidden'; } ?> class="nav-item">
                        <a class="nav-link" href="/expenses"> <i class="bi bi-building-fill-dash expenses"></i> Expense Management</a>
                    </li>
                    <li <?php if($admin !== 2){ echo 'hidden'; } ?> class="nav-item">
                        <a class="nav-link" href="/accounting"> <i class="bi bi-building"></i> Accounting Management</a>
                    </li>
                    <li <?php if($admin !== 2){ echo 'hidden'; } ?> class="nav-item">
                        <a class="nav-link" href="/marketing"> <i class="bi bi-broadcast-pin marketing"></i> Marketing</a>
                    </li>
                    <li <?php if($admin !== 2){ echo 'hidden'; } ?> class="nav-item">
                        <a class="nav-link" href="/staff"> <i class="bi bi-people-fill members"></i> Staff Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/profile"> <i class="bi bi-person-circle profile"></i> My Account</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="/logout"> <i class="bi bi-box-arrow-right"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
