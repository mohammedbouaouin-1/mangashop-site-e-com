
-- CREATE DATABASE IF NOT EXISTS mangashop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE mangashop;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS wishlist;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS bundle_products;
DROP TABLE IF EXISTS bundles;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS promo_codes;
DROP TABLE IF EXISTS newsletter;
DROP TABLE IF EXISTS devis;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS admin_activity_logs;
DROP TABLE IF EXISTS cart_items;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  icon VARCHAR(10) DEFAULT '',
  color VARCHAR(20) DEFAULT '#f1ede6',
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  category_id INT NOT NULL,
  author VARCHAR(150),
  price DECIMAL(8,2) NOT NULL,
  old_price DECIMAL(8,2),
  stock INT DEFAULT 100,
  badge VARCHAR(20),
  image_url VARCHAR(500),
  image_url2 VARCHAR(500),
  description TEXT,
  chapters TEXT,
  featured TINYINT(1) DEFAULT 0,
  is_new TINYINT(1) DEFAULT 0,
  isbn VARCHAR(30),
  pages INT DEFAULT 200,
  publisher VARCHAR(100),
  language VARCHAR(50) DEFAULT 'Français',
  rating DECIMAL(3,2) DEFAULT 4.50,
  review_count INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  INDEX idx_category (category_id),
  INDEX idx_slug (slug),
  INDEX idx_stock (stock)
);

CREATE TABLE bundles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  description TEXT,
  price DECIMAL(8,2) NOT NULL,
  old_price DECIMAL(8,2),
  image_url VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bundle_products (
  bundle_id INT,
  product_id INT,
  PRIMARY KEY (bundle_id, product_id),
  FOREIGN KEY (bundle_id) REFERENCES bundles(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);


CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(20) UNIQUE,
  customer_name VARCHAR(150),
  customer_email VARCHAR(150),
  customer_phone VARCHAR(30),
  customer_address TEXT,
  city VARCHAR(100),
  total DECIMAL(10,2),
  status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  tracking_number VARCHAR(100) DEFAULT NULL,
  livreur_id INT DEFAULT NULL,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_created (created_at),
  INDEX idx_email (customer_email),
  INDEX idx_livreur (livreur_id),
  FOREIGN KEY (livreur_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  custom_title VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  INDEX idx_order_id (order_id)
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) DEFAULT 'user',
  phone VARCHAR(30),
  address TEXT,
  city VARCHAR(100),
  reset_token VARCHAR(100) DEFAULT NULL,
  reset_expires DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(45) NOT NULL,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_time (ip, attempted_at)
);


