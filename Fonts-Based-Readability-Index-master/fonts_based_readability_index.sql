-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 17, 2014 at 07:29 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fonts_based_readability_index`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `email` varchar(200) CHARACTER SET latin1 NOT NULL,
  `password` varchar(200) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`email`, `password`) VALUES
('ashishsfb@gmail.com', '123'),
('sneha@gmail.com', '1234'),
('nikhiljn585@gmail.com', 'nikko');

-- --------------------------------------------------------

--
-- Table structure for table `main`
--

CREATE TABLE IF NOT EXISTS `main` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) CHARACTER SET latin1 NOT NULL,
  `age` int(11) NOT NULL,
  `gender` int(11) NOT NULL,
  `edu_back` varchar(200) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=8 ;

--
-- Dumping data for table `main`
--

INSERT INTO `main` (`user_id`, `email`, `age`, `gender`, `edu_back`) VALUES
(1, 'ashishsfb@gmail.com', 21, 1, 'higher_sec'),
(2, 'nikhiljn585@gmail.com', 21, 1, 'higher_sec'),
(3, 'parthredbull@gmail.com', 21, 1, 'higher_sec'),
(4, 'anishgoel1994@gmail.com', 19, 1, 'higher_sec'),
(5, 'sneha@gmail.com', 21, 0, 'higher_sec'),
(6, 'nikhiljn@gmail.com', 21, 1, 'higher_sec'),
(7, 'chalu@gmail.com', 21, 1, 'higher_sec');

-- --------------------------------------------------------

--
-- Table structure for table `paragraphs`
--

CREATE TABLE IF NOT EXISTS `paragraphs` (
  `pid` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `language` text NOT NULL,
  `article_type` varchar(50) NOT NULL,
  `para` text NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `paragraphs`
--

INSERT INTO `paragraphs` (`pid`, `language`, `article_type`, `para`) VALUES
(1, 'English', 'Newspaper', 'Effective text entrymethods are crucial for pleasant, fluent, and efficient use ofmany of\r\nthe computer systems that surround us. Due to various requirements, such as the small\r\nform factor of mobile devices, or a userâ€™s limited motor abilities, the pervasive full-sized\r\nQWERTY keyboard may not always be a feasible input device. As a result, a wide array of\r\ntext entry methods have been designed and evaluated using a variety of input modalities,\r\nsuch as single-switches, keypads, touchscreens, eye-trackers, accelerometers, and\r\njoysticks. Similar to other user interface techniques, text entry methods need to be evaluated\r\nin order for us to better understand and improve them. In this paper we show how\r\nshort composition style tasks can be used to help evaluate text entry methods. Such\r\ncomposition tasks can complement the traditional transcription task used in text\r\nentry evaluations.'),
(2, 'English', 'Newspaper', 'Under these assumptions, by applying our complete model to the COCA (as described\r\nin Section 3.5.1), we compute an estimate of ultimate text entry speed at 29.0wpm.\r\nHowever, if a user potentially gets to the point where he or she can enter text without\r\nany reliance on audio feedback, then both acknowledgment time and selection time\r\nbecome negligible. In this case, we predict ultimate entry speed of 40.7wpm. This lack\r\nof reliance on audio feedback may be more readily achieved by allowing an entire phrase\r\nto be entered before disambiguation occurs and using improved disambiguation with a\r\nmore complex language model. The results presented in this work show the potential of the Rotext system and of alphabets designed to support continuous ambiguous input in general. There are many future areas to explore in both of these domains.\r\nThe continuous disambiguation applied here can readily be applied to improve a\r\nvariety of selection-based text entry techniques, with the requirement being that one\r\ncan characterize an expected miss model. The simple one-dimensional miss models\r\npresented here can readily be extrapolated to higher-dimensional cases, such as with\r\nimprecise conventional touchscreen typing.\r\nIt would be useful to more fully characterize the sensitivity of both alphabet design\r\nand disambiguation performance to miss model assumptions. Users can always choose\r\nto operate anywhere within a speedâ€“accuracy tradeoff; at each different point within\r\nthis tradeoff the user makes use of a different miss model. A disambiguation system\r\ncould account for temporal aspects of text entry to adjust the assumed miss model\r\nbased on the speed of entry. Even more complex and worthy of further thought is the\r\nhumanâ€“computer dynamic that involves the user adapting his or her level of speedâ€“\r\naccuracy tradeoff to accommodate the precision expected by the computer.'),
(3, 'English', 'Research Papers', 'We present a new method in image segmentation\r\nthat is based on Otsuâ€™s method but iteratively searches for\r\nsubregions of the image for segmentation, instead of treating the\r\nfull image as a whole region for processing. The iterative method\r\nstarts with Otsuâ€™s threshold and computes the mean values of\r\nthe two classes as separated by the threshold. Based on the\r\nOtsuâ€™s threshold and the two mean values, the method separates\r\nthe image into three classes instead of two as the standard\r\nOtsuâ€™s method does. The first two classes are determined as\r\nthe foreground and background and they will not be processed\r\nfurther. The third class is denoted as a to-be-determined (TBD)\r\nregion that is processed at next iteration. At the succeeding\r\niteration, Otsuâ€™s method is applied on the TBD region to calculate\r\na new threshold and two class means and the TBD region is again\r\nseparated into three classes, namely, foreground, background,\r\nand a new TBD region, which by definition is smaller than the\r\nprevious TBD regions. Then, the new TBD region is processed in\r\nthe similar manner. The process stops when the Otsuâ€™s thresholds\r\ncalculated between two iterations is less than a preset threshold.\r\nThen, all the intermediate foreground and background regions\r\nare, respectively, combined to create the final segmentation result.'),
(4, 'English', 'Research Papers', 'PARA 2\r\nThe Goal Programming (GP) approach is used to\r\nmodel problems of Pattern classification. It involves finding the\r\nseparating boundary lines between different classes to get\r\nminimum misclassification. A theoretical overview of solving\r\nthe problem using GP is discussed and its different variants are\r\napplied to various datasets to show the effectiveness of the\r\nalgorithm. The datasets considered for experimentation are\r\ntaken to be in 2 â€“ dimensional Euclidean space for better\r\nvisualization of separating boundaries.'),
(5, 'English', 'Research Papers', 'The support vector machine (SVM) has been demonstrated\r\nto be a very effective classifier in many applications, but\r\nits performance is still limited as the data distribution information\r\nis underutilized in determining the decision hyperplane. Most of\r\nthe existing kernels employed in nonlinear SVMs measure the similarity\r\nbetween a pair of pattern images based on the Euclidean\r\ninner product or the Euclidean distance of corresponding input\r\npatterns, which ignores data distribution tendency and makes the\r\nSVM essentially a â€œlocalâ€ classifier. In this paper, we provide a\r\nstep toward a paradigm of kernels by incorporating data specific\r\nknowledge into existing kernels. We first find the data structure\r\nfor each class adaptively in the input space via agglomerative hierarchical\r\nclustering (AHC), and then construct the weighted Mahalanobis\r\ndistance (WMD) kernels using the detected data distribution\r\ninformation. In WMD kernels, the similarity between two\r\npattern images is determined not only by the Mahalanobis distance\r\n(MD) between their corresponding input patterns but also by the\r\nsizes of the clusters they reside in. Although WMD kernels are not\r\nguaranteed to be positive definite (pd) or conditionally positive definite\r\n(cpd), satisfactory classification results can still be achieved\r\nbecause regularizers in SVMs with WMD kernels are empirically\r\npositive in pseudo-Euclidean (pE) spaces.'),
(6, 'English', 'Research Papers', 'Cluster analysis is often one of the first steps in the analysis\r\nof data. As such, it is an effort at unsupervised learning usually\r\nin the context of very little a priori knowledge. Therefore, the\r\nrequirement that a user supply an analysis system with parameter\r\nvalues, such as minimum acceptable cluster distance or\r\nminimum acceptable standard deviation, knowledge of which\r\npresumes previous study of the data, is a major detriment of\r\nsuch systems. In fact, as Chen points out, "a\r\ncommon drawback to all cluster algorithms is that their performance\r\nis highly dependent on the user setting various\r\nparameters. In fact, the "proper" setting usually can only\r\nbe determined by a trial and error method."'),
(7, 'English', 'Research Papers', 'People often encounter a diverse range of context-sensitive information needs in their\r\nday-to-day lives. Deciding which movie to see in the cinema with friends, finding the\r\nlocation of a nearby restaurant, learning the latest football results, and getting information\r\non the time of the next bus home are all examples of the needs we encounter\r\nevery day. At times the information desired is essential to the task at hand, and in\r\nmany cases, people require assistance to address the need in question. Technology has\r\nbecome one such source of assisting people in coping with their daily information needs.\r\nOver the past 10 years, we have witnessed huge advances in mobile technology\r\nin particular. Improvements in mobile networks, the growing popularity of mobile\r\napplications, and significant advances in mobile handset technology have contributed\r\nto a dramatic increase in the use of these portable communications devices to access a\r\nwealth of information while on the move.1 This shift in how users access and consumecontent through their mobile devices is having a profound impact on how users address\r\ntheir daily information needs.\r\nSeveral recent studies have highlighted interesting insights into the types of information\r\nneeds that occur in mobile settings. These studies have helped us understand\r\nhuman information needs across a variety of contexts and have pointed to a number of\r\nimportant implications for future mobile services. However, the majority of these studies\r\nare relatively small in terms of scope, scale, and duration, and many open research\r\nquestions remain.\r\nThe goal of this work is to provide a fundamental understanding of daily information\r\nneeds through a large-scale, in-depth, quantitative investigation conducted in situ.\r\nLarge-scale observational studies can shed light on unanswered questions regarding\r\nwhy information needs arise, the challenges people face in addressing their information\r\nneeds, and how external factors influence peopleâ€™s daily needs. These insights could\r\npoint to new opportunities for future information-based services. To this end, we conducted\r\none of the most comprehensive studies of information needs to date, spanning\r\na 3-month period in Spain, involving more than 100 users, and covering daily information\r\nneeds generated in both mobile and nonmobile settings. Our study employed a\r\ncontextual Experience Sampling Method (ESM), a snippet-based diary technique using\r\nSMS, and an online diary to gain insights into the types of needs that arise on\r\na day-to-day basis and how those needs are addressed.'),
(8, 'English', 'Newspaper', 'SAO PAULO: A confident Croatia is ready to spoil Brazil''s party in their World Cup opener and continue a long tradition of first match upsets at the tournament.\nWith all the pressure on the host team, Croatia coach Niko Kovac said Wednesday that his team can deliver a "historic result" in Thursday''s match in Sao Paulo.\nKovac acknowledged that Brazil is the clear favorite in the Group A game, but added that few teams would like to face his squad either.\n"They are not going to have it easy against Croatia," he said at Itaquerao Stadium. "We are a tricky side and I am sure that we will show that and demonstrate that tomorrow."\nEven a draw would significantly enhance Croatia''s chances of advancing from a group that also includes Mexico and Cameroon.\nIt would also deliver a blow to Brazil - a clear tournament favorite - and join the list of opening game upsets that include Belgium''s defeat of defending champion Argentina in 1982 and holder Italy being held to a 1-1 draw by Bulgaria four years later.'),
(9, 'English', 'Newspaper', 'Efforts to make the bus commute from the northern beaches faster risk impeding other motorists and punishing residents closer to the city, critics have warned.\nPremier Mike Baird has confirmed that Tuesdayâ€™s budget will contain transport measures to speed up interminable bus journeys from Mona Vale to the city, a 28-kilometre trip which can take up to 90 minutes.\nMr Baird also detailed on Thursday the preliminary cost of the north and south extensions to the planned WestConnex motorway, which would be funded by the partial sale of the stateâ€™s electricity assets.\nThe two proposed spurs, which would create a motorway link from southern Sydney through to the Anzac Bridge, were â€˜â€˜in the ballparkâ€™â€™ of $1.5 billion each â€“ subject to the findings of a feasibility study.\nâ€˜â€˜We will be detailing the final costing as we get to November,â€™â€™ he said.'),
(10, 'English', 'Newspaper', 'The brazen Karachi airport attack clearly suggests the role of the Pakistani Talibanâ€™s sleeper cells in the city that must have planned it during the interval when their central leadership was busy talking peace with the government, a media report said on Thursday.\r\nBackground interviews with current and former intelligence and law enforcement officials revealed that real success against militants was not possible till the elimination of their sleeper cells. The cells provided shelter, weapons, explosives, transport and even intelligence to their comrades responsible for carrying out attacks, the Dawn reported.'),
(11, 'English', 'NCERT Text', 'Brasilia:oye Neymar''s agent Wagner Ribeiro described Brazil''s coach Luiz Felipe Scolari as an "old jerk" after the Selecao lost 1-7 at the hands of Germany in the World Cup semi-final Tuesday. (Also read: Brazil''s pride has taken a hammer blow, says Diego Maradona)\n"One - being Portugal''s coach and winning nothing, two - going to Chelsea and being sacked the following day, three - going to coach in Uzbekistan, four - returning to Brazil, taking over a big team (Palmeiras) and getting them relegated to second division. Five - leaving the club 56 days before the end of the season to escape the relegation. Six - being an old jerk, arrogant, repulsive, conceited and ridiculous," Ribeiro tweeted, referring to Scolari.'),
(12, 'English', 'NCERT Text', 'Brasilia, Brazil: Neymar has urged his Brazil teammates to rescue some of their battered pride after their humiliating World Cup semifinal defeat to Germany by beating the Netherlands in Saturday''s third-place play-off in Brasilia. (Also read: Neymar breaks down in tears)\r\nIt is the one game that no team ever wants to take part in, but the encounter at the Mane Garrincha National Stadium took on extra significance after the hosts'' dream of lifting the trophy at the Maracana 24 hours later was ended by their record 7-1 loss against the Germans.'),
(13, 'English', 'NCERT Text', 'The mood in the Dutch camp is the same. The Oranje have a day less to prepare for the match after their agonising defeat on penalties to Argentina in Wednesday''s second semifinal in Sao Paulo.\r\nHowever, in contrast to Brazil, Holland will be able to look back on a campaign which started with a 5-1 thumping of Spain as a success.\r\n"We have had a fantastic tournament. Nobody expected us to get beyond the group stage," said coach Louis van Gaal, who must motivate himself for his final match at the helm before he takes over at Manchester United.'),
(15, 'Hindi', 'Legal Document', 'मैंने ख़ुदा से एक छोटी सी दुआ मांगी दुआ में उससे अपनी मौत मांगी \r\n \r\nख़ुदा ने कहा बेशक मैं तुझे मौत दे दूं पर उसे क्या दूं जिसने तेरी लंबी उमर की दुआ मांगी'),
(16, 'English', 'Legal Document', 'kya hai be lodu'),
(17, 'englsih', 'Newspaper', 'ggggdfgfdgfd'),
(18, 'englsih', 'Newspaper', 'ggggdfgfdgfd');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `qid` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(2) unsigned NOT NULL,
  `ques` text NOT NULL,
  `multi_correct` text NOT NULL,
  `opt1` text NOT NULL,
  `opt2` text NOT NULL,
  `opt3` text NOT NULL,
  `opt4` text NOT NULL,
  PRIMARY KEY (`qid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`qid`, `pid`, `ques`, `multi_correct`, `opt1`, `opt2`, `opt3`, `opt4`) VALUES
