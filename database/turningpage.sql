-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 05:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `turningpage`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `bio`) VALUES
(1, 'J.K. Rowling', 'J.K. Rowling is a British novelist and philanthropist best known for creating the Harry Potter series, a global phenomenon that redefined young adult fantasy literature. Her work has inspired films, merchandise, theme parks, and an entire generation of readers. Before her success, Rowling wrote the first book while struggling financially, and her story has become a symbol of perseverance.'),
(2, 'George R.R. Martin', 'George R.R. Martin is an American novelist and screenwriter acclaimed for his epic fantasy series, A Song of Ice and Fire. His richly layered world-building, political intrigue, and morally complex characters transformed modern fantasy. The television adaptation, Game of Thrones, elevated his work to international prominence and reshaped fantasy storytelling in media.'),
(3, 'Agatha Christie', 'Agatha Christie was an English writer who earned the title “Queen of Mystery” for her more than 60 detective novels and iconic characters such as Hercule Poirot and Miss Marple. With her clever plotting and surprising twists, Christie became the best-selling fiction author of all time, with her works translated into over 100 languages. Very Good.'),
(4, 'Stephen King', 'Stephen King is an American author of horror, supernatural fiction, thrillers, and fantasy. Known for his vivid characters and atmospheric tension, King has written more than 60 novels, many adapted into films and TV series. His works such as The Shining, IT, and Misery have left a lasting impact on modern horror.'),
(5, 'Haruki Murakami', 'Haruki Murakami is a Japanese novelist recognized for blending magical realism with psychological depth. His works often explore themes of loneliness, identity, and surreal alternate realities. Novels such as Kafka on the Shore and Norwegian Wood have made him one of the most influential contemporary writers globally.'),
(6, 'Neil Gaiman', 'Neil Gaiman is an English author of novels, comics, and screenplays, celebrated for his imaginative storytelling and mythological themes. His works, including American Gods, Coraline, and The Sandman, demonstrate a mastery of dark fantasy and folklore. Gaiman\'s influence extends to film and television adaptations of his stories.'),
(7, 'Brandon Sanderson', 'Brandon Sanderson is a prolific American fantasy writer known for his intricate magic systems and expansive interconnected universe known as the Cosmere. He completed Robert Jordan\'s The Wheel of Time series and created acclaimed sagas such as Mistborn and The Stormlight Archive, earning a devoted global readership.'),
(8, 'Dan Brown', 'Dan Brown is an American author whose fast-paced thrillers combine history, symbology, and conspiracy. His novel The Da Vinci Code became a worldwide bestseller and sparked widespread cultural discussion. Brown\'s works typically feature puzzles, codes, and secret societies woven into modern mysteries.'),
(9, 'Paulo Coelho', 'Paulo Coelho is a Brazilian novelist best known for The Alchemist, a philosophical fable that has touched millions of readers. His writing focuses on spirituality, personal discovery, and the pursuit of dreams. Coelho\'s works have been translated into dozens of languages and remain globally influential.'),
(10, 'Rick Riordan', 'Rick Riordan is an American author famous for creating the Percy Jackson & the Olympians series, which introduced young readers to Greek mythology through humorous and fast-paced adventure. He later expanded into Roman, Egyptian, and Norse myth-based series, becoming a central figure in modern middle-grade literature.'),
(11, 'James Patterson', 'James Patterson is an American author known for his prolific output and bestselling thrillers. With series such as Alex Cross and Women’s Murder Club, Patterson has dominated commercial fiction for decades. He is also known for his collaborations, youth novels, and philanthropic support for reading programs.'),
(12, 'Jane Austen', 'Jane Austen was an English novelist whose sharp wit and keen observations of society shaped her enduring works, including Pride and Prejudice and Emma. Her novels explore class, marriage, and morality with humor and insight, securing her legacy as one of literature’s most beloved writers.'),
(13, 'Leo Tolstoy', 'Leo Tolstoy was a Russian novelist and philosopher widely regarded as one of the greatest authors of all time. His masterpieces War and Peace and Anna Karenina explore history, morality, and human nature with unparalleled depth. Later in life, Tolstoy became a moral thinker who inspired global social movements.'),
(14, 'Fyodor Dostoevsky', 'Fyodor Dostoevsky was a Russian author known for exploring psychology, philosophy, and the complexities of the human condition. His works such as Crime and Punishment and The Brothers Karamazov examine guilt, morality, and existential struggle, shaping modern literature and thought.'),
(15, 'Mark Twain', 'Mark Twain, born Samuel Clemens, was an American writer celebrated for his humor, satire, and vivid depictions of 19th-century America. His novels The Adventures of Tom Sawyer and Adventures of Huckleberry Finn are considered foundational works of American literature.'),
(16, 'Arthur Conan Doyle', 'Sir Arthur Conan Doyle was a British writer and physician best known for creating Sherlock Holmes, one of the most iconic fictional detectives. His works helped define the detective genre, and his influence continues to shape mystery storytelling today.'),
(17, 'Oscar Wilde', 'Oscar Wilde was an Irish poet, playwright, and novelist famed for his sharp wit and flamboyant personality. His works, including The Picture of Dorian Gray and The Importance of Being Earnest, remain celebrated for their humor, satire, and social insight.'),
(18, 'Ernest Hemingway', 'Ernest Hemingway was an American novelist known for his concise writing style and themes of courage, war, and human endurance. Works like The Old Man and the Sea and A Farewell to Arms cemented his reputation as one of the most influential writers of the 20th century.'),
(19, 'Jules Verne', 'Jules Verne was a French novelist and pioneer of science fiction whose visionary adventure stories, including Twenty Thousand Leagues Under the Sea and Journey to the Center of the Earth, anticipated modern technology long before it existed.'),
(20, 'H.G. Wells', 'H.G. Wells was an English writer often called the “father of science fiction,” known for classics such as The War of the Worlds, The Time Machine, and The Invisible Man. His works combined scientific curiosity with social commentary, shaping the foundations of modern speculative fiction.');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `genre` enum('Fiction','Non-Fiction','None','') DEFAULT NULL,
  `set_price` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `condition` enum('New','Used','Collectible') DEFAULT 'Used',
  `stock` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author_id`, `genre`, `set_price`, `price`, `description`, `image`, `condition`, `stock`, `created_at`) VALUES
(26, 'Harry Potter and the Sorcerer\'s Stone', 1, 'Fiction', 500.00, 350.00, 'First book in the Harry Potter series.', 'hp1.jpg', 'Used', 10, '2025-01-01 10:00:00'),
(27, 'Harry Potter and the Chamber of Secrets', 1, 'Fiction', 520.00, 360.00, 'Second book in the Harry Potter series.', 'hp2.jpg', 'Used', 10, '2025-01-02 10:00:00'),
(28, 'Harry Potter and the Prisoner of Azkaban', 1, 'Fiction', 540.00, 370.00, 'Third book in the Harry Potter series.', 'hp3.jpg', 'Used', 8, '2025-01-03 10:00:00'),
(29, 'Harry Potter and the Goblet of Fire', 1, 'Fiction', 560.00, 380.00, 'Fourth book in the Harry Potter series.', 'hp4.jpg', 'Used', 7, '2025-01-04 10:00:00'),
(30, 'Harry Potter and the Order of the Phoenix', 1, 'Fiction', 580.00, 390.00, 'Fifth book in the Harry Potter series.', 'hp5.jpg', 'Used', 9, '2025-01-05 10:00:00'),
(31, 'Harry Potter and the Half-Blood Prince', 1, 'Fiction', 600.00, 400.00, 'Sixth book in the Harry Potter series.', 'hp6.jpg', 'Used', 10, '2025-01-06 10:00:00'),
(32, 'Harry Potter and the Deathly Hallows', 1, 'Fiction', 620.00, 420.00, 'Seventh and final book in the Harry Potter series.', 'hp7.jpg', 'Used', 8, '2025-01-07 10:00:00'),
(33, 'Fantastic Beasts and Where to Find Them', 1, 'Non-Fiction', 480.00, 330.00, 'Magical creatures companion book.', 'fb1.jpg', 'New', 6, '2025-01-08 10:00:00'),
(34, 'Quidditch Through the Ages', 1, 'Non-Fiction', 470.00, 320.00, 'Guide to the sport of Quidditch.', 'quidditch.jpg', 'New', 5, '2025-01-09 10:00:00'),
(35, 'The Tales of Beedle the Bard', 1, 'Fiction', 450.00, 300.00, 'Collection of wizarding fairy tales.', 'beedle.jpg', 'New', 8, '2025-01-10 10:00:00'),
(36, 'A Game of Thrones', 2, 'Fiction', 600.00, 450.00, 'First book in the A Song of Ice and Fire series.', 'got1.jpg', 'Used', 7, '2025-01-11 10:00:00'),
(37, 'A Clash of Kings', 2, 'Fiction', 620.00, 470.00, 'Second book in the series.', 'got2.jpg', 'Used', 7, '2025-01-12 10:00:00'),
(38, 'A Storm of Swords', 2, 'Fiction', 640.00, 490.00, 'Third book in the series.', 'got3.jpg', 'Used', 8, '2025-01-13 10:00:00'),
(39, 'A Feast for Crows', 2, 'Fiction', 660.00, 510.00, 'Fourth book in the series.', 'got4.jpg', 'Used', 6, '2025-01-14 10:00:00'),
(40, 'A Dance with Dragons', 2, 'Fiction', 680.00, 530.00, 'Fifth book in the series.', 'got5.jpg', 'Used', 6, '2025-01-15 10:00:00'),
(41, 'The Winds of Winter', 2, 'Fiction', 700.00, 550.00, 'Upcoming sixth book.', '691e6ed4de89a_default.png', 'New', 5, '2025-01-16 10:00:00'),
(42, 'Dreamsongs Volume I', 2, 'Fiction', 500.00, 350.00, 'Collection of short stories.', 'dream1.jpg', 'Used', 5, '2025-01-17 10:00:00'),
(43, 'Dreamsongs Volume II', 2, 'Fiction', 520.00, 370.00, 'Collection of short stories.', 'dream2.jpg', 'Used', 5, '2025-01-18 10:00:00'),
(44, 'The Armageddon Rag', 2, 'Fiction', 480.00, 320.00, 'Novel exploring mystery and music.', 'armageddon.jpg', 'Used', 4, '2025-01-19 10:00:00'),
(45, 'Fevre Dream', 2, 'Fiction', 500.00, 340.00, 'Historical vampire novel.', 'fevre.jpg', 'Used', 6, '2025-01-20 10:00:00'),
(46, 'Murder on the Orient Express', 3, 'Fiction', 300.00, 200.00, 'Hercule Poirot investigates a murder on a train.', 'orient1.jpg', 'Collectible', 3, '2025-01-21 10:00:00'),
(47, 'Death on the Nile', 3, 'Fiction', 320.00, 220.00, 'Poirot solves a murder on a cruise.', '691e6edeabef8_default.png', 'Collectible', 2, '2025-01-22 10:00:00'),
(48, 'And Then There Were None', 3, 'Fiction', 350.00, 250.00, 'Ten strangers are killed one by one on an isolated island.', 'attwn.jpg', 'Used', 4, '2025-01-23 10:00:00'),
(49, 'The Murder of Roger Ackroyd', 3, 'Fiction', 300.00, 210.00, 'Poirot investigates a murder in a village.', 'ackroyd.jpg', 'Used', 5, '2025-01-24 10:00:00'),
(50, 'The A.B.C. Murders', 3, 'Fiction', 310.00, 220.00, 'Poirot tackles a serial killer using the alphabet.', 'abc.jpg', 'Used', 4, '2025-01-25 10:00:00'),
(51, 'The Mysterious Affair at Styles', 3, 'Fiction', 290.00, 200.00, 'Christie\'s first Poirot novel.', 'styles.jpg', 'Used', 6, '2025-01-26 10:00:00'),
(52, 'Peril at End House', 3, 'Fiction', 300.00, 210.00, 'Poirot investigates near the seaside.', 'endhouse.jpg', 'Used', 5, '2025-01-27 10:00:00'),
(53, 'The Secret Adversary', 3, 'Fiction', 280.00, 190.00, 'Tommy and Tuppence adventure novel.', 'secret.jpg', 'Used', 6, '2025-01-28 10:00:00'),
(54, 'Five Little Pigs', 3, 'Fiction', 310.00, 220.00, 'Poirot investigates a murder from 16 years ago.', 'fivepigs.jpg', 'Used', 3, '2025-01-29 10:00:00'),
(55, 'Hallowe\'en Party', 3, 'Fiction', 300.00, 210.00, 'Poirot investigates a murder at a Halloween party.', 'halloween.jpg', 'Used', 4, '2025-01-30 10:00:00'),
(56, 'Carrie', 4, 'Fiction', 400.00, 250.00, 'Stephen King\'s first published novel about a telekinetic girl.', 'carrie.jpg', 'Used', 7, '2025-02-01 10:00:00'),
(57, 'Salem\'s Lot', 4, 'Fiction', 420.00, 270.00, 'A writer returns to a town infested with vampires.', 'salems.jpg', 'Used', 6, '2025-02-02 10:00:00'),
(58, 'The Shining', 4, 'Fiction', 450.00, 300.00, 'Horror novel set in an isolated hotel.', 'shining.jpg', 'Used', 8, '2025-02-03 10:00:00'),
(59, 'It', 4, 'Fiction', 500.00, 350.00, 'A group of friends confronts a shape-shifting entity.', 'it.jpg', 'Used', 5, '2025-02-04 10:00:00'),
(60, 'Misery', 4, 'Fiction', 400.00, 280.00, 'A writer is held captive by an obsessed fan.', 'misery.jpg', 'Used', 5, '2025-02-05 10:00:00'),
(61, 'Pet Sematary', 4, 'Fiction', 380.00, 260.00, 'A family faces the consequences of reviving the dead.', 'petsem.jpg', 'Used', 4, '2025-02-06 10:00:00'),
(62, 'The Stand', 4, 'Fiction', 520.00, 370.00, 'Epic tale of good versus evil after a plague.', 'stand.jpg', 'Used', 5, '2025-02-07 10:00:00'),
(64, '11/22/63', 4, 'Fiction', 480.00, 340.00, 'A man travels back in time to prevent JFK\'s assassination.', '112263.jpg', 'Used', 5, '2025-02-09 10:00:00'),
(65, 'The Dark Tower: The Gunslinger', 4, 'Fiction', 500.00, 360.00, 'First book in The Dark Tower series.', 'darktower1.jpg', 'Used', 6, '2025-02-10 10:00:00'),
(66, 'Norwegian Wood', 5, 'Fiction', 350.00, 220.00, 'A story of love and loss in 1960s Tokyo.', 'norwegian.jpg', 'New', 12, '2025-02-11 10:00:00'),
(67, 'Kafka on the Shore', 5, 'Fiction', 500.00, 360.00, 'A surreal novel blending metaphysical elements and reality.', 'kafka.jpg', 'New', 10, '2025-02-12 10:00:00'),
(68, '1Q84', 5, 'Fiction', 600.00, 400.00, 'A parallel world and love story in 1984 Tokyo.', '1q84.jpg', 'New', 5, '2025-02-13 10:00:00'),
(69, 'The Wind-Up Bird Chronicle', 5, 'Fiction', 550.00, 380.00, 'A man searches for his missing wife and self.', 'windup.jpg', 'New', 7, '2025-02-14 10:00:00'),
(70, 'Colorless Tsukuru Tazaki', 5, 'Fiction', 450.00, 320.00, 'A young man investigates the disappearance of friends.', 'tsukuru.jpg', 'New', 9, '2025-02-15 10:00:00'),
(71, 'Hard-Boiled Wonderland and the End of the World', 5, 'Fiction', 500.00, 350.00, 'A dystopian dual-world narrative.', 'hardboiled.jpg', 'New', 5, '2025-02-16 10:00:00'),
(72, 'After Dark', 5, 'Fiction', 400.00, 300.00, 'Story unfolding overnight in Tokyo.', 'afterdark.jpg', 'New', 4, '2025-02-17 10:00:00'),
(73, 'South of the Border, West of the Sun', 5, 'Fiction', 380.00, 270.00, 'A tale of love, memory, and longing.', 'southborder.jpg', 'New', 6, '2025-02-18 10:00:00'),
(74, 'Sputnik Sweetheart', 5, 'Fiction', 360.00, 250.00, 'Young woman disappears and a friend searches for her.', 'sputnik.jpg', 'New', 5, '2025-02-19 10:00:00'),
(75, 'Dance Dance Dance', 5, 'Fiction', 400.00, 300.00, 'A man investigates a mysterious hotel and strange events.', 'dance.jpg', 'New', 4, '2025-02-20 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `book_images`
--

CREATE TABLE `book_images` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_images`
--

