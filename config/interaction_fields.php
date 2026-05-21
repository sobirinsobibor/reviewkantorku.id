<?php

// config/interaction_fields.php
return [
    'review' => [
        'main_contents' => [
            'experience', 
            // 'positive_notes'
        ],
        'labels' => [
            'experience'     => 'Pengalaman Kerja',
            // 'positive_notes' => 'Hal Positif',
        ],
    ],
    'cerita_magang' => [
        'main_contents' => ['title', 'story', 'lessons'],
        'labels' => [
            'title'   => 'Judul',
            'story'   => 'Cerita',
            'lessons' => 'Pelajaran',
        ],
    ],
    'menfess' => [
        'main_contents' => ['message'],
        'labels' => [
            'message' => 'Pesan',
        ],
    ],
    'qna' => [
        'main_contents' => ['question_title', 'content'],
        'labels' => [
            'question_title' => 'Judul Pertanyaan',
            'content'        => 'Isi Pertanyaan',
        ],
    ],
    'reply' => [
        'main_contents' => ['message'],
        'labels' => [
            'message' => 'Pesan Balasan',
        ],
    ],
];