(1, 1, 'With the exception of a speech recognition study by Karat et al.\r\n[1999], composition tasks have rarely been used in text entry evaluations', 'FFFF', 'p1q1a1', 'p1q1a2', 'p1q1a3', 'p1q1a4'),
(2, 2, 'It would be\r\ninteresting to explore how a disambiguation system could over time adapt to a user,\r\nwhile simultaneously accounting for the adapting of the user to the system.', 'FFFF', 'p2q1a1', 'p2q1a2', 'p2q1a3', 'p2q1a4'),
(3, 3, 'Tests on synthetic and real images showed that the new iterative\r\nmethod can achieve better performance than the standard Otsuâ€™s\r\nmethod in many challenging cases, such as identifying weak\r\nobjects and revealing fine structures of complex objects while\r\nthe added computational cost is minimal.', 'FFFF', 'p1q1a1', 'p1q1a2', 'p1q1a3', 'p1q1a4'),
(4, 4, 'Finally, the results are\r\ncompared with the K-Nearest Neighbor Classifier', 'FFFF', 'p2q1a1', 'p2q1a2', 'p2q1a3', 'p2q1a4'),
(5, 5, 'Experimental results on both synthetic and real-world data sets show the effectiveness of\r\nâ€œpluggingâ€ data structure into existing kernels.', 'FFFF', 'p3q1a1', 'p3q1a2', 'p3q1a3', 'p3q1a4'),
(6, 6, 'If incorporated into a cluster seeking algorithm, the measure presented here\r\nsubstantially overcomes this difficulty by requiring the user\r\nto specify only the p and q exponents, which is equivalent\r\nto requiring the user to specify only the distance and dispersion\r\nmeasures to be used.', 'FFFF', 'p4q1a1', 'p4q1a2', 'p4q1a3', 'p4q1a4'),
(7, 7, 'The study resulted in almost\r\n12,000 SMS snippets and more than 9,000 associated diary entries', 'FFFF', 'p5q1a1', 'p5q1a2', 'p5q1a3', 'p5q1a4'),
(8, 8, 'Cameroon then defeated defending champion Argentina in 1990 while reigning champion France lost in 2002 to a Senegal side making its first appearance at the World Cup.', 'FFFF', 'p1q1a1', 'p1q1a2', 'p1q1a3', 'p1q1a4'),
(9, 9, 'Next weekâ€™s budget will fund a $5 million feasibility study into building a tunnel under Military Road, a longer-term prospect which would enable city-bound vehicles to avoid the notorious bottleneck.', 'FFFF', 'p2q1a1', 'p2q1a2', 'p2q1a3', 'p2q1a4'),
(10, 10, 'The report said the attack on the airport was a work of planning that must have begun weeks if not months before its execution and it appeared the TTP leadership was using the time it had bought through a ceasefire to put the finishing touches to the planning of the airport attack.', 'FFFF', 'p3q1a1', 'p3q1a2', 'p3q1a3', 'p3q1a4'),
(11, 8, 'SAU PAULO Q 2', 'FFFF', 'ANS 1', 'ANS 2', 'ANS 3', 'ANS 4'),
(12, 11, 'Ribeiro is also Lucas Moura''s agent, whom Scolari did not pick for the World Cup.', 'TTFF', 'Rooney', 'Ronaldo', 'Messi', 'Kaka'),
(13, 12, 'Neymar missed that match after fracturing a bone in his back in the quarterfinal win against Colombia, but the 22-year-old superstar faced the media on Thursday as he called for Brazil to bow out on a high. (Also read: Neymar''s agent calls Scolari ''old jerk'')', 'FFFF', 'A', 'B', 'C', ''),
(14, 13, 'Who''s ur fav soccer player ?', '0000', '0', '0', '0', '0'),
(15, 14, 'jain', 'FFFF', 'a', 'b', 'c', 'd'),
(16, 15, 'सही में ?', 'FFFF', 'हाँ', 'नहीं', 'पता नहीं', 'पापा से पूछ के बताता हूँ'),
(17, 15, 'आपने किस्से दुआ मांगी ?', 'FFFF', 'खुदा', 'खुदसे', 'तुझसे', 'मज्जू से'),
(18, 15, 'किस किस ने दुआ मांगी', 'FTTF', 'खुदा', 'तूने', 'तेरी प्रेमिका ने', 'मैंने'),
(19, 15, 'क्या दुआ मांगी ?', 'FTFF', 'जीने की', 'मरने की', 'चोकलेट खाने की', 'टट्टी जाने की'),
(20, 15, 'दुआ क्या होती है ?', '0000', '0', '0', '0', '0'),
(21, 15, 'किसने किससे दुआ मांगी ?', '0000', '0', '0', '0', '0'),
(22, 16, 'q1', 'TTFF', 'a1', 'a2', 'A3', 'A4'),
(23, 16, 'q2', 'TTFF', 'a1', 'a2', 'a3', 'a4');

