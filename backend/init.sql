-- Create the recipe_app database
CREATE DATABASE IF NOT EXISTS recipe_app;
USE recipe_app;

-- Create the recipes table
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    ingredients TEXT,
    steps TEXT,
    time_per_step INT,
    difficulty VARCHAR(20)
);

-- Insert 5 recipes
INSERT INTO recipes (title, category, ingredients, steps, time_per_step, difficulty) VALUES
('Spaghetti Bolognese', 'Main', 'spaghetti, minced beef, onion, garlic, tomato', 'Cook pasta. Cook beef. Mix with sauce.', 5, 'Medium'),
('Vegan Pancakes', 'Vegan', 'flour, oat milk, banana, baking powder', 'Mix ingredients. Cook on skillet.', 4, 'Easy'),
('Healthy Pizza', 'Main', 'whole wheat flour, tomato sauce, cheese, vegetables', 'Make dough. Add toppings. Bake.', 6, 'Medium'),
('Easy Lamb Biryani', 'Main', 'lamb, rice, onion, spices', 'Cook lamb. Cook rice. Layer and steam.', 7, 'Hard'),
('Couscous Salad', 'Salad', 'couscous, cucumber, tomato, lemon juice', 'Soak couscous. Chop veg. Mix all.', 3, 'Easy');
