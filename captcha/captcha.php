<?php
// Start a secure session with strict mode enabled
ini_set('session.use_strict_mode', 1);
session_start();

header('Content-type: image/png');

// Create a larger temporary image to avoid text overflow
$temp_image = imagecreatetruecolor(80, 60); // 60px height to accommodate larger text
imagesavealpha($temp_image, true); // Preserve transparency
$transparent_color = imagecolorallocatealpha($temp_image, 0, 0, 0, 127); // Fully transparent
imagefill($temp_image, 0, 0, $transparent_color); // Fill the image with the transparent color

// Define noise and stroke colors
$line_color = imagecolorallocate($temp_image, 64, 64, 64); // Dark gray
$black = imagecolorallocate($temp_image, 0, 0, 0); // Black for the stroke

// Generate a cryptographically secure 4-character string (hexadecimal)
$secure_code = bin2hex(random_bytes(2)); // Produces a 4-character string

// Store a hashed version of the code in the session for verification
$_SESSION['captcha_code'] = hash('sha256', $secure_code);

// Add minimal noise (random lines) to the temporary image
for ($i = 0; $i < 2; $i++) {
    imageline($temp_image, 0, rand(0, 60), 80, rand(0, 60), $line_color);
}

// Define font properties
$font = __DIR__ . '/../fonts/ArialCE.ttf'; // Path to the font
$font_size = 20; // Font size set to 20px
$x = 5; // Initial X-coordinate
$y = 40; // Y-coordinate to align text within the image

// Render each letter with a random color and a black stroke
for ($i = 0; $i < strlen($secure_code); $i++) {
    $angle = rand(-10, 10); // Adjusted angle for better fit
    $text_color = imagecolorallocate($temp_image, rand(0, 255), rand(0, 255), rand(0, 255)); // Random color

    // Draw the black stroke around each letter using a 3x3 grid
    for ($dx = -1; $dx <= 1; $dx++) {
        for ($dy = -1; $dy <= 1; $dy++) {
            if ($dx != 0 || $dy != 0) {
                imagettftext($temp_image, $font_size, $angle, $x + $dx, $y + $dy, $black, $font, $secure_code[$i]);
            }
        }
    }

    // Draw the colored letter on top of the stroke
    imagettftext($temp_image, $font_size, $angle, $x, $y, $text_color, $font, $secure_code[$i]);

    // Adjust X position for the next letter
    $x += 18; // Adjusted spacing between letters
}

// Create the final 80x30 image and copy the cropped content from the temporary image
$image = imagecreatetruecolor(80, 30); // Final image with exact size 80x30
imagesavealpha($image, true); // Preserve transparency
imagefill($image, 0, 0, $transparent_color); // Fill with transparent color

// Copy the relevant part of the temporary image into the final image
imagecopy($image, $temp_image, 0, 0, 0, 15, 80, 30); // Crop and align correctly

// Free the temporary image
imagedestroy($temp_image);

// Output the final image as PNG and free memory
imagepng($image);
imagedestroy($image);
?>
