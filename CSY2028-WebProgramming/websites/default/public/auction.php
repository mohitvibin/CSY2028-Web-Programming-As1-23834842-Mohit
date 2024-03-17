<?php
session_start();

// Example array of auctions (replace this with your actual data retrieval logic)
$allAuctions = [
    [
        'id' => 1,
        'title' => 'Station wagon',
        'category' => 'Estate',
        'image' => 'https://imageio.forbes.com/specials-images/imageserve/5d35eacaf1176b0008974b54/0x0.jpg?format=jpg&crop=4560,2565,x790,y784,safe&height=900&width=1600&fit=bounds',
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sodales ornare purus, non laoreet dolor sagittis id. Vestibulum lobortis laoreet nibh, eu luctus purus volutpat sit amet. Proin nec iaculis nulla.',
        'currentBid' => '£1234.00',
    ],
    [
        'id' => 2,
        'title' => 'Tesla Model X',
        'category' => 'Electric',
        'image' => 'https://images.prismic.io/carwow/c340a77d-af56-4562-abfb-bd5518ccb292_2023+Tesla+Model+X+front+quarter+moving.jpg',
        'description' => 'The GT model have a 0-100 km/h (0-62 mph) time in 3.5 seconds, with dual electric motors producing 430kW of power (576hp) and 740Nm of torque combined, Michelin Pilot Sport 4S tyres, a rear electronic limited-slip differential, electronically controlled suspension, and minor exterior and interior changes.',
        'currentBid' => 'Rs70,00,000',
    ],
    [
        'id' => 3,
        'title' => 'Chevrolet Camaro ZL1',
        'category' => 'Coupe',
        'image' => 'banners/coupe.jpg',
        'description' => 'The Camaros 650-hp supercharged 6.2-liter V-8 is a well-behaved and tractable engine that makes power all over the rev range. Whether paired with the standard six-speed manual transmission or the available 10-speed automatic, the supercharged small-block V-8 is an absolute juggernaut with 650 pound-feet of torque.',
        'currentBid' => 'Rs45,00,000',
    ],
    [
        'id' => 4,
        'title' => 'Jaguar XE',
        'category' => 'Saloon',
        'image' => 'banners/saloon.jpg',
        'description' => ' The performance offered by its Ingenium petrol and diesel engines isnt quite as impressive as its ride and handling balance. The P250 petrol is a little thirsty and not exactly smooth if you really ring its neck, while the automatic gearbox its paired with isnt the slickest operator, either',
        'currentBid' => 'Rs56,00,000',
    ],
    [
        'id' => 5,
        'title' => 'Mahendra Thar',
        'category' => '4x4',
        'image' => 'banners/Thar.jpg',
        'description' => '
        The Mahindra Thar, a robust second-hand car, is powered by the mHawk 130 CRDe diesel engine with a 2184 cm3 capacity. It delivers a punchy 97 kW of power at 3750 r/min and a solid torque of 300 Nm at 1600-2800 r/min. The Thars 57-litre fuel tank ensures enduring journeys, complemented by a 20-litre Diesel Exhaust Fluid Tank for environmental efficiency. This off-road-ready vehicle promises a thrilling and reliable adventure, combining power with a touch of rugged charm.',
        'currentBid' => 'Rs15,00,000',
    ],
    [
        'id' => 6,
        'title' => 'Lamborghini Huracan',
        'category' => 'Sports',
        'image' => 'banners/sports.jpg',
        'description' => 'The Huracán maintains the 5.2-litre naturally aspirated Audi/Lamborghini V10 engine with an additional 0.2 litres, compared to the Gallardo, tuned to generate a maximum power output of 449 kW (602 hp; 610 PS). To ensure its balance and performance, the car is mid-engined. The engine has both direct fuel injection and multi-point fuel injection.',
        'currentBid' => 'Rs2,00,00,000',
    ],
    [
        'id' => 7,
        'title' => 'Tata zhest',
        'category' => 'Hybrid',
        'image' => 'banners/tata.jpg',
        'description' => 'he Zest is available in petrol versions; the petrol version of is powered by a new Revotron 1.2-litre turbocharged, 90 PS engine. All come with a 5-speed manual transmission.',
        'currentBid' => 'Rs2,00,000',
    ]
];

