-- create the database
CREATE DATABASE recipe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recipe;

-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AUTHORS
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    profile_url VARCHAR(255)
);

-- CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- DIETARY
CREATE TABLE dietary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- INGREDIENTS
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- RECIPES
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    prep_time VARCHAR(50),
    cook_time VARCHAR(50),
    servings VARCHAR(50),
    author_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES authors(id)
);

-- RECIPE_CATEGORIES
CREATE TABLE recipe_categories (
    recipe_id INT,
    category_id INT,
    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- RECIPE_INGREDIENTS
CREATE TABLE recipe_ingredients (
    recipe_id INT,
    ingredient_id INT,
    quantity VARCHAR(100),
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

-- STEPS
CREATE TABLE steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    step_number INT,
    instruction TEXT,
    time_minutes INT,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- RECIPE_DIETARY
CREATE TABLE recipe_dietary (
    recipe_id INT,
    dietary_id INT,
    PRIMARY KEY (recipe_id, dietary_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (dietary_id) REFERENCES dietary(id) ON DELETE CASCADE
);

-- FAVOURITES
CREATE TABLE favourites (
    user_id INT,
    recipe_id INT,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- RATINGS
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    recipe_id INT,
    difficulty_rating INT CHECK (difficulty_rating BETWEEN 1 AND 5), -- force to choose from 1-5
    taste_rating INT CHECK (taste_rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- USERS
INSERT INTO users (name, email, password_hash) VALUES
('User1 Example', 'user1@aaa.com', 'P@ssw0rd'),
('User2 Example', 'user2@aaa.com', 'P@ssw0rd');

-- AUTHORS
INSERT INTO authors (id, name) VALUES
(1, 'Jo Pratt'),
(2, 'Justine Pattison'),
(3, 'Sunil Vijayakar'),
(4, 'Nargisse Benkabbou'),
(5, 'James Martin'),
(6, 'Samin Nosrat'),
(7, 'Sabrina Ghayour');

-- CATEGORIES
INSERT INTO categories (name) VALUES
('Main'), ('Vegetarian'), ('Vegan'), ('Dessert'), ('Salad'), ('Meat'), ('Breakfast'), ('Snack');

-- DIETARY
INSERT INTO dietary (name) VALUES
('Egg-free'),
('Nut-free'),
('Dairy-free'),
('Pregnancy-friendly'),
('Vegan'),
('Vegetarian'),
('Healthy'),
('Gluten-free');

-- INGREDIENTS (from all 8 recipes, bbc website)
INSERT INTO ingredients (name) VALUES
('olive oil'),
('sun-dried tomato oil'),
('smoked streaky bacon'),
('onion'),
('garlic'),
('lean minced beef'),
('red wine'),
('chopped tomatoes'),
('antipasti marinated mushrooms'),
('bay leaf'),
('dried oregano'),
('dried thyme'),
('balsamic vinegar'),
('sun-dried tomato'),
('salt'),
('black pepper'),
('basil'),
('spaghetti'),
('parmesan'),
('self-raising flour'),
('caster sugar'),
('baking powder'),
('sea salt'),
('soya milk'),
('almond milk'),
('vanilla extract'),
('sunflower oil'),
('self-raising brown flour'),
('self-raising wholemeal flour'),
('plain yoghurt'),
('yellow pepper'),
('orange pepper'),
('courgette'),
('red onion'),
('extra virgin olive oil'),
('dried chilli flakes'),
('mozzarella'),
('cheddar'),
('goats’ cheese'),
('passata sauce'),
('boneless lamb'),
('Greek yoghurt'),
('natural yoghurt'),
('ginger'),
('Kashmiri red chilli powder'),
('ground cumin'),
('ground cardamom seeds'),
('lime'),
('coriander leaves'),
('mint leaves'),
('green chillies'),
('double cream'),
('full-fat milk'),
('saffron strands'),
('basmati rice'),
('pomegranate seeds'),
('preserved lemons'),
('dried cranberries'),
('pine nuts'),
('unsalted shelled pistachio nuts'),
('flatleaf parsley'),
('red wine vinegar'),
('rocket leaves'),
('milk'),
('butter'),
('plums'),
('brown sugar'),
('flaked almonds'),
('icing sugar'),
('brioche'),
('lemon'),
('plum jam'),
('egg'),
('digestive biscuits'),
('granulated sugar'),
('ground cardamom'),
('powdered gelatine'),
('cream cheese'),
('Alfonso mango pulp'),
('rose harissa'),
('lemon juice'),
('white wine vinegar'),
('dried mint'),
('oyster mushrooms'),
('garlic oil'),
('sweet paprika'),
('ground coriander'),
('celery salt'),
('garlic granules'),
('white pitta bread'),
('white cabbage'),
('pickled chillies');

-- RECIPES
INSERT INTO recipes (title, image_url, author_id, prep_time, cook_time, servings) VALUES
('Spaghetti Bolognese', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/spaghettibolognese_67868_16x9.jpg', 1, 'less than 30 mins', '1 to 2 hours', 'Serves 6-8'),
('Vegan Pancakes', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/vegan_american_pancakes_76094_16x9.jpg', 2, 'less than 30 mins', '10 to 30 mins', 'Serves 2'),
('Healthy Pizza', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/healthy_pizza_55143_16x9.jpg', 2, 'less than 30 mins', '10 to 30 mins', 'Serves 2'),
('Easy Lamb Biryani', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/easy_lamb_biryani_46729_16x9.jpg', 3, 'overnight', '1 to 2 hours', 'Serves 6–8'),
('Couscous Salad', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/dried_fruits_and_nuts_18053_16x9.jpg', 4, 'less than 30 mins', 'less than 10 mins', 'Serves 6'),
('Plum Clafoutis', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/plumclafoutis_11536_16x9.jpg', 5, 'less than 30 mins', '30 mins to 1 hour', 'Serves 4-6'),
('Mango Pie', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/mango_pie_18053_16x9.jpg', 6, '30 mins to 1 hour', '30 mins to 1 hour', 'Serves 16'),
('Mushroom Doner', 'https://ichef.bbci.co.uk/food/ic/food_16x9_1600/recipes/mushroom_doner_22676_16x9.jpg', 7, 'less than 30 mins', '10 to 30 mins', 'Serves 4');

-- RECIPE_CATEGORIES
INSERT INTO recipe_categories (recipe_id, category_id) VALUES
(1, 1), (1, 6), -- Spaghetti Bolognese: Main, Meat
(2, 2), (2, 3), (2, 7), -- Vegan Pancakes: Vegetarian, Vegan, Breakfast
(3, 1), (3, 2), -- Healthy Pizza: Main, Vegetarian
(4, 1), (4, 6), -- Easy Lamb Biryani: Main, Meat
(5, 5), (5, 2), -- Couscous Salad: Salad, Vegetarian
(6, 4), -- Plum Clafoutis: Dessert
(7, 4), -- Mango Pie: Dessert
(8, 1), (8, 2); -- Mushroom Doner: Main, Vegetarian

-- RECIPE_INGREDIENTS (for easy reference, add the recipe_id)
-- Spaghetti Bolognese (recipe_id = 1)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(1, 1, '2 tbsp'),
(1, 2, '2 tbsp'),
(1, 3, '6 rashers, chopped'),
(1, 4, '2 large, chopped'),
(1, 5, '3 cloves, crushed'),
(1, 6, '1kg'),
(1, 7, '2 large glasses'),
(1, 8, '2x400g cans'),
(1, 9, '1x290g jar, drained'),
(1, 10, '2'),
(1, 11, '1 tsp'),
(1, 12, '1 tsp'),
(1, 13, 'drizzle'),
(1, 14, '12-14 halves, in oil'),
(1, 15, 'to taste'),
(1, 16, 'to taste'),
(1, 17, 'handful, torn'),
(1, 18, '800g-1kg'),
(1, 19, 'to serve');

-- Vegan Pancakes (recipe_id = 2)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(2, 20, '125g'),
(2, 21, '2 tbsp'),
(2, 22, '1 tsp'),
(2, 23, 'good pinch'),
(2, 24, '150ml'),
(2, 25, '150ml'),
(2, 26, '1/4 tsp'),
(2, 27, '4 tsp, for frying');

-- Healthy Pizza (recipe_id = 3)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(3, 28, '125g'),
(3, 23, 'pinch'),
(3, 30, '125g'),
(3, 31, '1'),
(3, 32, '1'),
(3, 33, '1'),
(3, 34, '1'),
(3, 35, '1 tbsp, plus extra'),
(3, 36, '1/2 tsp'),
(3, 37, '50g'),
(3, 38, '50g'),
(3, 39, '50g'),
(3, 40, '6 tbsp'),
(3, 11, '1 tsp'),
(3, 16, 'to taste'),
(3, 17, 'to serve');

-- Easy Lamb Biryani (recipe_id = 4)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(4, 35, '5 tbsp'),
(4, 4, '2, finely sliced'),
(4, 42, '200g'),
(4, 44, '4 tbsp, grated'),
(4, 5, '3 tbsp, grated'),
(4, 45, '1-2 tsp'),
(4, 46, '5 tsp'),
(4, 47, '1 tsp'),
(4, 15, '4 tsp'),
(4, 48, '1, juice only'),
(4, 49, '30g, chopped'),
(4, 50, '30g, chopped'),
(4, 51, '3-4, chopped'),
(4, 41, '800g, diced'),
(4, 52, '4 tbsp'),
(4, 53, '1.5 tbsp'),
(4, 54, '1 tsp'),
(4, 55, '400g'),
(4, 56, '2 tbsp, optional');

-- Couscous Salad (recipe_id = 5)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(5, 57, '225g'),
(5, 58, '8, chopped'),
(5, 59, '180g'),
(5, 60, '120g, toasted'),
(5, 61, '160g, chopped'),
(5, 1, '125ml'),
(5, 62, '60g, chopped'),
(5, 5, '4 cloves, crushed'),
(5, 63, '4 tbsp'),
(5, 34, '1, chopped'),
(5, 15, '1 tsp'),
(5, 64, '80g');

-- Plum Clafoutis (recipe_id = 6)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(6, 64, '125ml'),
(6, 52, '125ml, plus extra to serve'),
(6, 26, '2-3 drops'), 
(6, 73, '4'),
(6, 21, '170g'),
(6, 20, '1 tbsp'), 
(6, 65, '30g'),           
(6, 66, '500g'),
(6, 67, '2 tbsp'), 
(6, 68, '30g, optional'),
(6, 69, 'to dust');

-- Mango Pie (recipe_id = 7)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(7, 74, '280g'),
(7, 75, '65g for base, 100g for filling'),
(7, 76, '1/4 tsp'),
(7, 65, '128g, melted'),
(7, 15, 'pinch, plus large pinch'),
(7, 77, '2 tbsp + 1/4 tsp'),
(7, 52, '120ml'),
(7, 78, '115g'),
(7, 79, '850g');

-- Mushroom Doner (recipe_id = 8)
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(8, 8, '1x400g tin, plus 2 sliced for garnish'),
(8, 80, '2 tbsp'),
(8, 21, '2 tsp'),
(8, 81, 'good squeeze'),
(8, 4, '1, sliced'),
(8, 82, '2 tsp'),
(8, 61, '20g, chopped'),
(8, 30, '150g'),
(8, 83, '1 heaped tsp'),
(8, 15, 'to taste'),
(8, 16, 'to taste, plus 1/2 tsp'),
(8, 84, '500g, sliced'),
(8, 85, '2 tsp'),
(8, 86, '2 tsp'),
(8, 87, '2 heaped tsp'),
(8, 88, '2 tsp'),
(8, 89, '3 tsp'),
(8, 90, '4'),
(8, 91, '1/4, shredded'),
(8, 92, '4-6, sliced');


## STEPS

-- Spaghetti Bolognese (recipe_id = 1)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(1, 1, 'Heat the oil in a large, heavy-based saucepan and fry the bacon until golden over a medium heat. Add the onions and garlic, frying until softened.', 10),
(1, 2, 'Increase the heat and add the minced beef. Fry it until it has browned, breaking down any chunks of meat with a wooden spoon.', 10),
(1, 3, 'Pour in the wine and boil until it has reduced in volume by about a third.', 10),
(1, 4, 'Reduce the temperature and stir in the tomatoes, drained mushrooms, bay leaves, oregano, thyme and balsamic vinegar.', 5),
(1, 5, 'Blitz or chop the sun-dried tomatoes and add to the pan. Season well with salt and pepper. Cover and simmer for 1-1½ hours until rich and thickened, stirring occasionally.', 75),
(1, 6, 'Stir in the basil and add any extra seasoning if necessary.', 2),
(1, 7, 'Remove from the heat to settle while you cook the spaghetti. Cook the spaghetti in boiling salted water as per packet instructions.', 10),
(1, 8, 'Drain and divide between plates. Scatter parmesan over the spaghetti, add Bolognese sauce, and finish with more cheese and black pepper.', 3);

-- Vegan Pancakes (recipe_id = 2)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(2, 1, 'Mix the flour, baking powder, sugar and a pinch of salt in a bowl.', 2),
(2, 2, 'Whisk the soya milk, vanilla extract, oil and lemon juice together in a jug.', 2),
(2, 3, 'Pour the wet ingredients into the dry and whisk to a smooth batter.', 2),
(2, 4, 'Heat a non-stick frying pan and add a little oil. Pour in some batter and cook for 2-3 minutes until bubbles form.', 3),
(2, 5, 'Flip and cook for another 2 minutes until golden. Repeat with remaining batter.', 10),
(2, 6, 'Serve with maple syrup and fruit.', 1);

-- Healthy Pizza (recipe_id = 3)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(3, 1, 'Mix the flours, yeast and salt in a bowl. Add water and oil to form a dough.', 5),
(3, 2, 'Knead the dough for 5 minutes, then cover and leave to rise for 30 minutes.', 30),
(3, 3, 'Roll out the dough, spread with tomato puree and chopped tomatoes.', 5),
(3, 4, 'Top with mozzarella, tomato slices and bake at 220C for 10 minutes.', 10),
(3, 5, 'Serve with yogurt.', 2);

-- Easy Lamb Biryani (recipe_id = 4)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(4, 1, 'Fry onions and garlic in oil until golden.', 5),
(4, 2, 'Add lamb and brown all over.', 10),
(4, 3, 'Add spices and cook for 2 minutes.', 2),
(4, 4, 'Add rice and water, bring to boil, then simmer until rice is cooked.', 30),
(4, 5, 'Season and serve.', 3);

-- Couscous Salad (recipe_id = 5)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(5, 1, 'Prepare couscous according to packet instructions.', 5),
(5, 2, 'Chop dried fruit, nuts, tomato and onion.', 5),
(5, 3, 'Mix all ingredients together and season.', 5),
(5, 4, 'Serve chilled or at room temperature.', 10);

-- Plum Clafoutis (recipe_id = 6)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(6, 1, 'Preheat oven to 180C. Grease a baking dish.', 5),
(6, 2, 'Halve and stone the plums, arrange in the dish.', 5),
(6, 3, 'Mix flour, sugar and salt. Whisk in eggs, milk and oil to make a batter.', 5),
(6, 4, 'Pour batter over plums.', 2),
(6, 5, 'Bake for 35 minutes until golden and set.', 35),
(6, 6, 'Cool slightly before serving.', 5);

-- Mango Pie (recipe_id = 7)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(7, 1, 'Preheat oven to 180C. Grease a pie dish.', 5),
(7, 2, 'Peel and slice mangoes.', 5),
(7, 3, 'Mix flour, sugar and salt. Whisk in eggs and oil to make a dough.', 5),
(7, 4, 'Press dough into dish, arrange mango slices on top.', 5),
(7, 5, 'Bake for 25 minutes until golden.', 25),
(7, 6, 'Cool before serving.', 5);

-- Mushroom Doner (recipe_id = 8)
INSERT INTO steps (recipe_id, step_number, instruction, time_minutes) VALUES
(8, 1, 'Slice mushrooms and fry in oil until golden.', 5),
(8, 2, 'Warm pita breads.', 2),
(8, 3, 'Fill pitas with mushrooms, lettuce, cucumber, tomato and yogurt.', 5),
(8, 4, 'Season and serve.', 2);

-- RECIPE_DIETARY
-- Spaghetti Bolognese (id=1)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (1, 1), (1, 2);

-- Vegan pancakes (id=2)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (2, 1), (2, 3), (2, 4), (2, 5), (2, 6);

-- Healthy pizza (id=3)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (3, 1), (3, 2), (3, 4), (3, 6), (3, 7);

-- Easy lamb biryani (id=4)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (4, 1), (4, 4), (4, 8);

-- Couscous salad (id=5)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (5, 1), (5, 5), (5, 6);

-- Plum clafoutis (id=6)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (6, 6);

-- Mango pie (id=7)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (7, 1), (7, 2);

-- Mushroom doner (id=8)
INSERT INTO recipe_dietary (recipe_id, dietary_id) VALUES (8, 1), (8, 2), (8, 4), (8, 6), (8, 7);