CREATE TABLE admin_activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  admin_name VARCHAR(150) NOT NULL,
  action VARCHAR(100) NOT NULL,
  details TEXT,
  ip VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id VARCHAR(100) NOT NULL,
  quantity INT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_product (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE promo_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  discount_pct INT NOT NULL DEFAULT 10,
  max_uses INT DEFAULT 100,
  used INT DEFAULT 0,
  expires_at DATE NULL,
  active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE newsletter (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE devis (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150),
  email VARCHAR(150),
  format_type VARCHAR(50),
  cover_type VARCHAR(50),
  pages INT,
  qty INT DEFAULT 1,
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE wishlist (
  user_id INT,
  product_id INT,
  PRIMARY KEY (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT,
  customer_name VARCHAR(150),
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  UNIQUE KEY uq_user_product_review (user_id, product_id),
  INDEX idx_product (product_id)
);


INSERT INTO categories (name, slug, icon, color, description) VALUES
('Aventure',  'aventure', '',  '#fff5eb', 'Mangas d\'aventure épiques'),
('Shonen',    'shonen',   '',  '#ffe8e8', 'Pour les jeunes lecteurs'),
('Action',    'action',   '',  '#e8f0ff', 'Combats et action intense'),
('Drame',     'drame',    '',  '#e8fff2', 'Histoires émotionnelles'),
('Horreur',   'horreur',  '',  '#f0e8ff', 'Frissons garantis'),
('Seinen',    'seinen',   '',  '#fff0f5', 'Pour adultes'),
('Romance',   'romance',  '',  '#ffe0f0', 'Histoires d\'amour'),
('Fantaisie', 'fantaisie','',  '#f0fff4', 'Mondes magiques et féeriques');

INSERT INTO products (title, slug, author, category_id, price, old_price, image_url, image_url2, description, chapters, badge, featured, is_new, isbn, pages, publisher, language, rating, review_count) VALUES
('One Piece – Vol. 1', 'one-piece-vol-1', 'Eiichiro Oda', 1, 99.00, 139.00,
 'assets/images/covers/one-piece-vol-1.jpg',
 'assets/images/covers/one-piece-vol-1.jpg',
 'Monkey D. Luffy rêve de devenir le Roi des Pirates. Après avoir mangé un Fruit du Démon qui lui a donné un corps de caoutchouc, il part à la recherche du trésor légendaire One Piece avec son équipage des Chapeaux de Paille.',
 'Chapter 1 – Romance Dawn\nChapter 2 – They Call Him Straw Hat Luffy\nChapter 3 – Morgan Versus Luffy\nChapter 4 – The Great Captain Usopp\nChapter 5 – A Terrifying Sabotage',
 'best', 1, 0, '978-1-59116-455-4', 216, 'VIZ Media', 'Français', 4.9, 1247),

('Vagabond – Vol. 1', 'vagabond-vol-1', 'Takehiko Inoué', 1, 109.00, 149.00,
 'assets/images/covers/vagabond-vol-1.jpg',
 'assets/images/covers/vagabond-vol-1.jpg',
 'Shinmen Takezo est destiné à devenir le légendaire saint de l\'épée, Miyamoto Musashi. Pour l\'instant, Takezo est un tueur au sang-froid qui affrontera n\'importe qui en combat mortel pour se faire un nom. C\'est le voyage d\'un jeune brute sauvage qui s\'efforce d\'atteindre l\'illumination par la voie de l\'épée.',
 'Chapter 1 – Shinmen Takezo\nChapter 2 – Akemi\nChapter 3 – Oko\nChapter 4 – The Brigand Tsujikaze\nChapter 5 – Blood Game\nChapter 6 – The Troubles of Matahachi\nChapter 7 – Farewell Takezo\nChapter 8 – Miyamoto Village\nChapter 9 – Fiancee\nChapter 10 – Left Behind',
 'hot', 1, 0, '978-1-59116-002-0', 200, 'VIZ Media', 'Français', 4.8, 892),

('Berserk – Vol. 1', 'berserk-vol-1', 'Kentaro Miura', 1, 119.00, 159.00,
 'assets/images/covers/berserk-vol-1.jpg',
 'assets/images/covers/berserk-vol-1.jpg',
 'Guts, né du corps d\'une femme morte, est un mercenaire solitaire qui porte une gigantesque épée. Il combat des démons et des humains corrompus, hanté par un passé traumatisant. Une dark fantasy épique et violente considérée comme l\'un des plus grands chefs-d\'œuvre du manga.',
 'Chapter 1 – The Black Swordsman\nChapter 2 – The Brand\nChapter 3 – Struggle for Survival\nChapter 4 – Night of Miracles',
 'best', 1, 0, '978-1-59307-020-5', 240, 'Dark Horse', 'Français', 4.9, 2103),

('Hunter x Hunter – Vol. 1', 'hunter-x-hunter-vol-1', 'Yoshihiro Togashi', 1, 99.00, 129.00,
 'assets/images/covers/hunter-x-hunter-vol-1.jpg',
 'assets/images/covers/hunter-x-hunter-vol-1.jpg',
 'Gon Freecss, 12 ans, rêve de devenir Hunter comme son père disparu. Les Hunters sont des aventuriers d\'élite qui chassent les trésors cachés, les créatures rares et les criminels dangereux.',
 'Chapter 1 – Departure\nChapter 2 – Test Number One\nChapter 3 – First Phase Begins\nChapter 4 – The Night Before the Second Phase',
 '', 1, 0, '978-1-59116-228-4', 192, 'VIZ Media', 'Français', 4.8, 743),

('Fullmetal Alchemist – Vol. 1', 'fullmetal-alchemist-vol-1', 'Hiromu Arakawa', 1, 99.00, 129.00,
 'assets/images/covers/fullmetal-alchemist-vol-1.jpg',
 'assets/images/covers/fullmetal-alchemist-vol-1.jpg',
 'Edward et Alphonse Elric ont tenté de ressusciter leur mère grâce à l\'alchimie, mais le prix a été terrible. Maintenant alchimiste d\'État, Edward cherche la Pierre Philosophale pour rendre son corps et celui de son frère.',
 'Chapter 1 – The Two Alchemists\nChapter 2 – Body of the Sanctioned\nChapter 3 – The Mining Town\nChapter 4 – Battle on the Train',
 'best', 1, 0, '978-1-59116-920-7', 192, 'VIZ Media', 'Français', 4.9, 1856),

('Naruto – Vol. 1', 'naruto-vol-1', 'Masashi Kishimoto', 2, 89.00, 119.00,
 'assets/images/covers/naruto-vol-1.jpg',
 'assets/images/covers/naruto-vol-1.jpg',
 'Naruto Uzumaki, un jeune ninja turbulent, rêve de devenir le Hokage de son village. Portant en lui le renard à neuf queues, il devra prouver sa valeur et trouver sa place dans le monde des ninjas.',
 'Chapter 1 – Uzumaki Naruto!!\nChapter 2 – Konohamaru\nChapter 3 – Sasuke Uchiha\nChapter 4 – The Tests of the Ninja\nChapter 5 – Failure?\nChapter 6 – Not Sasuke!\nChapter 7 – Kakashi Hatake\nChapter 8 – Arrival',
 'best', 1, 0, '978-1-56931-900-0', 192, 'VIZ Media', 'Français', 4.8, 2341),

('My Hero Academia – Vol. 1', 'my-hero-academia-vol-1', 'Kohei Horikoshi', 2, 89.00, 119.00,
 'assets/images/covers/my-hero-academia-vol-1.jpg',
 'assets/images/covers/my-hero-academia-vol-1.jpg',
 'Dans un monde où 80% de la population possède des super-pouvoirs appelés "Alter", Izuku Midoriya naît sans pouvoir mais rêve de devenir le plus grand héros. Sa rencontre avec le héros numéro 1 va tout changer.',
 'Chapter 1 – Izuku Midoriya: Origin\nChapter 2 – What It Takes to Be a Hero\nChapter 3 – Roaring Muscles\nChapter 4 – Start Line\nChapter 5 – What I Can Do for Now\nChapter 6 – Rage, You Damned Nerd\nChapter 7 – Kacchan\'s Battle',
 '', 1, 0, '978-1-42158-247-6', 200, 'VIZ Media', 'Français', 4.7, 987),

('Dragon Ball – Vol. 1', 'dragon-ball-vol-1', 'Akira Toriyama', 2, 79.00, 109.00,
 'assets/images/covers/dragon-ball-vol-1.jpg',
 'assets/images/covers/dragon-ball-vol-1.jpg',
 'Un jeune garçon nommé Son Goku vit seul dans les montagnes avec une mystérieuse sphère de cristal. Lorsqu\'il rencontre Bulma, il découvre l\'existence des Dragon Balls et des dragons pouvant exaucer n\'importe quel vœu.',
 'Chapter 1 – Bulma and Son Goku\nChapter 2 – No Balls!\nChapter 3 – Sea Monkeys\nChapter 4 – Oolong the Terrible\nChapter 5 – Oolong\'s Weakness\nChapter 6 – Midnight Visitor\nChapter 7 – Yamcha the Desert Bandit',
 'best', 1, 0, '978-1-59116-076-1', 192, 'VIZ Media', 'Français', 4.9, 3102),

('Bleach – Vol. 1', 'bleach-vol-1', 'Tite Kubo', 2, 89.00, 119.00,
 'assets/images/covers/bleach-vol-1.jpg',
 'assets/images/covers/bleach-vol-1.jpg',
 'Ichigo Kurosaki peut voir les fantômes. Après avoir rencontré Rukia Kuchiki, une Shinigami (dieu de la mort), il hérite accidentellement de ses pouvoirs et doit assumer ses nouvelles responsabilités.',
 'Chapter 1 – Death and Strawberry\nChapter 2 – Starter\nChapter 3 – Head-Hittin\nChapter 4 – WHY DO YOU EAT IT?\nChapter 5 – BINDA',
 '', 0, 1, '978-1-59116-441-7', 192, 'VIZ Media', 'Français', 4.6, 1432),

('Fairy Tail – Vol. 1', 'fairy-tail-vol-1', 'Hiro Mashima', 2, 89.00, 119.00,
 'assets/images/covers/fairy-tail-vol-1.jpg',
 'assets/images/covers/fairy-tail-vol-1.jpg',
 'Lucy Heartfilia rêve de rejoindre la guilde de magie Fairy Tail. Sa rencontre avec Natsu Dragneel, un mage Dragon Slayer, va l\'emmener dans une aventure magique extraordinaire.',
 'Chapter 1 – The Fairy Tail\nChapter 2 – The Master\'s Orders\nChapter 3 – A Day for Natsu and Friends\nChapter 4 – Lucy\'s Team',
 '', 0, 1, '978-0-34550-544-4', 192, 'Kodansha', 'Français', 4.5, 876),

('Demon Slayer – Vol. 1', 'demon-slayer-vol-1', 'Koyoharu Gotouge', 3, 109.00, 149.00,
 'assets/images/covers/demon-slayer-vol-1.jpg',
 'assets/images/covers/demon-slayer-vol-1.jpg',
 'Tanjiro Kamado mène une vie paisible en montagne jusqu\'au jour où des démons massacrent sa famille. Sa sœur Nezuko est transformée en démon. Il devient chasseur de démons pour la sauver et venger ses proches.',
 'Chapter 1 – Cruelty\nChapter 2 – The Stranger\nChapter 3 – To Become a Demon Slayer\nChapter 4 – Visitor\nChapter 5 – My Own Steel\nChapter 6 – A Mountain of Hands',
 'new', 1, 1, '978-1-97477-083-7', 200, 'VIZ Media', 'Français', 4.9, 2876),

('Jujutsu Kaisen – Vol. 1', 'jujutsu-kaisen-vol-1', 'Gege Akutami', 3, 99.00, 129.00,
 'assets/images/covers/jujutsu-kaisen-vol-1.jpg',
 'assets/images/covers/jujutsu-kaisen-vol-1.jpg',
 'Yuji Itadori est un lycéen aux capacités physiques extraordinaires. Par inadvertance, il avale le doigt d\'un démon-fléau de haut rang, Ryomen Sukuna, et devient son récipient. Il entre alors dans l\'école de magie occulte de Tokyo.',
 'Chapter 1 – Ryomen Sukuna\nChapter 2 – For Myself\nChapter 3 – Girl of Steel\nChapter 4 – Curse Womb Must Die\nChapter 5 – Curse Womb Must Die II\nChapter 6 – After Rain\nChapter 7 – Assault\nChapter 8 – School of Hard Knocks',
 'hot', 1, 1, '978-1-97477-214-5', 192, 'VIZ Media', 'Français', 4.8, 1654),

('Attack on Titan – Vol. 1', 'attack-on-titan-vol-1', 'Hajime Isayama', 3, 119.00, 159.00,
 'assets/images/covers/attack-on-titan-vol-1.jpg',
 'assets/images/covers/attack-on-titan-vol-1.jpg',
 'L\'humanité vit dans des villes entourées de murs gigantesques pour se protéger des Titans, d\'énormes humanoïdes mangeurs d\'hommes. Eren Jäger jure de les exterminer tous après que les Titans ont détruit sa ville natale.',
 'Chapter 1 – To You, 2000 Years in the Future\nChapter 2 – That Day\nChapter 3 – Night of the Disbanding Ceremony\nChapter 4 – First Battle',
 '', 1, 0, '978-1-61262-024-1', 200, 'Kodansha', 'Français', 4.9, 3241),

('Black Clover – Vol. 1', 'black-clover-vol-1', 'Yûki Tabata', 3, 89.00, 119.00,
 'assets/images/covers/black-clover-vol-1.jpg',
 'assets/images/covers/black-clover-vol-1.jpg',
 'Asta est né sans magie dans un monde où tout le monde en possède. Avec son frère adoptif Yuno, il rêve de devenir le Roi Mage Suprême. Sa détermination sans faille lui permettra peut-être de défier le destin.',
 'Chapter 1 – The Boy\'s Vow\nChapter 2 – The Magic Knight Entrance Exam\nChapter 3 – The Path to the Wizard King\nChapter 4 – The Black Bulls',
 '', 0, 1, '978-1-42158-836-2', 200, 'VIZ Media', 'Français', 4.5, 678),

('Spy x Family – Vol. 1', 'spy-x-family-vol-1', 'Tatsuya Endo', 3, 99.00, 129.00,
 'assets/images/covers/spy-x-family-vol-1.jpg',
 'assets/images/covers/spy-x-family-vol-1.jpg',
 'Un espion doit se trouver une fausse famille pour sa mission. Il adopte une petite fille qui peut lire dans les pensées et épouse une tueuse à gages. Ni l\'un ni l\'autre ne connaît les secrets des autres — sauf la petite fille.',
 'Chapter 1 – Operation Strix\nChapter 2 – Secure a Wife\nChapter 3 – First Contact\nChapter 4 – The Informant\'s Action Report',
 'new', 1, 1, '978-1-97477-667-9', 200, 'VIZ Media', 'Français', 4.9, 1987),

('Tokyo Revengers – Vol. 1', 'tokyo-revengers-vol-1', 'Ken Wakui', 4, 99.00, 129.00,
 'assets/images/covers/tokyo-revengers-vol-1.jpg',
 'assets/images/covers/tokyo-revengers-vol-1.jpg',
 'Takemichi Hanagaki remonte dans le temps jusqu\'au collège pour sauver son ex-petite amie Hinata qui a été tuée par un gang. Il doit infiltrer les Tokyo Manji et changer le cours de l\'histoire.',
 'Chapter 1 – Reborn\nChapter 2 – Resist\nChapter 3 – Resolve\nChapter 4 – Return\nChapter 5 – Regret\nChapter 6 – Reveal',
 'hot', 0, 1, '978-1-64651-386-6', 208, 'Kodansha', 'Français', 4.7, 1123),

('Death Note – Vol. 1', 'death-note-vol-1', 'Tsugumi Ohba', 4, 99.00, 134.00,
 'assets/images/covers/death-note-vol-1.jpg',
 'assets/images/covers/death-note-vol-1.jpg',
 'Light Yagami, un lycéen brillant, trouve un cahier mystérieux appelé Death Note. Quiconque voit son nom écrit dans ce cahier meurt. Il décide de l\'utiliser pour créer un monde sans criminalité, mais le détective L est sur sa piste.',
 'Chapter 1 – Boredom\nChapter 2 – L\nChapter 3 – Family\nChapter 4 – Current\nChapter 5 – Eyeballs\nChapter 6 – Manipulation\nChapter 7 – Target',
 '', 0, 0, '978-1-42150-168-7', 200, 'VIZ Media', 'Français', 4.9, 2567),

('Your Lie in April – Vol. 1', 'your-lie-in-april-vol-1', 'Naoshi Arakawa', 4, 99.00, 129.00,
 'assets/images/covers/your-lie-in-april-vol-1.jpg',
 'assets/images/covers/your-lie-in-april-vol-1.jpg',
 'Kousei Arima, un jeune pianiste prodige, perd sa capacité à entendre les notes après la mort de sa mère. La rencontre avec Kaori Miyazono, une violoniste extravertie, va changer sa vie pour toujours.',
 'Chapter 1 – The Monotone/Coloring\nChapter 2 – Friend A\nChapter 3 – The Performance\nChapter 4 – Journey',
 'new', 0, 1, '978-1-63236-064-0', 192, 'Kodansha', 'Français', 4.8, 867),

('Slam Dunk – Vol. 1', 'slam-dunk-vol-1', 'Takehiko Inoué', 4, 89.00, 119.00,
 'assets/images/covers/slam-dunk-vol-1.jpg',
 'assets/images/covers/slam-dunk-vol-1.jpg',
 'Hanamichi Sakuragi, délinquant juvénile rejeté 50 fois par des filles, découvre le basketball grâce à Haruko Akagi. Malgré son tempérament impulsif, son talent naturel pourrait le mener loin.',
 'Chapter 1 – I Am Sakuragi\nChapter 2 – The Beginning\nChapter 3 – Basketball\nChapter 4 – Practice',
 'best', 0, 0, '978-1-59116-233-8', 200, 'VIZ Media', 'Français', 4.8, 1432),

('Chainsaw Man – Vol. 1', 'chainsaw-man-vol-1', 'Tatsuki Fujimoto', 5, 109.00, 139.00,
 'assets/images/covers/chainsaw-man-vol-1.jpg',
 'assets/images/covers/chainsaw-man-vol-1.jpg',
 'Denji est un jeune homme pauvre qui chasse les démons avec son chien-démon Pochita. Trahi et tué, il fusionne avec Pochita pour devenir Chainsaw Man, un hybride démon-humain aux pouvoirs de tronçonneuse devastateurs.',
 'Chapter 1 – Dog & Chainsaw\nChapter 2 – The Place Where Pochita Is\nChapter 3 – Arrival in Tokyo\nChapter 4 – Power\nChapter 5 – A Way to Touch Some Boobs\nChapter 6 – Service',
 'new', 0, 1, '978-1-97477-417-0', 192, 'VIZ Media', 'Français', 4.8, 1765),

('Junji Ito – Uzumaki Vol. 1', 'uzumaki-vol-1', 'Junji Ito', 5, 129.00, 169.00,
 'assets/images/covers/uzumaki-vol-1.jpg',
 'assets/images/covers/uzumaki-vol-1.jpg',
 'La ville de Kurouzu-cho est hantée par une malédiction liée aux spirales. Kirie Goshima et son petit ami Shuichi Saito assistent à la lente descente de leur ville dans la folie causée par l\'obsession morbide des spirales.',
 'Chapter 1 – The Spiral Obsession Part 1\nChapter 2 – The Spiral Obsession Part 2\nChapter 3 – The Scar\nChapter 4 – The Snail\nChapter 5 – Twisted Souls',
 'hot', 0, 0, '978-1-59116-675-6', 216, 'VIZ Media', 'Français', 4.7, 892),

('Tokyo Ghoul – Vol. 1', 'tokyo-ghoul-vol-1', 'Sui Ishida', 5, 109.00, 139.00,
 'assets/images/covers/tokyo-ghoul-vol-1.jpg',
 'assets/images/covers/tokyo-ghoul-vol-1.jpg',
 'Ken Kaneki, un lycéen ordinaire, survit à une attaque de goule mais se retrouve à mi-chemin entre humain et goule. Il doit apprendre à naviguer dans le monde dangereux des goules tout en cachant sa vraie nature.',
 'Chapter 1 – Tragedy\nChapter 2 – Urge\nChapter 3 – Dove\nChapter 4 – Supper\nChapter 5 – Scars',
 '', 0, 0, '978-1-42158-542-2', 200, 'VIZ Media', 'Français', 4.7, 1234),

('Vinland Saga – Vol. 1', 'vinland-saga-vol-1', 'Makoto Yukimura', 6, 139.00, 179.00,
 'assets/images/covers/vinland-saga-vol-1.jpg',
 'assets/images/covers/vinland-saga-vol-1.jpg',
 'Dans la Scandinavie médiévale, le jeune Thorfinn grandit au sein d\'une bande de mercenaires vikings dirigée par le tueur de son père. Il rêve de venger sa mort et de trouver le légendaire Vinland, une terre de paix.',
 'Chapter 1 – Somewhere Not Here\nChapter 2 – Normans\nChapter 3 – Troll\nChapter 4 – For the Sword\nChapter 5 – Messenger from the Norse Lands',
 'best', 1, 0, '978-1-61262-224-5', 464, 'Kodansha', 'Français', 4.9, 1543),

('Berserk Deluxe – Vol. 1', 'berserk-deluxe-vol-1', 'Kentaro Miura', 6, 299.00, 399.00,
 'assets/images/covers/berserk-deluxe-vol-1.jpg',
 'assets/images/covers/berserk-deluxe-vol-1.jpg',
 'Édition Deluxe de Berserk — réunit les volumes 1, 2 et 3 dans un grand format luxueux avec couverture rigide. La dark fantasy de référence absolue enfin disponible dans une édition collector premium.',
 'Volumes 1, 2 et 3 complets\nBlack Swordsman arc\nConviction arc (début)',
 'sale', 0, 1, '978-1-50671-242-1', 672, 'Dark Horse', 'Français', 4.9, 567),

('Oyasumi Punpun – Vol. 1', 'oyasumi-punpun-vol-1', 'Inio Asano', 6, 129.00, 159.00,
 'assets/images/covers/oyasumi-punpun-vol-1.jpg',
 'assets/images/covers/oyasumi-punpun-vol-1.jpg',
 'Punpun Punyama est un garçon ordinaire qui grandit dans une famille dysfonctionnelle. Asano décrit l\'adolescence avec une honnêteté crue et souvent bouleversante, explorant les thèmes de l\'amour, de la dépression et de la croissance.',
 'Chapter 1 – Punpun\nChapter 2 – Home\nChapter 3 – School\nChapter 4 – Aiko\nChapter 5 – Summer',
 '', 0, 0, '978-1-42158-629-0', 200, 'VIZ Media', 'Français', 4.8, 789),

('Gantz – Vol. 1', 'gantz-vol-1', 'Hiroya Oku', 6, 119.00, 149.00,
 'assets/images/covers/gantz-vol-1.jpg',
 'assets/images/covers/gantz-vol-1.jpg',
 'Kei Kurono et son ami d\'enfance Masaru Kato meurent dans un accident de métro, mais se réveillent dans un appartement avec une mystérieuse sphère noire. Ils sont contraints de participer à des jeux de chasse mortels.',
 'Chapter 1 – A New Morning\nChapter 2 – The Black Sphere\nChapter 3 – Onion Alien\nChapter 4 – The Mission',
 '', 0, 0, '978-1-59307-949-9', 240, 'Dark Horse', 'Français', 4.5, 432),

('Fruits Basket – Vol. 1', 'fruits-basket-vol-1', 'Natsuki Takaya', 7, 89.00, 119.00,
 'assets/images/covers/fruits-basket-vol-1.jpg',
 'assets/images/covers/fruits-basket-vol-1.jpg',
 'Tohru Honda, une lycéenne orpheline, découvre que les membres de la famille Sohma sont maudits et se transforment en animaux du zodiaque chinois lorsqu\'ils sont étreints par une personne du sexe opposé.',
 'Chapter 1 – The Cinderella Complex\nChapter 2 – The Cinderella Complex Part 2\nChapter 3 – The Cinderella Complex Part 3\nChapter 4 – The Cinderella Complex Part 4',
 'new', 0, 1, '978-1-59182-603-5', 216, 'Yen Press', 'Français', 4.6, 654),

('Toradora! – Vol. 1', 'toradora-vol-1', 'Yuyuko Takemiya', 7, 99.00, 129.00,
 'assets/images/covers/toradora-vol-1.jpg',
 'assets/images/covers/toradora-vol-1.jpg',
 'Ryuji Takasu et Taiga Aisaka semblent être des opposés totaux — lui est doux malgré sa tête de délinquant, elle est agressive malgré sa taille de poupée. Pourtant ils vont devoir s\'entraider pour conquérir leurs amours respectifs.',
 'Chapter 1 – Ryuji and Taiga\nChapter 2 – Kushieda\nChapter 3 – The Tiger and the Dragon\nChapter 4 – Advance',
 '', 0, 0, '978-0-31610-154-2', 192, 'Seven Seas', 'Français', 4.5, 432),

('Kimi ni Todoke – Vol. 1', 'kimi-ni-todoke-vol-1', 'Karuho Shiina', 7, 89.00, 119.00,
 'assets/images/covers/kimi-ni-todoke-vol-1.jpg',
 'assets/images/covers/kimi-ni-todoke-vol-1.jpg',
 'Sawako Kuronuma est une lycéenne timide que ses camarades évitent à cause de sa ressemblance avec Sadako de Ring. Kazehaya, le garçon le plus populaire de l\'école, décide de lui parler et de tout changer.',
 'Chapter 1 – Prologue\nChapter 2 – Sawako\nChapter 3 – Kazehaya-kun\nChapter 4 – Friends',
 '', 0, 0, '978-1-42158-100-4', 192, 'VIZ Media', 'Français', 4.6, 543),

('Sword Art Online – Vol. 1', 'sword-art-online-vol-1', 'Reki Kawahara', 8, 109.00, 139.00,
 'assets/images/covers/sword-art-online-vol-1.jpg',
 'assets/images/covers/sword-art-online-vol-1.jpg',
 'En 2022, des milliers de joueurs se retrouvent piégés dans Sword Art Online, un MMORPG en réalité virtuelle. La mort dans le jeu signifie la mort réelle. Kirito, un joueur solitaire, devra survivre jusqu\'au boss final.',
 'Chapter 1 – Aincrad\nChapter 2 – Beater\nChapter 3 – Red Nosed Reindeer\nChapter 4 – The Black Swordsman',
 '', 0, 1, '978-0-31630-254-9', 176, 'Yen Press', 'Français', 4.5, 876),

('Re:Zero – Vol. 1', 'rezero-vol-1', 'Tappei Nagatsuki', 8, 99.00, 129.00,
 'assets/images/covers/re-zero-vol-1.jpg',
 'assets/images/covers/re-zero-vol-1.jpg',
 'Subaru Natsuki est transporté dans un autre monde. Il découvre rapidement qu\'il possède le pouvoir "Retour par la Mort" — quand il meurt, il revient à un point fixe dans le temps, gardant tous ses souvenirs.',
 'Chapter 1 – The Witching Hour\nChapter 2 – Natsuki Subaru\'s Restart\nChapter 3 – A Redo from Zero\nChapter 4 – A Dark Darkness',
 'new', 0, 1, '978-0-31641-704-2', 160, 'Yen Press', 'Français', 4.7, 765),

('Made in Abyss – Vol. 1', 'made-in-abyss-vol-1', 'Akihito Tsukushi', 8, 109.00, 139.00,
 'assets/images/covers/made-in-abyss-vol-1.jpg',
 'assets/images/covers/made-in-abyss-vol-1.jpg',
 'Riko vit dans l\'orphelinat de Belchero, à l\'entrée d\'un gouffre mystérieux appelé l\'Abîme. Elle rêve de descendre dans l\'Abîme comme sa mère disparue, grande exploratrice. Sa rencontre avec un robot humanoïde va tout changer.',
 'Chapter 1 – The City at the Edge of the Abyss\nChapter 2 – The Caves of the Bowels\nChapter 3 – Raid\nChapter 4 – The Law of the Abyss',
 'hot', 1, 0, '978-1-62692-545-5', 192, 'Seven Seas', 'Français', 4.8, 654);

INSERT INTO bundles (name, slug, description, price, old_price, image_url) VALUES
('Pack Shonen Essentiel – 4 Vol.',
 'pack-shonen-essentiel',
 'One Piece Vol.1 + Naruto Vol.1 + Dragon Ball Vol.1 + My Hero Academia Vol.1 — Les 4 piliers du shonen en un seul pack.',
 299.00, 456.00,
 'assets/images/covers/bundle-pack-shonen-essentiel.jpg'),

('Pack Action Ultime – 3 Vol.',
 'pack-action-ultime',
 'Demon Slayer Vol.1 + Jujutsu Kaisen Vol.1 + Attack on Titan Vol.1 — Les 3 titans de l\'action moderne.',
 289.00, 377.00,
 'assets/images/covers/bundle-pack-action-ultime.jpg'),

('Pack Horreur & Dark Fantasy – 3 Vol.',
 'pack-horreur-dark',
 'Chainsaw Man Vol.1 + Uzumaki Vol.1 + Berserk Vol.1 — Pour les amateurs de frissons garantis.',
 329.00, 517.00,
 'assets/images/covers/bundle-pack-horreur-dark.jpg'),

('Pack Découverte Manga – 5 Vol.',
 'pack-decouverte',
 'One Piece + Naruto + Demon Slayer + Death Note + Fruits Basket — Parfait pour commencer sa collection.',
 429.00, 585.00,
 'assets/images/covers/bundle-pack-decouverte.jpg'),

('Pack Seinen Premium – 2 Vol.',
 'pack-seinen-premium',
 'Vagabond Vol.1 + Vinland Saga Vol.1 — Les deux chefs-d\'œuvre absolus du manga seinen.',
 229.00, 328.00,
 'assets/images/covers/bundle-pack-seinen-premium.jpg'),

('Pack Spy & Hero – 2 Vol.',
 'pack-spy-hero',
 'Spy x Family Vol.1 + My Hero Academia Vol.1 — Aventure et humour garantis.',
 179.00, 248.00,
 'assets/images/covers/bundle-pack-spy-hero.jpg'),

('Pack Légendaire – 7 Vol.',
 'pack-legendaire',
 'One Piece + Naruto + Dragon Ball + Berserk + Attack on Titan + Death Note + Fullmetal Alchemist — La collection ultime.',
 699.00, 916.00,
 'assets/images/covers/bundle-pack-legendaire.jpg');

INSERT INTO bundle_products VALUES
(1,1),(1,6),(1,8),(1,7),
(2,11),(2,12),(2,13),
(3,19),(3,20),(3,3),
(4,1),(4,6),(4,11),(4,16),(4,25),
(5,2),(5,22),
(6,15),(6,7),
(7,1),(7,6),(7,8),(7,3),(7,13),(7,16),(7,5);

INSERT IGNORE INTO promo_codes (code, discount_pct, max_uses) VALUES
('MANGA10', 10, 500),
('WELCOME15', 15, 200),
('SPRING20', 20, 100),
('BOD25', 25, 50);

INSERT IGNORE INTO users (name, email, password, role) VALUES
('Administrateur', 'admin@mangashop.ma', '$2y$10$isaCzuXHQGb7uZ18JZwpRuxbRU.Zn/bwFt84AK0x8V.vaMr3A8EUu', 'admin');
