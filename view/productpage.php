<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $userInfo;
if (isset ($_SESSION["uid"])) {
    ReLogInUser();
}

// Check if $userInfo is set, and then set the username
if (isset ($userInfo)) {
    $username = $userInfo->getUsername();
}

// Get recommended products
$recommendedProducts = GetRecommendedProducts($productDetails->getProductID());

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $productDetails->getName(); ?> - EvoTech
    </title>
    <link rel="stylesheet" href="/view/css/productpage.css">
</head>

<nav>
    <?php
    $currentPage = "products";
    include __DIR__ . '/nav.php';
    ?>
</nav>

<body>

    <main>
        <div class="main-product-box">
            <div class="product-image">
                <img
                    src="/view/images/products/<?php echo $productDetails->getProductID(); ?>/<?php echo $productDetails->getMainImage() ?>">
            </div>

            <div class="product-details">
                <h2>
                    <?php echo $productDetails->getName(); ?>
                </h2>
                <p class="product-price">£
                    <?php echo $productDetails->getPrice(); ?>
                </p>

                <div class="product-description">
                    <h4><?php echo $productDetails->getDescription(); ?></h4>
                </div>

                <div class="product-stock">
                    <p>Available Stock: <?php echo $productDetails->getStock(); ?></p>
                </div>

                <form action="/add-to-basket" method="post" class="add-to-basket-form" id="add-to-basket-form">
                    <input type="hidden" name="productID" value="<?php echo $productDetails->getProductID(); ?>">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="quantity">Quantity</label>
                        <input id="quantityInput" type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $productDetails->getStock(); ?>" placeholder="Quantity" aria-label="Quantity" aria-describedby="quantiy-addon">
                        <button type="submit" class="btn btn-<?= isset ($_SESSION['uid']) ? 'primary' : 'secondary'; ?>"type="button" id="button-addon2" <?php echo isset ($_SESSION['uid']) ? '' : 'disabled'; ?>>
                            <?php echo isset ($_SESSION['uid']) ? 'Add to Basket' : 'Log in to Add to Basket'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="recommendation-section">
            <h2 class="recommendation-title">YOU MIGHT ALSO LIKE</h2>
        </div>

        <div class="recommendation-section">

            <div class="recommended-products-wrapper">
                <?php foreach ($recommendedProducts as $recommendedProduct): ?>
                    <a href="/product?productID=<?php echo $recommendedProduct->getProductID(); ?>"
                        class="recommended-product-link">
                        <div class="recommended-product-box">
                            <div class="recommended-product-image">
                                <img src="/view/images/products/<?php echo $recommendedProduct->getProductID(); ?>/<?php echo $recommendedProduct->getMainImage(); ?>"
                                    alt="Recommended Product Image">
                            </div>
                            <div class="recommended-product-details">
                                <h5>
                                    <?php echo $recommendedProduct->getName(); ?>
                                </h5>
                                <p class="recommended-product-price">£
                                    <?php echo $recommendedProduct->getPrice(); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php' ?>
    <script>
        window.addEventListener('load', function() {
        var productImage = document.querySelector('.product-image img');
        var productDetails = document.querySelector('.product-details');

        // Set the height of product details to match the height of the image
        productDetails.style.height = productImage.offsetHeight + 'px';
});
    </script>
</body>

</html>