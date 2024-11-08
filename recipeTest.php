<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals - Recipe Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        input, button {
            padding: 10px;
            margin: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <h2>Recipe Search</h2>

    <input type="text" id="userInput" placeholder="Enter a recipe" />
    <button onclick="searchRecipes()">Search</button>
    <div id="errorMessage">

    </div>

    <div id="displayResultsHere"></div>

    <script src="app.js"></script>
</body>
</html>
