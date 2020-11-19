<?php
session_start(); //start session
?>
<head>
    <title>Phone Case Shop</title>
    <?php
    include "../page_incs/head.inc.php";
    ?>
    <style>
        article {
            text-align: center;
        }

        #companyInfo {
            margin-top: -100px;
        }

        #logo {
            height: 200px;
        }
    </style>
</head>
<body>
    <?php
    include "../page_incs/nav.inc.php";
    ?>
    <header class="jumbotron parallax text-center";">
        <div>
            <h1 class="display-4">Welcome to <img id="logo" src="../images/phonix_logo.png"></h1>
            <br/>
            <h4 id="companyInfo" style="color:white">The premier place to buy your mobile phones and accessories</h4>
        </div>
    </header>
    <main class="container">
        <h3>Popular items</h3>
        <?php
//Nicholas db connect
//$con = mysqli_connect("localhost", "root", "E*z?%-iD8#hr", "1004_project");
//Kah Wei db connect
        $con = mysqli_connect("localhost", "root", "kahwei", "1004_project");


//// The amounts of products to show on each page
        $num_products_on_each_page = 6;

// The current page, in the URL this will appear as index.php?page=products&p=1, index.php?page=products&p=2, etc...
        $current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int) $_GET['p'] : 1;

// Select products ordered by the date added
        $stmt = $con->prepare("SELECT * FROM products ORDER BY p_price ASC LIMIT ?,?");

// bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT
        $var1 = (($current_page - 1) * $num_products_on_each_page);

        $stmt->bind_param('ii', $var1, $num_products_on_each_page);
        $stmt->execute();

// Fetch the products from the database and return the result as an Array
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);

// Get the total number of products
        $total_products = $result->num_rows;
        ?> 
            <div class="row">
                <div class="grid-container">

                    <?php foreach ($products as $product): ?>

                        <div class="grid-item">
                            <article>
                                <figure>
                                    <a href="index.php?page=product&id=<?= $product['product_id'] ?>" class="product">
                                        <img src="../images/phone_cases_img/<?= $product['p_img'] ?>" width="80px" height="150px" class="phone_image" alt="<?= $product['p_name'] ?>">
                                    </a>

                                    <h5 class="text-body"><?= $product['p_name'] ?></h5>
                                    <h5 class="text-info">&dollar;<?= $product['p_price'] ?></h5>

                                    <div class="form-group">
                                        <label for="quantity"></label>
                                        <input class="form-control" type="number" id="quantity" name="quantity"
                                               placeholder="1">
                                    </div>

                                    <input type="hidden" name="hidden_name" value="<?= $product['p_name'] ?>">
                                    <input type="hidden" name="hidden_price" value="<?= $product['p_price'] ?>">
                                    <input type="submit" name="add" style="margin-top: 5px;" class="btn btn-success" value="Add to Cart">
                                </figure>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="buttons">
            <?php if ($current_page > 1): ?>
                <a href="index.php?page=products&p=<?= $current_page - 1 ?>">Prev</a>
            <?php endif; ?>

            <?php if ($total_products > ($current_page * $num_products_on_each_page) - $num_products_on_each_page + count($products)): ?>
                <a href="index.php?page=products&p=<?= $current_page + 1 ?>">Next</a>
            <?php endif; ?>
        </div>
    </main>        
</body>
<?php
include "../page_incs/footer.inc.php";
?>