<?php
require_once 'includes/db.php';

$exams = [
    [
        'name' => 'JEE Main 2026',
        'slug' => 'jee-main-2026',
        'conducting_body' => 'National Testing Agency (NTA)',
        'level' => 'NATIONAL',
        'exam_date' => '2026-01-24'
    ],
    [
        'name' => 'GATE 2026',
        'slug' => 'gate-2026',
        'conducting_body' => 'IIT Roorkee',
        'level' => 'NATIONAL',
        'exam_date' => '2026-02-01'
    ],
    [
        'name' => 'CAT 2025',
        'slug' => 'cat-2025',
        'conducting_body' => 'IIM Rohtak',
        'level' => 'NATIONAL',
        'exam_date' => '2025-11-24'
    ],
    [
        'name' => 'NEET UG 2026',
        'slug' => 'neet-ug-2026',
        'conducting_body' => 'National Testing Agency (NTA)',
        'level' => 'NATIONAL',
        'exam_date' => '2026-05-03'
    ],
    [
        'name' => 'BITSAT 2026',
        'slug' => 'bitsat-2026',
        'conducting_body' => 'BITS Pilani',
        'level' => 'UNIVERSITY',
        'exam_date' => '2026-05-20'
    ]
];

echo "Starting exam seeding...\n";

foreach ($exams as $exam) {
    try {
        $exam_id = bin2hex(random_bytes(16));
        $stmt = $pdo->prepare("INSERT INTO exams (id, name, slug, conducting_body, level) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $exam_id,
            $exam['name'],
            $exam['slug'],
            $exam['conducting_body'],
            $exam['level']
        ]);
        
        // Seed Session
        $session_id = bin2hex(random_bytes(16));
        $stmt_session = $pdo->prepare("INSERT INTO exam_sessions (id, exam_id, session_name, year, exam_date) VALUES (?, ?, ?, ?, ?)");
        $stmt_session->execute([
            $session_id,
            $exam_id,
            'Main Session',
            2026,
            $exam['exam_date']
        ]);

        echo "Inserted: " . $exam['name'] . "\n";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Skipped (Duplicate): " . $exam['name'] . "\n";
        } else {
            echo "Error inserting " . $exam['name'] . ": " . $e->getMessage() . "\n";
        }
    }
}

echo "Seeding complete!\n";
?>
