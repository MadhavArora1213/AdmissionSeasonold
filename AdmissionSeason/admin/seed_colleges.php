<?php
require_once 'includes/db.php';

$colleges = [
    [
        'name' => 'Indian Institute of Technology (IIT) Delhi',
        'slug' => 'iit-delhi',
        'type' => 'GOVERNMENT',
        'state' => 'Delhi',
        'city' => 'New Delhi',
        'nirf_rank' => 2,
        'about_description' => 'Indian Institute of Technology Delhi is a public technical and research university located in Hauz Khas, Delhi, India.'
    ],
    [
        'name' => 'Indian Institute of Management (IIM) Ahmedabad',
        'slug' => 'iim-ahmedabad',
        'type' => 'GOVERNMENT',
        'state' => 'Gujarat',
        'city' => 'Ahmedabad',
        'nirf_rank' => 1,
        'about_description' => 'IIM Ahmedabad is a business school located in Ahmedabad, Gujarat, India. It was the second Indian Institute of Management to be established.'
    ],
    [
        'name' => 'Birla Institute of Technology and Science (BITS) Pilani',
        'slug' => 'bits-pilani',
        'type' => 'PRIVATE',
        'state' => 'Rajasthan',
        'city' => 'Pilani',
        'nirf_rank' => 25,
        'about_description' => 'BITS Pilani is a private deemed university in Pilani, India. It focuses primarily on higher education in engineering and the sciences.'
    ],
    [
        'name' => 'Vellore Institute of Technology (VIT)',
        'slug' => 'vit-vellore',
        'type' => 'PRIVATE',
        'state' => 'Tamil Nadu',
        'city' => 'Vellore',
        'nirf_rank' => 11,
        'about_description' => 'VIT is a private deemed university located in Vellore, Tamil Nadu, India. It has campuses in Vellore, Chennai, Bhopal and Amaravati.'
    ],
    [
        'name' => 'Lovely Professional University (LPU)',
        'slug' => 'lpu-phagwara',
        'type' => 'PRIVATE',
        'state' => 'Punjab',
        'city' => 'Phagwara',
        'nirf_rank' => 38,
        'about_description' => 'LPU is a private university located in Phagwara, Punjab, India. The university was established in 2005 by Lovely International Trust.'
    ],
    [
        'name' => 'All India Institute of Medical Sciences (AIIMS) Delhi',
        'slug' => 'aiims-delhi',
        'type' => 'GOVERNMENT',
        'state' => 'Delhi',
        'city' => 'New Delhi',
        'nirf_rank' => 1,
        'about_description' => 'AIIMS Delhi is a public medical research university and hospital based in New Delhi, India.'
    ]
];

echo "Starting college seeding...\n";

foreach ($colleges as $college) {
    try {
        // ID is varchar(36) with default uuid(), so we can omit it if supported, 
        // but let's generate one to be safe if the default doesn't trigger in PDO.
        $id = bin2hex(random_bytes(16)); 
        
        $stmt = $pdo->prepare("INSERT INTO colleges (id, name, slug, type, state, city, nirf_rank, about_description, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $id,
            $college['name'],
            $college['slug'],
            $college['type'],
            $college['state'],
            $college['city'],
            $college['nirf_rank'],
            $college['about_description']
        ]);
        echo "Inserted: " . $college['name'] . "\n";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Skipped (Duplicate): " . $college['name'] . "\n";
        } else {
            echo "Error inserting " . $college['name'] . ": " . $e->getMessage() . "\n";
        }
    }
}

echo "Seeding complete!\n";
?>