$auctionId = isset($_GET['id']) ? $_GET['id'] : 0;
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Find the selected auction in the array based on both ID and category
$selectedAuction = null;
foreach ($allAuctions as $auction) {
    if ($auction['id'] == $auctionId && $auction['category'] == $category) {
        $selectedAuction = $auction;
        break;
    }
}

// Bid placement logic
$maxBidAttempts = 100000000;
$bidAttempts = isset($_SESSION['bidAttempts']) ? $_SESSION['bidAttempts'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bidAmount'])) {
    if ($bidAttempts < $maxBidAttempts) {
        $bidAmount = $_POST['bidAmount'];
        // Perform validation and update bid logic here (e.g., check if bid is higher than current bid)
        // Update the current bid for the selected auction
        $selectedAuction['currentBid'] = $bidAmount;
        $bidAttempts++;
        $_SESSION['bidAttempts'] = $bidAttempts;
        $confirmationMessage = "Thank you for participating in the bidding! Your payment has been received. All future notifications will be sent to your email. We appreciate your involvement!";
    } else {
        $errorMessage = "You have reached the maximum number of bid attempts. Further bids are not allowed.";
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Carbuy Auctions</title>
		<link rel="stylesheet" href="auction.css" />
	</head>

	<body>
		<header>
			<h1><span class="C">C</span>
 			<span class="a">a</span>
			<span class="r">r</span>
			<span class="b">b</span>
			<span class="u">u</span>
			<span class="y">y</span></h1>

			<form action="">
				<input type="text" name="search" placeholder="Search for a car" />
				<input type="submit" name="submit" value="Search" />

				<div class = "loginregister" >
				<a class="login" href="https://as1.v.je/login.php">Login</a>
				<a> / </a>
				<a class="register" href="https://as1.v.je/register.php">Register</a>

				</div>

			</form>

			

		</header>

		<nav>
			<ul>
				<div class="normal">
				<li><a class="categoryLink" href="https://as1.v.je/estate.php">Estate</a></li>
				<li><a class="categoryLink" href="#">Electric</a></li>
				<li><a class="categoryLink" href="#">Coupe</a></li>
				<li><a class="categoryLink" href="#">Saloon</a></li>
				<li><a class="categoryLink" href="#">4x4</a></li>
				<li><a class="categoryLink" href="#">Sports</a></li>
				<li><a class="categoryLink" href="#">Hybrid</a></li>
				<li>

				<div class="more">
  <button class="morebtn">More</button>
  <div class="more-content">
    <a href="#">Convertible</a>
    <a href="#">Sedan</a>
    <a href="#">SUV</a>
  </div>
  </li>
</div>
</ul>
</nav>
</main>

   <!-- Your HTML structure for displaying detailed information about the auction -->
<div class="container">
    <div class="left-section">
        <!-- left section content (Title, Category, Image, Description) -->
        <?php
        if ($selectedAuction) {
            echo '<h1>' . $selectedAuction['title'] . '</h1>';
            echo '<img src="' . $selectedAuction['image'] . '" alt="' . $selectedAuction['category'] . '">';
            echo '<p>' . $selectedAuction['description'] . '</p>';
        } else {
            echo '<p>Auction not found</p>';
        }
        ?>
    </div>
    <div class="right-section">
        <!-- Your right section content (Current bid section) -->
        <?php
        if ($selectedAuction) {
            echo '<p class="price">Current bid: ' . $selectedAuction['currentBid'] . '</p>';
        }
        ?>
        <!-- Bid placement form -->
        <form action="" method="post">
            <label for="bidAmount">Bid Amount:</label>
            <input type="text" id="bidAmount" name="bidAmount" required>
            <button type="submit">Place Bid</button>
        </form>
    </div>
</div>



    <footer>
        &copy; Carbuy 2024
    </footer>
</body>
</html>