-- --------------------------------------------------------

--
-- Table structure for table `test_data`
--

CREATE TABLE IF NOT EXISTS `test_data` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `font` varchar(20) NOT NULL,
  `size` int(11) NOT NULL,
  `line_height` int(11) NOT NULL,
  `word_spacing` int(11) NOT NULL,
  `reading_time` double NOT NULL,
  `test_time` double NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `test_data`
--

INSERT INTO `test_data` (`tid`, `uid`, `pid`, `font`, `size`, `line_height`, `word_spacing`, `reading_time`, `test_time`) VALUES
(1, 5, 10, 'Times New Roman', 100, 23, 0, 12.16, 4.16),
(2, 5, 5, 'Comic Sans MS', 120, 23, 0, 1.654, 4.96),
(3, 5, 8, 'Lucida Sans', 100, 23, 0, 1.384, 7.03),
(4, 5, 15, 'Calibri', 140, 23, 0, 6.964, 36.66),
(5, 5, 15, 'Lucida Sans', 160, 23, 0, 2.448, 31.99),
(6, 1, 15, 'Lucida Sans', 120, 23, 0, 2.404, 42.33),
(7, 5, 15, 'Times New Roman', 140, 23, 0, 0.995, 28.28),
(8, 1, 9, 'Lucida Sans', 200, 23, 0, 1.93, 8.55),
(9, 1, 13, 'Lucida Sans', 200, 31, 0, 3.111, 20.43),
(10, 1, 14, 'Lucida Sans', 180, 23, 14, 2.128, 3.78),
(11, 1, 10, 'Lucida Sans', 120, 23, 0, 1.766, 3.14),
(12, 4, 2, 'Lucida Sans', 200, 23, 0, 5.544, 4.79),
(13, 1, 2, 'Times New Roman', 230, 25, 0, 4.062, 10.33),
(14, 1, 9, 'Arial', 170, 23, 0, 4.201, 8.37);

