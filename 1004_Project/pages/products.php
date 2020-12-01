<?php
 if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
 //DB Login
include "../page_incs/db_onetimelogin.php";

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
}


$num_results_on_page = 6;

// The current page, in the URL this will appear as index.php?page=products&p=1, index.php?page=products&p=2, etc...
// 
//$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

$total_products = $con->query('SELECT * FROM products')->num_rows;

// Select products ordered by the date added
if ($stmt = $con->prepare('SELECT * FROM products ORDER BY p_date_added  DESC LIMIT ?,?')) {
    // bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT
    $calc_page = ($page - 1) * $num_results_on_page;
	$stmt->bind_param('ii', $calc_page, $num_results_on_page);
	$stmt->execute(); 
	// Get the results...
        // Fetch the products from the database and return the result as an Array
	$result = $stmt->get_result();
}

$products = $result->fetch_all(MYSQLI_ASSOC);


 if(isset($_POST["add_to_cart"]))  
 {  
      if(isset($_SESSION["shopping_cart"]))  
      {  
           $item_array_id = array_column($_SESSION["shopping_cart"], "item_id");  
           if(!in_array($_GET["id"], $item_array_id))  
           {  
                $count = count($_SESSION["shopping_cart"]);  
                $item_array = array(  
                     'item_id'               =>     $_GET["id"],  
                     'item_name'               =>     $_POST["hidden_name"],  
                     'item_price'          =>     $_POST["hidden_price"],  
                     'item_quantity'          =>     $_POST["quantity"],
                     'item_image' => $_POST["hidden_image_url"]
                   
                );  
                $_SESSION["shopping_cart"][$count] = $item_array;  
                  echo '<script>alert("Item Added")</script>';  
           }  
           else  
           {  
                echo '<script>alert("Item Already Added")</script>';  
                //echo '<script>window.location="index.php"</script>';  
           }  
      }  
      else  
      {  
           $item_array = array(  
                'item_id'               =>     $_GET["id"],  
                'item_name'               =>     $_POST["hidden_name"],  
                'item_price'          =>     $_POST["hidden_price"],  
                'item_quantity'          =>     $_POST["quantity"],
                'item_image' => $_POST["hidden_image_url"]
                
           );  
           $_SESSION["shopping_cart"][0] = $item_array;  
      }  
 }  
?>
      <!doctype html>
<html lang="en">
<head>
    <title>Phone Case Shop</title>
    <?php
    include "../page_incs/head.inc.php";
    ?>
</head>

<body>
<?php
include "../page_incs/nav.inc.php";
?>
 <main class="container">

     <div class="container">
            <div class="row">
                <h1>&nbsp;Phone Cases and Accessories</h1>
            </div>

            <div class="row">
                <h6>&nbsp;<?= $total_products ?> Products</h6>
            </div>

            <div class="row">
                <div id="wrap">


            <?php
            if (!empty($_SESSION["shopping_cart"])) {
                $cart_count = count(array_keys($_SESSION["shopping_cart"]));
                ?>
                <?php
            }
      

           foreach ($products as $product):
               ?>          
          
                <div class="item">
                    <article>
                        <figure>
                            <a href="index.php?page=product&id=<?=$product['product_id']?>" class="product">
                                <img src="../phone_cases_img/<?=$product['p_img']?>" width="80px" height="150px" class="phone_image" alt="<?=$product['p_name']?>">
                            </a>

                            <h5 class="text-body"><?=$product['p_name']?></h5>
                            <h5 class="text-info">&dollar;<?=$product['p_price']?></h5>
                            <form method="post" action="products.php?action=add&id=<?php echo $product["product_id"]; ?>&page=<?php echo $page ?>">
                                <div class="form-group">
                                    <label for="quantity"></label>
                                    <input class="form-control" type="number" id="quantity" name="quantity"
                                           value="1" min="1" max="<?=$product['p_qty']?>" placeholder="Quantity" required>
                                </div> 
                               <input type="hidden" name="hidden_name" value="<?php echo $product["p_name"]; ?>" />  
                               <input type="hidden" name="hidden_price" value="<?php echo $product["p_price"]; ?>" />
                               <input type="hidden" name="hidden_image_url" value="<?php echo $product["p_img"]; ?>" />
                               <input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-success" value="Add to Cart" /> 
                            </form>

                        </figure>
                    </article>
                </div>
            
        <?php
           
            endforeach;
            ?>         
        </div>
      </div>
        			<?php if (ceil($total_products / $num_results_on_page) > 0): ?>
			<ul class="pagination">
				<?php if ($page > 1): ?>
                            <li class="prev"><a href="products.php?page=<?php echo $page-1 ?>">Prev</a></li>
				<?php endif; ?>

				<?php if ($page > 3): ?>
				<li class="start"><a href="products.php?page=1">1</a></li>
				<li class="dots">...</li>
				<?php endif; ?>

				<?php if ($page-2 > 0): ?><li class="page"><a href="products.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
				<?php if ($page-1 > 0): ?><li class="page"><a href="products.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

				<li class="currentpage"><a href="products.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

				<?php if ($page+1 < ceil($total_products / $num_results_on_page)+1): ?><li class="page"><a href="products.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
				<?php if ($page+2 < ceil($total_products / $num_results_on_page)+1): ?><li class="page"><a href="products.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

				<?php if ($page < ceil($total_products / $num_results_on_page)-2): ?>
				<li class="dots">...</li>
				<li class="end"><a href="products.php?page=<?php echo ceil($total_products / $num_results_on_page) ?>"><?php echo ceil($total_products / $num_results_on_page) ?></a></li>
				<?php endif; ?>

				<?php if ($page < ceil($total_products / $num_results_on_page)): ?>
				<li class="next"><a href="products.php?page=<?php echo $page+1 ?>">Next</a></li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
   </main>        
</body>
<?php
include "../page_incs/footer.inc.php";
?>