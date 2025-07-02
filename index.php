<?php
include_once __DIR__ . '/components/Navbar.php';
include_once __DIR__ . '/components/HeroSection.php';
include_once __DIR__ . '/components/FeaturesBenefitsSection.php';
include_once __DIR__ . '/components/CallToActionSection.php';
include_once __DIR__ . '/components/FooterSection.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examplify - Redefine Your Learning Journey</title>
    <link href="./src/output.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    <!-- Navbar -->
   <?php Navbar();?>

   <!-- Hero Section -->
   <?php HeroSection();?>

   <!-- Features & Benefits Section -->
   <?php FeaturesBenefitsSection();?>

    <!-- Final Call to Action -->
    <?php CallToActionSection();?>

    <?php FooterSection(); ?>

</body>
</html>
