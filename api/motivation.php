<?php
// api/motivation.php
header('Content-Type: application/json; charset=utf-8');

$tips = [
    "Study in focused 25-minute blocks and rest for 5 minutes.",
    "Teach a concept to someone else to check if you really understand.",
    "Turn big scary tasks into tiny steps you can finish today.",
    "Review your notes within 24 hours to improve retention.",
    "Sleep, hydration, and short breaks count as 'study tools' too.",
    "Active recall is more effective than passive reading. Test yourself!",
    "Study the hardest material when you're most alert.",
    "Create connections between new and existing knowledge.",
    "Use the Feynman Technique: Explain concepts in simple terms.",
    "Take handwritten notes - it improves memory retention.",
    "Study in different locations to strengthen memory associations.",
    "Practice spaced repetition - review material at increasing intervals.",
    "Eliminate distractions: put your phone in another room.",
    "Exercise before studying to boost cognitive function.",
    "Use mnemonics and memory palaces for complex information.",
    "Study with purpose - set clear goals for each session.",
    "Take breaks to let your brain consolidate information.",
    "Teach what you learn - it's the ultimate test of understanding.",
    "Use multiple senses: read aloud, draw diagrams, write summaries.",
    "Don't cram - distributed practice beats massed practice every time.",
    "Stay hydrated - even mild dehydration affects cognitive performance.",
    "Use the 2-minute rule: if it takes less than 2 minutes, do it now.",
    "Create a dedicated study space free from distractions.",
    "Use background music without lyrics if it helps you focus.",
    "Reward yourself after completing study goals.",
    "Study before bed to enhance memory consolidation during sleep.",
    "Use flashcards for memorization - digital or physical.",
    "Join a study group to gain different perspectives.",
    "Practice retrieval: close your notes and write what you remember.",
    "Use color coding to organize and categorize information."
];

echo json_encode([
    'tip' => $tips[array_rand($tips)]
]);