-- --------------------------------------------------------

--
-- Table structure for table `test_questions_data`
--

CREATE TABLE IF NOT EXISTS `test_questions_data` (
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `qid` int(11) NOT NULL,
  `selected_option` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `test_questions_data`
--

INSERT INTO `test_questions_data` (`tid`, `uid`, `qid`, `selected_option`) VALUES
(1, 5, 10, 'p3q1a3'),
(2, 5, 5, 'p3q1a4'),
(3, 5, 8, 'p1q1a4'),
(3, 5, 11, 'ANS 2'),
(4, 5, 16, 'नहीं'),
(4, 5, 17, 'तुझसे'),
(4, 5, 18, 'मैंने'),
(4, 5, 19, 'चोकलेट खाने की'),
(4, 5, 20, 'koi bhi baat nahi boli'),
(4, 5, 21, 'ice cream ki boli thi'),
(5, 5, 16, 'पता नहीं'),
(5, 5, 17, 'मज्जू से'),
(5, 5, 18, 'मैंने'),
(5, 5, 18, 'तूने'),
(5, 5, 19, 'जीने की'),
(5, 5, 19, 'मरने की'),
(5, 5, 20, 'kya kar diya'),
(5, 5, 21, 'tumne humne'),
(6, 5, 16, 'हाँ'),
(6, 1, 17, 'खुदा'),
(6, 1, 18, 'खुदा'),
(6, 1, 18, 'तेरी प्रेमिका ने'),
(6, 1, 19, 'जीने की'),
(6, 1, 19, 'टट्टी जाने की'),
(6, 1, 20, 'why '),
(6, 1, 21, 'u do so'),
(7, 5, 16, 'हाँ'),
(7, 5, 17, 'मज्जू से'),
(7, 5, 18, 'तूने'),
(7, 5, 19, 'चोकलेट खाने की'),
(7, 5, 20, 'jid kari'),
(7, 5, 21, 'par koi naa'),
(8, 0, 9, 'p2q1a2'),
(9, 0, 14, 'Rooney Wayne'),
(10, 0, 15, 'c'),
(11, 0, 10, 'p3q1a3'),
(12, 0, 2, 'p2q1a2'),
(13, 0, 2, 'p2q1a2'),
(14, 0, 9, 'p2q1a3');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
