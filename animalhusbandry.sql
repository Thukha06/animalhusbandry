-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2024 at 08:21 PM
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
-- Database: `animal_husbandry`
--
CREATE DATABASE IF NOT EXISTS `animal_husbandry` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `animal_husbandry`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `company_id`, `password`) VALUES
(3, 18, 'thihaaung123'),
(13, 16, 'aikoo123'),
(16, 23, 'myat123');

-- --------------------------------------------------------

--
-- Table structure for table `animal_type`
--

CREATE TABLE `animal_type` (
  `animal_id` int(11) NOT NULL,
  `animal_type` varchar(60) NOT NULL,
  `animal_photo` varchar(255) NOT NULL,
  `animal_des` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animal_type`
--

INSERT INTO `animal_type` (`animal_id`, `animal_type`, `animal_photo`, `animal_des`) VALUES
(29, 'Pig', 'viber_image_2024-07-23_21-51-46-805.jpg', 'ကျွန်ုပ်တို့၏လယ်ယာ၏ အဓိကတိရစ္ဆာန်များထဲမှတစ်ခုဖြစ်ပြီး, အသားထုတ်လုပ်မှုအတွက် အဓိကရှိသည်။ သူတို့သည် ဉာဏ်ကောင်းမှုနှင့် လယ်ယာအကျိုးပြုလုပ်ပေးနိုင်မှုအတွက် လူကြိုက်များသည်။'),
(30, 'Cow', 'cow2.jpg', 'ကျွန်ုပ်တို့၏လယ်ယာ၏ အဓိကတိရစ္ဆာန်များဖြစ်ပြီး, နို့နှင့် အသားထုတ်လုပ်မှုအတွက် အဓိကရှိသည်။ သူတို့သည် ငြိမ်သက်ကြောင်းနှင့် လယ်ယာအကျိုးပြုလုပ်ပေးသော တိရစ္ဆာန်များဖြစ်သည်။'),
(32, 'Fish', 'images.jpg', 'ကျွန်ုပ်တို့၏လယ်ယာ၏ ရေပြင်အကျိုးပြုတိရစ္ဆာန်များဖြစ်ပြီး, သန့်ရှင်းသော ရေအတွက် အဓိကဖြစ်သည်။ သူတို့သည် သဘာဝဘေးအန္တရာယ်များမှ ကာကွယ်ပေးပြီး လယ်ယာ၏ အထောက်အကူပြုမှုရှိသော တိရစ္ဆာန်များဖြစ်သည်။'),
(41, 'Goat', 'Goat.jpg', 'ဆိတ်သည် ကျွန်ုပ်တို့၏လယ်ယာတွင် သဘာဝအကျိုးပြုသော တိရစ္ဆာန်ဖြစ်ပြီး, နို့နှင့် အသားထုတ်လုပ်မှုအတွက် အဓိကဖြစ်သည်။ သူတို့သည် ငြိမ်းချမ်းပြီး စောင့်ရှောက်ရလွယ်ကူသော တိရစ္ဆာန်များဖြစ်သည်။'),
(42, 'Chicken', 'chicken.jpg', 'ကြက်များသည် ကျွန်ုပ်တို့၏လယ်ယာတွင် အဓိကကြိုးစားသော တိရစ္ဆာန်များဖြစ်ပြီး, ဥများနှင့် အသားထုတ်လုပ်မှုအတွက် အဓိကရှိသည်။ သူတို့သည် လန်းဆန်းတက်ကြွပြီး လယ်ယာထဲတွင် နေ့စဉ်ဖွံ့ဖြိုးတိုးတက်သည်။');

-- --------------------------------------------------------

--
-- Table structure for table `breed_animal`
--

CREATE TABLE `breed_animal` (
  `breed_animal_id` int(11) NOT NULL,
  `stock_animal` int(11) DEFAULT NULL,
  `breed_date` date NOT NULL,
  `animal_id` int(11) NOT NULL,
  `breed_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `breed_animal`
--

INSERT INTO `breed_animal` (`breed_animal_id`, `stock_animal`, `breed_date`, `animal_id`, `breed_id`) VALUES
(48, 80, '2024-07-26', 29, 35),
(49, 65, '2024-07-27', 29, 36),
(54, 75, '2024-07-30', 32, 37),
(63, 10, '2024-08-02', 30, 47),
(67, NULL, '2024-08-14', 41, 51),
(68, 30, '2024-08-16', 42, 52);

-- --------------------------------------------------------

--
-- Table structure for table `breed_technology`
--

CREATE TABLE `breed_technology` (
  `breed_id` int(11) NOT NULL,
  `breed_type` varchar(300) NOT NULL,
  `breed_des` text NOT NULL,
  `breed_photo` varchar(60) NOT NULL,
  `animal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `breed_technology`
--

INSERT INTO `breed_technology` (`breed_id`, `breed_type`, `breed_des`, `breed_photo`, `animal_id`) VALUES
(35, 'Black Pig', 'Black Pig သည် ကျွန်ုပ်တို့၏လယ်ယာမှ အထူးအရည်အသွေးမြင့် ဝက်များဖြစ်ပြီး, အစွမ်းထက်သော အသားနှင့် အရသာရရှိစေပါသည်။ ၎င်းသည် အနည်းငယ် မတူညီသော အသားအရောင်နှင့် မြေးတင်ကောင်းမွန်သော အရသာဖြင့် လက်ခံသုံးစွဲရန် သင့်လျော်သည်။', 'viber_image_2024-07-23_21-43-10-418.jpg', 29),
(36, 'Mud Pig', 'Mud Pig သည် ကျွန်ုပ်တို့၏လယ်ယာမှ ပုဇွန်ခေတ်သစ် ဝက်များဖြစ်ပြီး, စိတ်လှုပ်ရှားဖွယ်ကောင်းသော အနံ့နှင့် အရသာကို ပေးစွမ်းသည်။ သူတို့သည် ကျန်းမာရေးအတွက် အထူးသဖြင့် သဘာဝအတိုင်း စောင့်ရှောက်ထားပြီး, တိကျသော မျှော်မှန်းချက်နှင့် အရသာအထူးများဖြင့် ရရှိနိုင်ပါသည်။', 'viber_image_2024-07-23_21-51-54-515.jpg', 29),
(37, 'Gold Fish', 'ရွှေရောင်ငါးများသည် ကျွန်ုပ်တို့၏ရေကန်ထဲတွင် လှပသည့်အလှဆင်တိရစ္ဆာန်များဖြစ်ပြီး, စိမ်းလန်းစေသော ရေကန်ဘဝကို ဖန်တီးပေးသည်။ သူတို့သည် ရှုမူလိုက်စရာ သာယာမှုနှင့် သက်တောင့်သက်သာရှိစေသည်။', 'images (1).jpg', 32),
(47, 'Holy Cow', 'Holy Cow သည် ကျွန်ုပ်တို့၏လယ်ယာမှ ထုတ်လုပ်သော အထူးအရည်အသွေးမြင့် နို့ထုတ်ကုန်ဖြစ်ပြီး, မျှော်မှန်းထားသော အရသာနှင့် နူးညံ့မှုကို ပေးစွမ်းသည်။ ၎င်းသည် သာမန် နို့ထုတ်ကုန်များထက် ခြားနားသော အထူးသဖြင့် စားပြုရန် အထူးသင့်လျော်သည်။ အဓိကဖြစ်သည့် ငယ်စဉ်လေးစားမှုနဲ့ ကြီးထွားမှု၏ ပေါင်းစည်းမှုဖြင့် သင်၏အစားအစာကို တိုးတက်အောင် မြှင့်တင်ပေးပါသည်။', 'Holy-cow.jpg', 30),
(51, 'Myotonic Goat', 'Myotonic ဆိတ်များသည် ကျွန်ုပ်တို့၏လယ်ယာတွင် အထူးထိန်းသိမ်းထားသော တိရစ္ဆာန်များဖြစ်ပြီး, သူတို့၏ လှုပ်ရှားမှုအပြောင်းအလဲများကြောင့် လူသိများသည်။ သူတို့သည် အားကောင်းကောင်းနှင့် အာဟာရပြည့်ဝသော နို့နှင့် အသားကို ထုတ်လုပ်ပေးသည်။', 'myotonic-1.jpg', 41),
(52, 'Rooster', 'ကြက်လှည့်များသည် ကျွန်ုပ်တို့၏လယ်ယာ၏ အရေးပါသော တိရစ္ဆာန်များဖြစ်ပြီး, နံနက်ခင်းတွင် အသံထွက်ခေါ်ဆိုခြင်းအတွက် ထင်ရှားသည်။ သူတို့သည် အခြားကြက်များကို ကာကွယ်စောင့်ရှောက်မှုနှင့် သဘာဝအကျိုးပြုမှုအတွက် အထူးအရေးပါသည်။', 'rooster.jpg', 42);

-- --------------------------------------------------------

--
-- Table structure for table `company_info`
--

CREATE TABLE `company_info` (
  `company_id` int(100) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_info`
--

INSERT INTO `company_info` (`company_id`, `company_name`, `email`, `phone`, `address`) VALUES
(16, 'Aik Oo', 'aikoo141121@gmail.com', '+959675620778', 'Namsung Township, Shan State.'),
(18, 'Thiha Aung	', 'saithihaa6@gmail.com', '+959975535849', 'Ward(4), Zayar Road, Loilem Township, Shan State.'),
(23, 'Myat Thu Kha', 'myatthukha000@gmail.com', '+959777439816', 'No.(16), Yadanar Road, Thingangyun, Yangon.'),
(24, 'GoldenTKM Co.Ltd', 'goldentkm@gmail.com', '+959695129912', 'Shwegondaing Road, Delta Plaza, Yangon.');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contact_id` int(100) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `contact_address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`contact_id`, `contact_name`, `email`, `phone`, `contact_address`) VALUES
(12, 'Aik Oo', 'aikoo141121@gmail.com', '09675620778', 'Fill out the contact form below to get in touch with us. We\'re here to answer your questions, provide information, and assist you in any way we can. We look forward to hearing from you!'),
(13, 'Myat Thu Kha', 'myatthukha000@gmail.com', '09777439816', 'Fill out the contact form below to get in touch with us. We\'re here to answer your questions, provide information, and assist you in any way we can. We look forward to hearing from you!'),
(14, 'Thiha Aung', 'saithihaa6@gmail.com', '09975535849', 'Fill out the contact form below to get in touch with us. We\'re here to answer your questions, provide information, and assist you in any way we can. We look forward to hearing from you!');

-- --------------------------------------------------------

--
-- Table structure for table `dead_records`
--

CREATE TABLE `dead_records` (
  `dead_record_id` int(11) NOT NULL,
  `number_quantity` int(11) NOT NULL,
  `type_disease` varchar(200) NOT NULL,
  `dead_date` date NOT NULL,
  `breed_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dead_records`
--

INSERT INTO `dead_records` (`dead_record_id`, `number_quantity`, `type_disease`, `dead_date`, `breed_id`) VALUES
(16, 20, 'Polio', '2024-07-30', 35),
(21, 25, 'Drowning', '2024-07-30', 37),
(22, 15, 'Being dirty', '2024-07-30', 36);

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_type`
--

CREATE TABLE `knowledge_type` (
  `knowledge_id` int(11) NOT NULL,
  `knowledge_type` varchar(60) NOT NULL,
  `video_photo` varchar(100) NOT NULL,
  `breed_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_type`
--

INSERT INTO `knowledge_type` (`knowledge_id`, `knowledge_type`, `video_photo`, `breed_id`) VALUES
(37, '7 Mind-Blowing Facts About Goldfish', '7 Mind-Blowing Facts About Goldfish.mp4', 37),
(39, 'The Holy Cow', 'The Holy Cow.mp4', 47),
(40, 'Why Do Pigs Like Mud', 'Why Do Pigs Like Mud.mp4', 36),
(43, 'How To Tell if Your Chicken is a Rooster', 'How To Tell if Your Chicken is a Rooster.mp4', 52);

-- --------------------------------------------------------

--
-- Table structure for table `product_records`
--

CREATE TABLE `product_records` (
  `product_record_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_records`
--

INSERT INTO `product_records` (`product_record_id`, `product_id`, `product_quantity`, `product_date`) VALUES
(28, 1, 100, '2024-07-29'),
(29, 2, 60, '2024-08-05'),
(30, 3, 30, '2024-08-05'),
(31, 4, 25, '2024-08-05'),
(32, 1, 100, '2024-08-05'),
(33, 2, 40, '2024-07-29'),
(34, 3, 25, '2024-07-29'),
(35, 4, 50, '2024-07-29'),
(40, 7, 60, '2024-08-06'),
(41, 5, 20, '2024-08-07'),
(42, 1, 10, '2024-08-14'),
(44, 5, 100, '2024-08-15'),
(45, 1, 50, '2024-08-15'),
(46, 7, 100, '2024-08-16');

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(60) NOT NULL,
  `product_unit` varchar(60) NOT NULL,
  `product_photo` varchar(60) NOT NULL,
  `product_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`product_id`, `product_name`, `product_unit`, `product_photo`, `product_description`) VALUES
(1, 'Milk', 'L', 'Oat_milk_glass_and_bottles.jpg', 'နို့သည် ကျွန်ုပ်တို့၏လယ်ယာမှ ရရှိသော သန့်ရှင်းပြီး သန့်စင်သော ထုတ်ကုန်ဖြစ်ပြီး, ကျန်းမာရေးအတွက် အဓိက အာဟာရဓာတ်များကို ပံ့ပိုးပေးသည်။ နေ့စဉ်သောက်ရန် သင့်တော်ပြီး အရသာအထူးကောင်းမွန်သည်။'),
(2, 'Egg', 'Pic', 'Brown-eggs.png', 'ဥများသည် ကျွန်ုပ်တို့၏လယ်ယာမှ သန့်စင်စွာ ထုတ်လုပ်သော ထုတ်ကုန်ဖြစ်ပြီး, အာဟာရဓာတ်အပြည့်အဝ ပါရှိသည်။ နေ့စဉ်အတွက် အသုံးပြုရန် သင့်တော်ပြီး အရသာလည်း ကောင်းမွန်သည်။'),
(3, 'Meat', 'Kg', 'licensed-image.jpg', 'အသားသည် ကျွန်ုပ်တို့၏လယ်ယာမှ စနစ်တကျ ထုတ်လုပ်ထားသော အရည်အသွေးမြင့် ထုတ်ကုန်ဖြစ်ပြီး, အာဟာရဓာတ်များစွာ ပါဝင်သည်။ သန့်ရှင်းပြီး အရသာကောင်းကောင်းဖြင့် စားသုံးနိုင်ပါသည်။'),
(4, 'Wool', 'Kg', 'images (2).jpg', 'သိုးမွှေးသည် ကျွန်ုပ်တို့၏လယ်ယာမှ ရရှိသော သဘာဝထုတ်ကုန်ဖြစ်ပြီး, အပူပေးမှုနှင့် နူးညံ့မှုအတွက် အထူးကောင်းမွန်သည်။ အဝတ်အထည်များ၊ ကျပ်ကော်များ၊ နွားသားအိတ်များ အတွက် သုံးစွဲရန် သင့်တော်သည်။'),
(5, 'Cheese', 'Lb', 'Cheese.jpg', 'ချိစ်သည် ကျွန်ုပ်တို့၏လယ်ယာမှ ထုတ်လုပ်သော အရည်အသွေးမြင့် နို့ထွက်ပစ္စည်းဖြစ်ပြီး, အရသာအထူးကောင်းမွန်သည်။ စားသုံးရလွယ်ကူပြီး အာဟာရဓာတ်များစွာ ပါဝင်သည်။'),
(7, 'Butter', 'g', 'Butter.jpg', 'နို့ထောပတ်သည် ကျွန်ုပ်တို့၏လယ်ယာမှ ထုတ်လုပ်သော အရည်အသွေးမြင့် နို့ထွက်ပစ္စည်းဖြစ်ပြီး, အနံ့အရသာ အထူးကောင်းမွန်သည်။ အာဟာရဓာတ်များစွာ ပါဝင်ပြီး, မိသားစုအတွက် အသုံးပြုရန် သင့်တော်သည်။');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `company_id` (`company_id`);

--
-- Indexes for table `animal_type`
--
ALTER TABLE `animal_type`
  ADD PRIMARY KEY (`animal_id`);

--
-- Indexes for table `breed_animal`
--
ALTER TABLE `breed_animal`
  ADD PRIMARY KEY (`breed_animal_id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `breed_id` (`breed_id`);

--
-- Indexes for table `breed_technology`
--
ALTER TABLE `breed_technology`
  ADD PRIMARY KEY (`breed_id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Indexes for table `company_info`
--
ALTER TABLE `company_info`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `dead_records`
--
ALTER TABLE `dead_records`
  ADD PRIMARY KEY (`dead_record_id`),
  ADD KEY `breed_animal_id` (`breed_id`);

--
-- Indexes for table `knowledge_type`
--
ALTER TABLE `knowledge_type`
  ADD PRIMARY KEY (`knowledge_id`),
  ADD KEY `breed_id` (`breed_id`);

--
-- Indexes for table `product_records`
--
ALTER TABLE `product_records`
  ADD PRIMARY KEY (`product_record_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `animal_type`
--
ALTER TABLE `animal_type`
  MODIFY `animal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `breed_animal`
--
ALTER TABLE `breed_animal`
  MODIFY `breed_animal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `breed_technology`
--
ALTER TABLE `breed_technology`
  MODIFY `breed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `company_info`
--
ALTER TABLE `company_info`
  MODIFY `company_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contact_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `dead_records`
--
ALTER TABLE `dead_records`
  MODIFY `dead_record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `knowledge_type`
--
ALTER TABLE `knowledge_type`
  MODIFY `knowledge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `product_records`
--
ALTER TABLE `product_records`
  MODIFY `product_record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_info` (`company_id`);

--
-- Constraints for table `breed_animal`
--
ALTER TABLE `breed_animal`
  ADD CONSTRAINT `breed_animal_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal_type` (`animal_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `breed_animal_ibfk_2` FOREIGN KEY (`breed_id`) REFERENCES `breed_technology` (`breed_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `breed_technology`
--
ALTER TABLE `breed_technology`
  ADD CONSTRAINT `breed_technology_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal_type` (`animal_id`) ON UPDATE CASCADE;

--
-- Constraints for table `dead_records`
--
ALTER TABLE `dead_records`
  ADD CONSTRAINT `dead_records_ibfk_1` FOREIGN KEY (`breed_id`) REFERENCES `breed_technology` (`breed_id`);

--
-- Constraints for table `knowledge_type`
--
ALTER TABLE `knowledge_type`
  ADD CONSTRAINT `knowledge_type_ibfk_1` FOREIGN KEY (`breed_id`) REFERENCES `breed_technology` (`breed_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_records`
--
ALTER TABLE `product_records`
  ADD CONSTRAINT `product_records_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_type` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
