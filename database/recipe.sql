drop database IF EXISTS recipe_app;
CREATE DATABASE recipe_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

drop user if EXISTS 'recipe-app'@'localhost';
CREATE USER 'recipe-app'@'localhost' IDENTIFIED BY 'AppPass2025!';
GRANT ALTER,CREATE,DELETE,DROP,INDEX,INSERT,REFERENCES,SELECT,UPDATE,CREATE VIEW,SHOW VIEW ON recipe_app.* TO 'recipe-app'@'localhost';

USE recipe_app;

-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

-- INGREDIENTS
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Recipes
CREATE TABLE recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  image VARCHAR(255),
  prep_time VARCHAR(50) NOT NULL,   
  cook_time VARCHAR(50) NOT NULL,
  servings VARCHAR(50),
  author_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Recipe ⇄ Category (Many-to-Many)
CREATE TABLE recipe_categories (
  recipe_id INT,
  category_id INT,
  PRIMARY KEY(recipe_id, category_id),
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

-- Steps
CREATE TABLE steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT,
  step_number INT,
  instruction TEXT,
  duration INT,    -- minutes
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- Favourites
CREATE TABLE favourites (
  user_id INT,
  recipe_id INT,
  PRIMARY KEY(user_id, recipe_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- Ratings
CREATE TABLE ratings (
  user_id INT,
  recipe_id INT,
  difficulty_score TINYINT NOT NULL,   -- 1–5
  aesthetics_score TINYINT NOT NULL,  -- 1–5
  taste_score TINYINT NOT NULL,  -- 1–5
  PRIMARY KEY(user_id,recipe_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  CONSTRAINT raintgs_taste_score_chk CHECK (taste_score BETWEEN 1 AND 5),
  CONSTRAINT raintgs_difficulty_score_chk CHECK (difficulty_score BETWEEN 1 AND 5),
  CONSTRAINT raintgs_aesthetics_score_chk CHECK (aesthetics_score BETWEEN 1 AND 5)
);

-- Populate USERS
INSERT INTO users (id,name,email,password_hash, created_at) VALUES 
(1, 'Jo Pratt', 'user1@demo.com', SHA2('password',256), now()),
(2, 'Justine Pattison', 'user2@demo.com', SHA2('password',256), now()),
(3, 'Sunil Vijayakar', 'user3@demo.com', SHA2('password',256), now()),
(4, 'Nargisse Benkabbou', 'user4@demo.com', SHA2('password',256), now()),
(5, 'James Martin', 'user5@demo.com', SHA2('password',256), now()),
(6, 'Samin Nosrat', 'user6@demo.com', SHA2('password',256), now()),
(7, 'Sabrina Ghayour', 'user7@demo.com', SHA2('password',256), now());

-- Populate Categories
INSERT INTO categories (name) VALUES 
  ('Main'),('Meat'),('Vegetarian'),('Dessert'),('Vegan'), ('Salad'), ('Breakfast');

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

-- Sample Recipes 
INSERT INTO recipes (id, name, image, author_id, prep_time, cook_time, servings, created_at) VALUES
(1, 'Spaghetti Bolognese', 'img/spaghettibolognese_67868_16x9.jpg', 1, 'less than 30 mins', '1 to 2 hours', 'Serves 6-8', now()),
(2, 'Vegan Pancakes', 'img/vegan_american_pancakes_76094_16x9.jpg', 2, 'less than 30 mins', '10 to 30 mins', 'Serves 2', now()),
(3, 'Healthy Pizza', 'img//healthy_pizza_55143_16x9.jpg', 2, 'less than 30 mins', '10 to 30 mins', 'Serves 2', now()),
(4, 'Easy Lamb Biryani', 'img/easy_lamb_biryani_46729_16x9.jpg', 3, 'overnight', '1 to 2 hours', 'Serves 6-8', now()),
(5, 'Couscous Salad', 'img/dried_fruits_and_nuts_18053_16x9.jpg', 4, 'less than 30 mins', 'less than 10 mins', 'Serves 6', now()),
(6, 'Plum Clafoutis', 'img/plumclafoutis_11536_16x9.jpg', 5, 'less than 30 mins', '30 mins to 1 hour', 'Serves 4-6', now()),
(7, 'Mango Pie', 'img/mango_pie_18053_16x9.jpg', 6, '30 mins to 1 hour', '30 mins to 1 hour', 'Serves 16', now()),
(8, 'Mushroom Doner', 'img/mushroom_doner_22676_16x9.jpg', 7, 'less than 30 mins', '10 to 30 mins', 'Serves 4', now());


-- RECIPE_CATEGORIES
INSERT INTO recipe_categories (recipe_id, category_id) VALUES
(1, 1), (1, 2),        -- Spaghetti Bolognese: Main (1), Meat (2)
(2, 3), (2, 5), (2, 7),-- Vegan Pancakes: Vegetarian (3), Vegan (5), Breakfast (7)
(3, 1), (3, 3),        -- Healthy Pizza: Main (1), Vegetarian (3)
(4, 1), (4, 2),        -- Easy Lamb Biryani: Main (1), Meat (2)
(5, 6), (5, 3),        -- Couscous Salad: Salad (6), Vegetarian (3)
(6, 4),                -- Plum Clafoutis: Dessert (4)
(7, 4),                -- Mango Pie: Dessert (4)
(8, 1), (8, 3);        -- Mushroom Doner: Main (1), Vegetarian (3)



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

-- Steps
-- Spaghetti Bolognese (recipe_id = 1)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(1, 1, 'Heat the oil in a large, heavy-based saucepan and fry the bacon until golden over a medium heat. Add the onions and garlic, frying until softened.', 10),
(1, 2, 'Increase the heat and add the minced beef. Fry it until it has browned, breaking down any chunks of meat with a wooden spoon.', 10),
(1, 3, 'Pour in the wine and boil until it has reduced in volume by about a third.', 10),
(1, 4, 'Reduce the temperature and stir in the tomatoes, drained mushrooms, bay leaves, oregano, thyme and balsamic vinegar.', 5),
(1, 5, 'Blitz or chop the sun-dried tomatoes and add to the pan. Season well with salt and pepper. Cover and simmer for 1-1½ hours until rich and thickened, stirring occasionally.', 75),
(1, 6, 'Stir in the basil and add any extra seasoning if necessary.', 2),
(1, 7, 'Remove from the heat to settle while you cook the spaghetti. Cook the spaghetti in boiling salted water as per packet instructions.', 10),
(1, 8, 'Drain and divide between plates. Scatter parmesan over the spaghetti, add Bolognese sauce, and finish with more cheese and black pepper.', 3);

-- Vegan Pancakes (recipe_id = 2)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(2, 1, 'Mix the flour, baking powder, sugar and a pinch of salt in a bowl.', 2),
(2, 2, 'Whisk the soya milk, vanilla extract, oil and lemon juice together in a jug.', 2),
(2, 3, 'Pour the wet ingredients into the dry and whisk to a smooth batter.', 2),
(2, 4, 'Heat a non-stick frying pan and add a little oil. Pour in some batter and cook for 2-3 minutes until bubbles form.', 3),
(2, 5, 'Flip and cook for another 2 minutes until golden. Repeat with remaining batter.', 10),
(2, 6, 'Serve with maple syrup and fruit.', 1);

-- Healthy Pizza (recipe_id = 3)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(3, 1, 'Mix the flours, yeast and salt in a bowl. Add water and oil to form a dough.', 5),
(3, 2, 'Knead the dough for 5 minutes, then cover and leave to rise for 30 minutes.', 30),
(3, 3, 'Roll out the dough, spread with tomato puree and chopped tomatoes.', 5),
(3, 4, 'Top with mozzarella, tomato slices and bake at 220C for 10 minutes.', 10),
(3, 5, 'Serve with yogurt.', 2);

-- Easy Lamb Biryani (recipe_id = 4)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(4, 1, 'Fry onions and garlic in oil until golden.', 5),
(4, 2, 'Add lamb and brown all over.', 10),
(4, 3, 'Add spices and cook for 2 minutes.', 2),
(4, 4, 'Add rice and water, bring to boil, then simmer until rice is cooked.', 30),
(4, 5, 'Season and serve.', 3);

-- Couscous Salad (recipe_id = 5)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(5, 1, 'Prepare couscous according to packet instructions.', 5),
(5, 2, 'Chop dried fruit, nuts, tomato and onion.', 5),
(5, 3, 'Mix all ingredients together and season.', 5),
(5, 4, 'Serve chilled or at room temperature.', 10);

-- Plum Clafoutis (recipe_id = 6)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(6, 1, 'Preheat oven to 180C. Grease a baking dish.', 5),
(6, 2, 'Halve and stone the plums, arrange in the dish.', 5),
(6, 3, 'Mix flour, sugar and salt. Whisk in eggs, milk and oil to make a batter.', 5),
(6, 4, 'Pour batter over plums.', 2),
(6, 5, 'Bake for 35 minutes until golden and set.', 35),
(6, 6, 'Cool slightly before serving.', 5);

-- Mango Pie (recipe_id = 7)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(7, 1, 'Preheat oven to 180C. Grease a pie dish.', 5),
(7, 2, 'Peel and slice mangoes.', 5),
(7, 3, 'Mix flour, sugar and salt. Whisk in eggs and oil to make a dough.', 5),
(7, 4, 'Press dough into dish, arrange mango slices on top.', 5),
(7, 5, 'Bake for 25 minutes until golden.', 25),
(7, 6, 'Cool before serving.', 5);

-- Mushroom Doner (recipe_id = 8)
INSERT INTO steps (recipe_id, step_number, instruction, duration) VALUES
(8, 1, 'Slice mushrooms and fry in oil until golden.', 5),
(8, 2, 'Warm pita breads.', 2),
(8, 3, 'Fill pitas with mushrooms, lettuce, cucumber, tomato and yogurt.', 5),
(8, 4, 'Season and serve.', 2);

commit;
