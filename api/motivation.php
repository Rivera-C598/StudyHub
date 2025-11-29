<?php
// api/motivation.php
header('Content-Type: application/json; charset=utf-8');

$tips = [
    "Study in focused 25-minute blocks and rest for 5 minutes.",
    "Teach a concept to someone else to check if you really understand.",
    "Turn big scary tasks into tiny steps you can finish today.",
    "Review your notes within 24 hours to improve retention.",
    "Sleep, hydration, and short breaks count as 'study tools' too."
];

echo json_encode([
    'tip' => $tips[array_rand($tips)]
]);
