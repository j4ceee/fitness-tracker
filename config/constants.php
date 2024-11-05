<?php

return [
    'max_cheat_days' => 4,
    'calorie_goal' => [
        'min' => 0,
        'max' => 10000,
    ],
    'meals' => [
        'min' => 0,
        'max' => 5,
    ],
    'water' => [
        'options' => [0, 0.5, 1, 1.5, 2, 2.5, 3],
    ],
    'training_duration' => [
        'options' => range(0, 210, 15),
    ],
    'steps' => [
        'options' => range(0, 40, 1),
    ],
    'weight' => [
        'min' => 0,
        'max' => 200,
    ],

    'points' => [
        'calorie_percentage' => [
            [1, 2],
            [0.9, 1],
            [0.6, 0],
            [-1]
        ],
        'training_duration' => [
            'interval' => 30,
            'points' => 2,
        ],
        'water' => [
            [3, 3],
            [2, 2],
            [1.5, 1],
            [1, 0],
            [-1]
        ],
        'steps_per_point' => 3, // steps in km
        'warm_meal_points' => 2,
        'cold_meal_points' => 1,
        'alcohol_points' => -5,
        'fast_food_points' => -2,
        'sweets_points' => -1,
    ],
];