INSERT INTO `book_images` (`id`, `book_id`, `image_path`) VALUES
(43, 26, '691e6e9596984_endhouse.jpg'),
(44, 26, '691e6e95975da_styles.jpg'),
(45, 26, '691e6e959815e_abc.jpg'),
(46, 26, '691e6e9599387_ackroyd.jpg'),
(47, 26, '691e6e959a560_attwn.jpg'),
(48, 27, '691e6ea200941_hp7.jpg'),
(49, 27, '691e6ea2027d1_hp6.jpg'),
(50, 27, '691e6ea203b79_hp5.jpg'),
(51, 27, '691e6ea204d8c_hp4.jpg'),
(52, 27, '691e6ea206449_hp3.jpg'),
(53, 27, '691e6ea20786c_hp2.jpg'),
(54, 27, '691e6ea2085da_hp1.jpg'),
(55, 28, '691e6eab4a2dd_hp7.jpg'),
(56, 28, '691e6eab4b6d0_hp6.jpg'),
(57, 28, '691e6eab4c938_hp5.jpg'),
(58, 28, '691e6eab4d6f2_hp4.jpg'),
(59, 28, '691e6eab4e6e9_hp3.jpg'),
(60, 28, '691e6eab4f621_hp2.jpg'),
(61, 28, '691e6eab506c6_hp1.jpg'),
(62, 29, '691e6eb4967c3_hp7.jpg'),
(63, 29, '691e6eb497737_hp6.jpg'),
(64, 29, '691e6eb4984ad_hp5.jpg'),
(65, 29, '691e6eb499006_hp4.jpg'),
(66, 29, '691e6eb499988_hp3.jpg'),
(67, 29, '691e6eb49a1e1_hp2.jpg'),
(68, 29, '691e6eb49ae09_hp1.jpg'),
(69, 30, '691e6ebb892ea_hp7.jpg'),
(70, 30, '691e6ebb8a680_hp6.jpg'),
(71, 30, '691e6ebb8b27d_hp5.jpg'),
(72, 30, '691e6ebb8c85f_hp4.jpg'),
(73, 30, '691e6ebb8d96b_hp3.jpg'),
(74, 30, '691e6ebb8e86f_hp2.jpg'),
(75, 30, '691e6ebb8fd61_hp1.jpg'),
(76, 32, '691e6ec4b0777_hp7.jpg'),
(77, 32, '691e6ec4b166b_hp6.jpg'),
(78, 32, '691e6ec4b232e_hp5.jpg'),
(79, 32, '691e6ec4b2e2a_hp4.jpg'),
(80, 32, '691e6ec4b393b_hp3.jpg'),
(81, 32, '691e6ec4b43cb_hp2.jpg'),
(82, 32, '691e6ec4b4d36_hp1.jpg'),
(83, 41, '691e6ed4df94e_hp7.jpg'),
(84, 41, '691e6ed4e01b5_hp6.jpg'),
(85, 41, '691e6ed4e0a45_hp5.jpg'),
(86, 41, '691e6ed4e1385_hp4.jpg'),
(87, 41, '691e6ed4e1c2e_hp3.jpg'),
(88, 41, '691e6ed4e248d_hp2.jpg'),
(89, 41, '691e6ed4e2ca7_hp1.jpg'),
(90, 75, '691e8a3b1f5f2_dance.jpg'),
(91, 75, '691e8a3b20206_sputnik.jpg'),
(92, 75, '691e8a3b20cbb_southborder.jpg'),
(93, 75, '691e8a3b21a32_afterdark.jpg'),
(94, 75, '691e8a3b22463_hardboiled.jpg'),
(95, 75, '691e8a3b234c9_tsukuru.jpg'),
(96, 74, '691e8a4be17a2_fb1.jpg'),
(97, 74, '691e8a4be2eb2_hp7.jpg'),
(98, 74, '691e8a4be561e_hp6.jpg'),
(99, 74, '691e8a4be6099_hp5.jpg'),
(100, 74, '691e8a4be75e9_hp4.jpg'),
(101, 74, '691e8a4be8362_hp3.jpg'),
(102, 74, '691e8a4be96d1_hp2.jpg'),
(103, 42, '691e8a544b23f_afterdark.jpg'),
(104, 42, '691e8a544bca5_hardboiled.jpg'),
(105, 42, '691e8a544cd2e_tsukuru.jpg'),
(106, 42, '691e8a544d789_windup.jpg'),
(107, 42, '691e8a544e3fa_1q84.jpg'),
(108, 55, '691e8a5e1e55f_dream1.jpg'),
(109, 55, '691e8a5e1f207_got6.jpeg'),
(110, 55, '691e8a5e1fdb5_got5.jpg'),
(111, 55, '691e8a5e2089a_got4.jpg'),
(112, 55, '691e8a5e213bf_got3.jpg'),
(113, 55, '691e8a5e223bb_got2.jpg'),
(114, 44, '691e8a6b05d65_got2.jpg'),
(115, 44, '691e8a6b072a0_got1.jpg'),
(116, 44, '691e8a6b0890a_beedle.jpg'),
(117, 44, '691e8a6b09edd_quidditch.jpg'),
(118, 44, '691e8a6b0b30e_fb1.jpg'),
(119, 44, '691e8a6b0c91f_hp7.jpg'),
(120, 44, '691e8a6b0d833_hp6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `amount`, `min_order`, `expires_at`) VALUES
(5, '12345', '', 123.00, 1.00, '2025-11-21 00:00:00'),
(6, '54321', '', 100.00, 1.00, '2025-11-21 00:00:00'),
(7, 'B10', '', 9.00, 1.00, '2025-11-21 00:00:00'),
(8, 'B11', '', 10.00, 1.00, '2025-11-22 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Paid','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `shipping_address` text DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `voucher_code` varchar(50) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`, `shipping_address`, `shipping_method`, `payment_method`, `voucher_code`, `shipping_fee`, `subtotal`, `total`) VALUES
(29, 21, NULL, 'Delivered', '2025-11-20 11:02:38', 'Taguig City', 'Express', 'COD', '', 150.00, 2130.00, 2280.00),
(31, 21, NULL, 'Delivered', '2025-11-20 11:17:36', 'Taguig City', 'Overnight', 'COD', '', 250.00, 2900.00, 3150.00),
(32, 22, NULL, 'Cancelled', '2025-11-20 11:33:19', 'Bagumbayan, Taguig City', 'Standard', 'COD', 'B10', 80.00, 2220.00, 2100.20);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(55, 29, 36, 2, 450.00),
(56, 29, 40, 1, 530.00),
(57, 29, 42, 1, 350.00),
(58, 29, 71, 1, 350.00),
(60, 31, 68, 3, 400.00),
(61, 31, 72, 1, 300.00),
(62, 31, 31, 1, 400.00),
(63, 31, 27, 2, 360.00),
(64, 31, 60, 1, 280.00),
(65, 32, 75, 4, 300.00),
(66, 32, 66, 1, 220.00),
(67, 32, 68, 2, 400.00);

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_transaction_details`
-- (See below for the actual view)
--
CREATE TABLE `order_transaction_details` (
`order_id` int(11)
,`user_id` int(11)
,`username` varchar(100)
,`email` varchar(150)
,`order_status` enum('Pending','Paid','Shipped','Delivered','Cancelled')
,`order_date` datetime
,`shipping_address` text
,`shipping_method` varchar(50)
,`order_payment_method` varchar(50)
,`voucher_code` varchar(50)
,`subtotal` decimal(10,2)
,`shipping_fee` decimal(10,2)
,`order_total` decimal(10,2)
,`order_item_id` int(11)
,`book_id` int(11)
,`book_title` varchar(200)
,`genre` enum('Fiction','Non-Fiction','None','')
,`author_id` int(11)
,`book_price` decimal(10,2)
,`quantity` int(11)
,`item_total` decimal(20,2)
,`transaction_id` int(11)
,`transaction_payment_method` varchar(50)
,`amount_paid` decimal(10,2)
,`transaction_status` enum('Success','Failed','Refunded')
,`processed_at` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `book_id`, `rating`, `review_text`, `created_at`) VALUES
(37, 1, 26, 5, 'Absolutely loved this book! A must-read.', '2025-01-10 12:34:00'),
(38, 2, 26, 4, 'Great story, very engaging.', '2025-01-11 14:20:00'),
(39, 3, 26, 3, 'It was okay. Some parts were interesting.', '2025-01-12 09:45:00'),
(40, 4, 26, 5, 'Fantastic! Could not put it down.', '2025-01-13 18:10:00'),
(41, 5, 26, 4, 'Enjoyable read with well-developed characters.', '2025-01-14 20:05:00'),
(42, 6, 27, 4, 'Really liked the plot twists.', '2025-01-15 11:22:00'),
(43, 7, 27, 5, 'Amazing book! Highly recommended.', '2025-01-16 16:33:00'),
(44, 8, 27, 3, 'Good, but not my favorite.', '2025-01-17 09:55:00'),
(45, 9, 27, 4, 'Interesting storyline and characters.', '2025-01-18 14:40:00'),
(46, 10, 27, 5, 'One of the best books I have read this year.', '2025-01-19 21:15:00'),
(47, 11, 28, 2, 'Didn\'t enjoy it much.', '2025-01-20 10:10:00'),
(48, 12, 28, 3, 'It was fine, nothing special.', '2025-01-21 12:00:00'),
(49, 13, 28, 4, 'Pretty good, liked some parts.', '2025-01-22 15:25:00'),
(50, 14, 28, 5, 'Loved it! Will read again.', '2025-01-23 18:50:00'),
(51, 15, 28, 4, 'Engaging story and well-written.', '2025-01-24 20:30:00'),
(52, 1, 26, 5, 'Absolutely loved this book! A must-read.', '2025-01-10 12:34:00'),
(53, 2, 26, 4, 'Great story, very engaging.', '2025-01-11 14:20:00'),
(54, 3, 26, 3, 'It was okay. Some parts were interesting.', '2025-01-12 09:45:00'),
(55, 4, 26, 5, 'Fantastic! Could not put it down.', '2025-01-13 18:10:00'),
(56, 5, 26, 4, 'Enjoyable read with well-developed characters.', '2025-01-14 20:05:00'),
(57, 6, 27, 4, 'Really liked the plot twists.', '2025-01-15 11:22:00'),
(58, 7, 27, 5, 'Amazing book! Highly recommended.', '2025-01-16 16:33:00'),
(59, 8, 27, 3, 'Good, but not my favorite.', '2025-01-17 09:55:00'),
(60, 9, 27, 4, 'Interesting storyline and characters.', '2025-01-18 14:40:00'),
(61, 10, 27, 5, 'One of the best books I have read this year.', '2025-01-19 21:15:00'),
(62, 11, 28, 2, 'Didn\'t enjoy it much.', '2025-01-20 10:10:00'),
(63, 12, 28, 3, 'It was fine, nothing special.', '2025-01-21 12:00:00'),
(64, 13, 28, 4, 'Pretty good, liked some parts.', '2025-01-22 15:25:00'),
(65, 14, 28, 5, 'Loved it! Will read again.', '2025-01-23 18:50:00'),
(66, 15, 28, 4, 'Engaging story and well-written.', '2025-01-24 20:30:00'),
(67, 1, 29, 5, 'Thrilling from start to finish.', '2025-01-25 11:00:00'),
(68, 2, 29, 3, 'Average book, some chapters were slow.', '2025-01-26 13:45:00'),
(69, 3, 29, 4, 'Really enjoyed the characters and plot.', '2025-01-27 16:10:00'),
(70, 4, 29, 2, 'Not my type, but others may like it.', '2025-01-28 17:20:00'),
(71, 5, 29, 5, 'Highly recommended! Fantastic read.', '2025-01-29 19:50:00'),
(72, 6, 30, 4, 'Engaging story and well-written characters.', '2025-01-30 10:15:00'),
(73, 7, 30, 5, 'Absolutely amazing!', '2025-01-31 14:05:00'),
(74, 8, 30, 3, 'Good, but could be better.', '2025-02-01 12:20:00'),
(75, 9, 30, 4, 'Enjoyed reading it thoroughly.', '2025-02-02 15:40:00'),
(76, 10, 30, 5, 'Loved it! Will recommend to friends.', '2025-02-03 18:30:00'),
(77, 11, 31, 4, 'A solid book, really enjoyed it.', '2025-02-04 11:10:00'),
(78, 12, 31, 5, 'Fantastic plot and character development.', '2025-02-05 16:20:00'),
(79, 13, 31, 3, 'It was okay, nothing special.', '2025-02-06 09:55:00'),
(80, 14, 31, 4, 'Interesting storyline, kept me engaged.', '2025-02-07 13:15:00'),
(81, 15, 31, 5, 'One of the best reads this year.', '2025-02-08 19:40:00'),
(82, 1, 26, 5, 'Absolutely loved this book! A must-read.', '2025-01-10 12:34:00'),
(83, 2, 26, 4, 'Great story, very engaging.', '2025-01-11 14:20:00'),
(84, 3, 26, 3, 'It was okay. Some parts were interesting.', '2025-01-12 09:45:00'),
(85, 4, 26, 5, 'Fantastic! Could not put it down.', '2025-01-13 18:10:00'),
(86, 5, 26, 4, 'Enjoyable read with well-developed characters.', '2025-01-14 20:05:00'),
(87, 6, 27, 4, 'Really liked the plot twists.', '2025-01-15 11:22:00'),
(88, 7, 27, 5, 'Amazing book! Highly recommended.', '2025-01-16 16:33:00'),
(89, 8, 27, 3, 'Good, but not my favorite.', '2025-01-17 09:55:00'),
(90, 9, 27, 4, 'Interesting storyline and characters.', '2025-01-18 14:40:00'),
(91, 10, 27, 5, 'One of the best books I have read this year.', '2025-01-19 21:15:00'),
(92, 11, 28, 2, 'Didn\'t enjoy it much.', '2025-01-20 10:10:00'),
(93, 12, 28, 3, 'It was fine, nothing special.', '2025-01-21 12:00:00'),
(94, 13, 28, 4, 'Pretty good, liked some parts.', '2025-01-22 15:25:00'),
(95, 14, 28, 5, 'Loved it! Will read again.', '2025-01-23 18:50:00'),
(96, 15, 28, 4, 'Engaging story and well-written.', '2025-01-24 20:30:00'),
(97, 1, 29, 5, 'Thrilling from start to finish.', '2025-01-25 11:00:00'),
(98, 2, 29, 3, 'Average book, some chapters were slow.', '2025-01-26 13:45:00'),
(99, 3, 29, 4, 'Really enjoyed the characters and plot.', '2025-01-27 16:10:00'),
(100, 4, 29, 2, 'Not my type, but others may like it.', '2025-01-28 17:20:00'),
(101, 5, 29, 5, 'Highly recommended! Fantastic read.', '2025-01-29 19:50:00'),
(102, 6, 30, 4, 'Engaging story and well-written characters.', '2025-01-30 10:15:00'),
(103, 7, 30, 5, 'Absolutely amazing!', '2025-01-31 14:05:00'),
(104, 8, 30, 3, 'Good, but could be better.', '2025-02-01 12:20:00'),
(105, 9, 30, 4, 'Enjoyed reading it thoroughly.', '2025-02-02 15:40:00'),
(106, 10, 30, 5, 'Loved it! Will recommend to friends.', '2025-02-03 18:30:00'),
(107, 11, 31, 4, 'A solid book, really enjoyed it.', '2025-02-04 11:10:00'),
(108, 12, 31, 5, 'Fantastic plot and character development.', '2025-02-05 16:20:00'),
(109, 13, 31, 3, 'It was okay, nothing special.', '2025-02-06 09:55:00'),
(110, 14, 31, 4, 'Interesting storyline, kept me engaged.', '2025-02-07 13:15:00'),
(111, 15, 31, 5, 'One of the best reads this year.', '2025-02-08 19:40:00'),
(112, 1, 32, 5, 'Loved the pacing and the story.', '2025-02-09 12:00:00'),
(113, 2, 32, 3, 'Average, not very memorable.', '2025-02-10 14:30:00'),
(114, 3, 32, 4, 'Really enjoyed this book.', '2025-02-11 09:15:00'),
(115, 4, 32, 5, 'Fantastic characters and plot.', '2025-02-12 17:20:00'),
(116, 5, 32, 4, 'Good read, worth the time.', '2025-02-13 18:50:00'),
(117, 6, 33, 4, 'Interesting storyline.', '2025-02-14 11:40:00'),
(118, 7, 33, 5, 'Amazing book, highly recommend.', '2025-02-15 16:00:00'),
(119, 8, 33, 3, 'It was okay.', '2025-02-16 10:20:00'),
(120, 9, 33, 4, 'Enjoyed reading it.', '2025-02-17 15:15:00'),
(121, 10, 33, 5, 'One of the best books I\'ve read.', '2025-02-18 19:30:00'),
(122, 11, 34, 3, 'Average book.', '2025-02-19 12:05:00'),
(123, 12, 34, 4, 'Good weekend read.', '2025-02-20 13:45:00'),
(124, 13, 34, 5, 'Fantastic, loved it!', '2025-02-21 16:25:00'),
(127, 1, 35, 5, 'Highly recommend this book.', '2025-02-24 13:10:00'),
(130, 4, 35, 4, 'Enjoyed reading it.', '2025-02-27 15:50:00'),
(131, 5, 35, 5, 'One of the best books I\'ve read.', '2025-02-28 18:20:00'),
(132, 21, 36, 4, 'Wow! ****', '2025-11-20 11:04:42'),
(133, 21, 36, 5, 'Ganda!', '2025-11-20 11:04:54'),
(134, 21, 42, 5, 'Still wow!', '2025-11-20 11:06:21'),
(135, 22, 75, 3, '**** packaginnffga tst', '2025-11-20 11:35:02'),
(136, 22, 66, 2, '**** ka hehehehe est', '2025-11-20 11:35:21'),
(137, 22, 68, 2, 'uyequiequyeqe ****', '2025-11-20 11:35:36'),
(138, 22, 68, 5, 'update test', '2025-11-20 11:36:28'),
(139, 22, 66, 5, 'update testttttt', '2025-11-20 11:36:47'),
(140, 22, 75, 5, 'update test', '2025-11-20 11:37:04'),
(141, 22, 75, 5, 'update test XDXD', '2025-11-20 11:48:22'),
(142, 22, 75, 5, 'update test BRUH', '2025-11-20 11:48:40'),
(143, 22, 75, 4, 'Hays', '2025-11-20 11:53:11'),
(144, 22, 75, 5, 'Okay', '2025-11-20 11:53:20');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `status` enum('Success','Failed','Refunded') DEFAULT 'Success',
  `processed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'jdoe', 'jdoe@example.com', '$2y$10$eH9cR7JkF1xBvWqN8aYz7O0QvXb1ZrL2tM4uP9dE5fK3sT6yL1nG.', 'admin', 'active', '2025-01-10 10:15:00'),
(2, 'asmith', 'asmith@example.com', '$2y$10$kL8vF9hM2yRzVwX0bYdQ7N5eGvCj1T8oP3xS6rK4uJqH9mB7nF2p.', 'customer', 'active', '2025-01-11 11:20:00'),
(3, 'mjohnson', 'mjohnson@example.com', '$2y$10$aM5vP7cL9kQxW2tY8zBfN1oR4eHj6S3gD7uV0bK5nTqL1xC9rF8y.', 'customer', 'active', '2025-01-12 09:30:00'),
(4, 'ljames', 'ljames@example.com', '$2y$10$dN7kR8fV1pXzL3bJ5aQyM6wT2eHc9S0oG4vB1uK7jPqN8rF2tL5.', 'customer', 'active', '2025-01-13 12:45:00'),
(5, 'kwilliams', 'kwilliams@example.com', '$2y$10$fL3pX9mQ5vJzT1kC7aRbH4oW2yD6sN8gU0eP1tK3nFqL9vY5cH7.', 'customer', 'active', '2025-01-14 08:50:00'),
(6, 'bjones', 'bjones@example.com', '$2y$10$hT5vB8kR2xLzJ6nF1cQpG9oY4eM7wS3uD0yK5tN8rPqV2bH7aC1.', 'customer', '', '2025-01-15 14:00:00'),
(7, 'mmiller', 'mmiller@example.com', '$2y$10$jQ2kX7vP1nRzL4fD9aTbC3oY6eH8sM0gU5yK1tN3rPqV7bF2cL8.', 'customer', 'active', '2025-01-16 16:10:00'),
(8, 'dtaylor', 'dtaylor@example.com', '$2y$10$lM8vF1kR5pXzJ3bQ7aYdH2oN4eG9sS0uT6yK3tP8rLqV1cB5nF9.', 'customer', '', '2025-01-17 10:25:00'),
(9, 'randerson', 'randerson@example.com', '$2y$10$mN5kR2fV8xLzJ1bD4aQyT6oP3eH7sS9gU0yK5tN2rPqV6bH1cF3.', 'customer', '', '2025-01-18 13:35:00'),
(10, 'croberts', 'croberts@example.com', '$2y$10$oP1vX3mQ7nRzL6fJ2aTbC5oY9eH0sS4gU8yK1tN7rPqV3bF9cL2.', 'customer', 'active', '2025-01-19 09:15:00'),
(11, 'ljohnson', 'ljohnson@example.com', '$2y$10$qR4kX9vP2nJzL5fD1aYbH6oW3eM8sS0gU2yK7tN4rPqV9bF5cL1.', 'customer', '', '2025-01-20 11:50:00'),
(12, 'mmartin', 'mmartin@example.com', '$2y$10$sT6vB1kR9xLzJ3nF4cQpG7oY2eH5sS8uD1yK0tN6rPqV2bH8aC3.', 'customer', '', '2025-01-21 15:05:00'),
(13, 'slee', 'slee@example.com', '$2y$10$tU9kR4fV3pXzJ7bD1aQyM6oN0eH8sS5gU2yK3tP9rPqV6bF1cL4.', 'customer', '', '2025-01-22 10:40:00'),
(14, 'kwalker', 'kwalker@example.com', '$2y$10$vW1vF5kR2xLzJ8nQ3aTbH9oY6eM1sS4gU7yK0tN5rPqV2bF9cL3.', 'customer', '', '2025-01-23 12:30:00'),
(15, 'phillips', 'phillips@example.com', '$2y$10$xY2kX6vP1nJzL4fD8aQyT3oP9eH0sS7gU5yK2tN8rPqV1bF6cL9.', 'admin', '', '2025-01-24 14:20:00'),
(16, 'brobinson', 'brobinson@example.com', '$2y$10$yZ3vB7kR5xLzJ2nF9cQpG0oY4eH6sS1uD8yK3tN7rPqV5bH2aC0.', 'admin', '', '2025-01-25 09:55:00'),
(17, 'gharris', 'gharris@example.com', '$2y$10$zA4kR8fV3pXzJ6bD1aQyM9oN2eH7sS5gU0yK4tP1rPqV8bF3cL6.', 'admin', 'active', '2025-01-26 11:15:00'),
(18, 'bdavis', 'bdavis@example.com', '$2y$10$aB5vF1kR2xLzJ7nQ4aTbH3oY8eM0sS6gU2yK5tN3rPqV9bF1cL7.', 'admin', 'active', '2025-01-27 13:45:00'),
(19, 'mthomas', 'mthomas@example.com', '$2y$10$bC6kX2vP9nJzL1fD3aQyT5oP7eH4sS0gU8yK1tN6rPqV2bF9cL8.', 'admin', 'active', '2025-01-28 08:30:00'),
(20, 'jwhite', 'jwhite@example.com', '$2y$10$cD7vB3kR8xLzJ4nF0cQpG1oY5eH9sS2uD3yK7tN0rPqV5bF2aC6.', 'admin', 'active', '2025-01-29 10:05:00'),
(21, 'Francis', 'francisokay@gmail.com', '1fb3381f4a67bfc2b7766213d411e29c8fca277c', 'admin', 'active', '2025-11-19 12:48:31'),
(22, 'eli', 'elijah@gmail.com', '8cb2237d0679ca88db6464eac60da96345513964', 'customer', 'active', '2025-11-20 11:29:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_initial` varchar(5) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `last_name`, `first_name`, `middle_initial`, `phone`, `address`, `town`, `zipcode`, `profile_picture`) VALUES
(1, 1, 'Doe', 'John', 'A', '09171234567', '123 Elm St', 'Manila', '1000', 'profile1.jpg'),
(2, 2, 'Smith', 'Alice', 'B', '09172345678', '456 Oak St', 'Quezon City', '1100', 'profile2.jpg'),
(3, 3, 'Johnson', 'Michael', 'C', '09173456789', '789 Pine St', 'Makati', '1200', 'profile3.jpg'),
(4, 4, 'James', 'Linda', 'D', '09174567890', '321 Maple St', 'Pasig', '1250', 'profile4.jpg'),
(5, 5, 'Williams', 'Kevin', 'E', '09175678901', '654 Cedar St', 'Taguig', '1300', 'profile5.jpg'),
(6, 6, 'Jones', 'Barbara', 'F', '09176789012', '987 Birch St', 'Mandaluyong', '1400', 'profile6.jpg'),
(7, 7, 'Miller', 'Mark', 'G', '09177890123', '147 Spruce St', 'Pasay', '1500', 'profile7.jpg'),
(8, 8, 'Taylor', 'Deborah', 'H', '09178901234', '258 Walnut St', 'Parañaque', '1600', 'profile8.jpg'),
(9, 9, 'Anderson', 'Robert', 'I', '09179012345', '369 Chestnut St', 'Las Piñas', '1700', 'profile9.jpg'),
(10, 10, 'Roberts', 'Catherine', 'J', '09170123456', '159 Ash St', 'Muntinlupa', '1800', 'profile10.jpg'),
(11, 11, 'Johnson', 'Laura', 'K', '09171234568', '753 Pine St', 'Caloocan', '1900', 'profile11.jpg'),
(12, 12, 'Martin', 'Matthew', 'L', '09172345679', '852 Oak St', 'Valenzuela', '2000', 'profile12.jpg'),
(13, 13, 'Lee', 'Sarah', 'M', '09173456780', '951 Elm St', 'Malabon', '2100', 'profile13.jpg'),
(14, 14, 'Walker', 'Kyle', 'N', '09174567891', '357 Cedar St', 'Navotas', '2200', 'profile14.jpg'),
(15, 15, 'Phillips', 'Patricia', 'O', '09175678902', '258 Birch St', 'Marikina', '2300', 'profile15.jpg'),
(16, 16, 'Robinson', 'Brian', 'P', '09176789013', '147 Spruce St', 'San Juan', '2400', 'profile16.jpg'),
(17, 17, 'Harris', 'Grace', 'Q', '09177890124', '369 Walnut St', 'Pasig', '2500', 'profile17.jpg'),
(18, 18, 'Davis', 'Brandon', 'R', '09178901235', '159 Chestnut St', 'Makati', '2600', 'user_18.png'),
(19, 19, 'Thomas', 'Monica', 'S', '09179012346', '753 Ash St', 'Quezon City', '2700', 'user_19.png'),
(20, 20, 'White', 'James', 'T', '09170123457', '852 Pine St', 'Taguig', '2800', 'user_20.jpg'),
(21, 21, 'Balbin', 'Jrob Francis', 'G', '092345678132', 'Taguig City', 'Tanyag', '1630', 'user_21.jpg'),
(22, 22, 'Gallardo', 'Elijah', 'S', '92345678132', 'Bagumbayan, Taguig City', 'Taguig City', '1234', '1763610944_dance.jpg');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_cart_items`
-- (See below for the actual view)
--
CREATE TABLE `view_cart_items` (
`user_id` int(11)
,`book_id` int(11)
,`quantity` int(11)
,`title` varchar(200)
,`price` decimal(10,2)
,`image` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_user_profile`
-- (See below for the actual view)
--
CREATE TABLE `view_user_profile` (
`user_id` int(11)
,`username` varchar(100)
,`email` varchar(150)
,`role` enum('admin','customer')
,`status` enum('active','inactive')
,`created_at` datetime
,`first_name` varchar(100)
,`middle_initial` varchar(5)
,`last_name` varchar(100)
,`phone` varchar(50)
,`address` text
,`town` varchar(100)
,`zipcode` varchar(20)
,`profile_picture` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `order_transaction_details`
--
DROP TABLE IF EXISTS `order_transaction_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_transaction_details`  AS SELECT `o`.`id` AS `order_id`, `o`.`user_id` AS `user_id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `o`.`status` AS `order_status`, `o`.`created_at` AS `order_date`, `o`.`shipping_address` AS `shipping_address`, `o`.`shipping_method` AS `shipping_method`, `o`.`payment_method` AS `order_payment_method`, `o`.`voucher_code` AS `voucher_code`, `o`.`subtotal` AS `subtotal`, `o`.`shipping_fee` AS `shipping_fee`, `o`.`total` AS `order_total`, `oi`.`id` AS `order_item_id`, `oi`.`book_id` AS `book_id`, `b`.`title` AS `book_title`, `b`.`genre` AS `genre`, `b`.`author_id` AS `author_id`, `b`.`price` AS `book_price`, `oi`.`quantity` AS `quantity`, `oi`.`quantity`* `oi`.`price` AS `item_total`, `t`.`id` AS `transaction_id`, `t`.`payment_method` AS `transaction_payment_method`, `t`.`amount_paid` AS `amount_paid`, `t`.`status` AS `transaction_status`, `t`.`processed_at` AS `processed_at` FROM ((((`orders` `o` left join `users` `u` on(`o`.`user_id` = `u`.`id`)) left join `order_items` `oi` on(`oi`.`order_id` = `o`.`id`)) left join `books` `b` on(`oi`.`book_id` = `b`.`id`)) left join `transactions` `t` on(`t`.`order_id` = `o`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_cart_items`
--
DROP TABLE IF EXISTS `view_cart_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_cart_items`  AS SELECT `c`.`user_id` AS `user_id`, `c`.`book_id` AS `book_id`, `c`.`quantity` AS `quantity`, `b`.`title` AS `title`, `b`.`price` AS `price`, `b`.`image` AS `image` FROM (`cart_items` `c` join `books` `b` on(`b`.`id` = `c`.`book_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_user_profile`
--
DROP TABLE IF EXISTS `view_user_profile`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_user_profile`  AS SELECT `u`.`id` AS `user_id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `u`.`role` AS `role`, `u`.`status` AS `status`, `u`.`created_at` AS `created_at`, `p`.`first_name` AS `first_name`, `p`.`middle_initial` AS `middle_initial`, `p`.`last_name` AS `last_name`, `p`.`phone` AS `phone`, `p`.`address` AS `address`, `p`.`town` AS `town`, `p`.`zipcode` AS `zipcode`, `p`.`profile_picture` AS `profile_picture` FROM (`users` `u` left join `user_profiles` `p` on(`u`.`id` = `p`.`user_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `book_images`
--
ALTER TABLE `book_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `book_images`
--
ALTER TABLE `book_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_fk_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `book_images`
--
ALTER TABLE `book_images`
  ADD CONSTRAINT `book_images_fk_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_fk_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_fk_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_fk_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
