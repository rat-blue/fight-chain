<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="icon.png">
    <title>UFC Fighter Chain Finder</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
            color: #FFFFFF;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #000000; /* Fully pitch black background */
        }

        .starry-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: #FFFFFF;
            border-radius: 50%;
            animation: moveStar linear infinite;
            opacity: 0.5;
        }

        @keyframes moveStar {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            5% {
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) translateX(100vw);
                opacity: 0;
            }
        }

        .container {
            background-color: rgba(36, 39, 43, 0.9);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            z-index: 1;
        }

        h1 {
            color: #00B100;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        p {
            margin-bottom: 1.5rem;
            color: #B0B0B0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-weight: bold;
            color: #B0B0B0;
        }

        input[type="text"] {
            padding: 0.75rem;
            border: 1px solid #00B100;
            border-radius: 8px;
            font-size: 1rem;
            color: #FFFFFF;
            background-color: #24272B;
            font-family: 'Lucida Console', Monaco, monospace;
        }

        input[type="submit"] {
            padding: 0.75rem;
            background-color: #00B100;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            font-family: 'Lucida Console', Monaco, monospace;
        }

        input[type="submit"]:hover {
            background-color: #003580;
        }

        .result {
            margin-top: 20px;
            padding: 20px;
            background-color: #24272B;
            border-radius: 8px;
            border: 1px solid #00B100;
            color: #B0B0B0;
        }

        .chain {
            font-weight: bold;
            color: #00B100;
        }
    </style>
</head>
<body>
    <div class="starry-background">
        <?php for ($i = 0; $i < 100; $i++): ?>
            <div class="star" style="top: <?= rand(-50, 100) ?>%; left: <?= rand(-50, 100) ?>%; animation-duration: <?= rand(20, 60) ?>s; opacity: <?= rand(5, 10) / 10; ?>"></div>
        <?php endfor; ?>
    </div>
    <div class="container">
        <h1>Fight Chain Finder</h1>
        <form method="post" action="">
            <label for="fighterA">Fighter A:</label>
            <input type="text" id="fighterA" name="fighterA" required>
            <label for="fighterD">Fighter B:</label>
            <input type="text" id="fighterD" name="fighterD" required>
            <input type="submit" value="Search">
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fighterA = $_POST['fighterA'];
            $fighterD = $_POST['fighterD'];
            $csvFile = 'complete_ufc_data.csv';

            function findClosestChain($fighterA, $fighterD, $csvFile) {
                $data = array_map('str_getcsv', file($csvFile));
                $headers = array_shift($data);
                $graph = [];

                foreach ($data as $row) {
                    $row = array_combine($headers, $row);
                    $winner = $row['fighter1'];
                    $loser = $row['fighter2'];

                    if (!isset($graph[$winner])) {
                        $graph[$winner] = [];
                    }
                    $graph[$winner][] = $loser;
                }

                $queue = [[$fighterA]];
                $visited = [];

                while (!empty($queue)) {
                    $path = array_shift($queue);
                    $currentFighter = end($path);

                    if ($currentFighter === $fighterD) {
                        return $path;
                    }

                    if (!in_array($currentFighter, $visited)) {
                        $visited[] = $currentFighter;

                        if (isset($graph[$currentFighter])) {
                            foreach ($graph[$currentFighter] as $opponent) {
                                $newPath = $path;
                                $newPath[] = $opponent;
                                $queue[] = $newPath;
                            }
                        }
                    }
                }

                return null;
            }

            $path = findClosestChain($fighterA, $fighterD, $csvFile);

            echo '<div class="result">';
            if ($path) {
                echo "<p class='chain'>Chain: " . implode(' → ', $path) . "</p>";
            } else {
                echo "<p>No chain found from {$fighterA} to {$fighterD}.</p>";
            }
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
