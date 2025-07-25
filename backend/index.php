<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipe Search</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional styling -->
</head>
<body>
    <h1>Find a Recipe</h1>

    <!-- Recipe Search Form -->
    <form action="search.php" method="GET">

        <!-- Keyword Search -->
        <label for="keyword">Keyword:</label>
        <input 
            type="text" 
            id="keyword" 
            name="keyword" 
            placeholder="e.g. pasta, flour, onion"
        >

        <!-- Category Filter -->
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="">Any</option>
            <option value="Main">Main</option>
            <option value="Vegetarian">Vegetarian</option>
            <option value="Vegan">Vegan</option>
            <option value="Salad">Salad</option>
        </select>

               <!-- Submit Button -->
        <button type="submit">Search</button>
    </form>
</body>
</html>
