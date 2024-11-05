<?php
	$themeData['ads_300'] = getADS('300x250_main');
	$themeData['ads_top'] = getADS('728x90_main');
	$date =  date('Ymdms');
	$date = strtotime($date);
	$themeData['cms'] = "<script src='https://api.gamemonetize.com/cms.js?". $date . "'></script>";

	$newGames_query = $GameMonetizeConnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY date_added desc, featured_sorting desc LIMIT 65");
	$ngm_r = '';
	$ids = '';
	while ($newGames = $newGames_query->fetch_array()) {
		$newGame_data = gameData($newGames);
		$themeData['new_game_url'] = $newGame_data['game_url'];
		$themeData['new_game_image'] = $newGame_data['image_url'];
		$themeData['new_game_name'] = $newGame_data['name'];
		$themeData['new_game_video_url'] = $newGames['video_url'];
		
		$themeData['new_game_featured'] = $newGame_data['featured'];

		$ids .= $newGames['game_id'] .',';

		$ngm_r .= \GameMonetize\UI::view('game/list-each/new-games-list');
	}

	 if ($_GET['p'] == 'home') {
        $cat=$_GET["cat"];
            if($cat<>""){
            $cat = str_replace('-', '.', $cat); 
            $cat = ucfirst($cat);
			$themeData['tag_name'] = '<div class="category-section-top" style="text-align:center;font-size:20px;margin-bottom:10px;margin-top:0px;">
	<h1 style="color:#fc0;height: inherit;line-height: inherit;font-size: inherit;text-indent: inherit;font-size:29px;line-height: 25px;">'. $cat .'</h2>
	<h2 style="color:#000;font-size:14px;margin-top:15px;">Play '. $cat .' Free Online at GameFree.Games! We have chosen best '. $cat .' games which you can play online for free. enjoy!</h2>
</div>

';
		}
	}

	$themeData['new_game_ids'] .= rtrim($ids, ',');
	$themeData['new_game_page'] = "games";

	$themeData['new_games_list'] = $ngm_r;

	$footer_description = getFooterDescription('home');

	$themeData['footer_description'] = isset($footer_description->description) ? htmlspecialchars_decode($footer_description->description): "";;
	$themeData['footer_description_has_content'] = isset($footer_description->has_content) ? $footer_description->has_content: "";
	$themeData['footer_description_content_value'] = isset($footer_description->content_value) ? $footer_description->content_value: "";

	// get all slider games
	$all_slider_container = "";
	$slider_data = $GameMonetizeConnect->query("SELECT * FROM ".SLIDERS." ORDER BY ordering ASC");
	$index = 1;
	while ($slider = $slider_data->fetch_array()) {
		if($slider['type'] == 'new'){
			// new games
			$all_splide_item = "";
			$newGames_query = $GameMonetizeConnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY date_added desc, featured_sorting desc LIMIT 20");
		
			while ($newGames = $newGames_query->fetch_array()) {
				$newGame_data = gameData($newGames);
				$themeData['splide_item_url'] = $newGame_data['game_url'];
				$themeData['splide_item_image'] = $newGame_data['image_url'];
				$themeData['splide_item_title'] = $newGame_data['name'];
				$themeData['splide_item_video_url'] = $newGames['video_url'];
			
				$all_splide_item .= \GameMonetize\UI::view('game/splide_item');
			}
		
			$themeData['splide_header_id'] = $index;
			$themeData['splide_header_title'] = 'New Games';
			$themeData['splide_header_url'] = siteUrl() . "/new-games";
		
			$themeData['splide_items'] = $all_splide_item;
		
			$all_slider_container .= \GameMonetize\UI::view('game/splide_container');
		}

		if($slider['type'] == 'best'){
			// new games
			$all_splide_item = "";
			$games_query = $GameMonetizeConnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY plays DESC LIMIT 20");
		
			while ($newGames = $games_query->fetch_array()) {
				$newGame_data = gameData($newGames);
				$themeData['splide_item_url'] = $newGame_data['game_url'];
				$themeData['splide_item_image'] = $newGame_data['image_url'];
				$themeData['splide_item_title'] = $newGame_data['name'];
				$themeData['splide_item_video_url'] = $newGames['video_url'];
			
				$all_splide_item .= \GameMonetize\UI::view('game/splide_item');
			}
		
			$themeData['splide_header_id'] = $index;
			$themeData['splide_header_title'] = 'Best Games';
			$themeData['splide_header_url'] = siteUrl() . "/best-games";
		
			$themeData['splide_items'] = $all_splide_item;
		
			$all_slider_container .= \GameMonetize\UI::view('game/splide_container');
		}
		
		if($slider['type'] == 'featured'){
			// new games
			$all_splide_item = "";
			$games_query = $GameMonetizeConnect->query("SELECT * FROM ".GAMES." WHERE published='1' AND featured='1' ORDER BY date_added DESC LIMIT 20");
		
			while ($newGames = $games_query->fetch_array()) {
				$newGame_data = gameData($newGames);
				$themeData['splide_item_url'] = $newGame_data['game_url'];
				$themeData['splide_item_image'] = $newGame_data['image_url'];
				$themeData['splide_item_title'] = $newGame_data['name'];
				$themeData['splide_item_video_url'] = $newGames['video_url'];
			
				$all_splide_item .= \GameMonetize\UI::view('game/splide_item');
			}
		
			$themeData['splide_header_id'] = $index;
			$themeData['splide_header_title'] = 'Featured Games';
			$themeData['splide_header_url'] = siteUrl() . "/featured-games";
		
			$themeData['splide_items'] = $all_splide_item;
		
			$all_slider_container .= \GameMonetize\UI::view('game/splide_container');
		}

		if($slider['type'] == 'played'){
			// new games
			$all_splide_item = "";
			$fav = explode(',,', $_COOKIE['playedgames']);
			// remove empty values from $fav
			if (strlen($_COOKIE['playedgames']) > 0) {
				foreach ($fav as $game_id) {
					$resultset[] = $game_id;
				}
				$string = implode(",", $resultset);
				$str = trim($string, ",");
				$comma_separated = rtrim($str, ',');
				$games_query = $GameMonetizeConnect->query("SELECT * FROM " . GAMES . " where `game_id` IN (" . $comma_separated . ") order by date_added DESC LIMIT 20");
			
		
				while ($newGames = $games_query->fetch_array()) {
					$newGame_data = gameData($newGames);
					$themeData['splide_item_url'] = $newGame_data['game_url'];
					$themeData['splide_item_image'] = $newGame_data['image_url'];
					$themeData['splide_item_title'] = $newGame_data['name'];
					$themeData['splide_item_video_url'] = $newGames['video_url'];
				
					$all_splide_item .= \GameMonetize\UI::view('game/splide_item');
				}
			
				$themeData['splide_header_id'] = $index;
				$themeData['splide_header_title'] = 'Played Games';
				$themeData['splide_header_url'] = siteUrl() . "/played-games";
			
				$themeData['splide_items'] = $all_splide_item;
			
				$all_slider_container .= \GameMonetize\UI::view('game/splide_container');
			}
		}

		if($slider['type'] == 'category'){
			// new games
			$all_splide_item = "";
			$category_id = $slider['category_tags_id'];
			$games_query = $GameMonetizeConnect->query("SELECT * FROM ".GAMES." WHERE category = '{$category_id}' AND published = '1' ORDER BY featured DESC LIMIT 20");
		
			while ($newGames = $games_query->fetch_array()) {
				$newGame_data = gameData($newGames);
				$themeData['splide_item_url'] = $newGame_data['game_url'];
				$themeData['splide_item_image'] = $newGame_data['image_url'];
				$themeData['splide_item_title'] = $newGame_data['name'];
				$themeData['splide_item_video_url'] = $newGames['video_url'];
			
				$all_splide_item .= \GameMonetize\UI::view('game/splide_item');
			}

			$category_query = $GameMonetizeConnect->query("SELECT * FROM ".CATEGORIES." WHERE id='{$category_id}'");
			$category_data = $category_query->fetch_array();
		
			$themeData['splide_header_id'] = $index;
			$themeData['splide_header_title'] = "{$category_data['name']}";
			$themeData['splide_header_url'] = siteUrl() . "/category/{$category_data['category_pilot']}";
		
			$themeData['splide_items'] = $all_splide_item;
		
			$all_slider_container .= \GameMonetize\UI::view('game/splide_container');
		}

		if($slider['type'] == 'tags'){
			// new games
			$all_splide_item = "";
			$tags_id = $slider['category_tags_id'];
			$games_query = $GameMonetizeConnect->query("SELECT * FROM ".GAMES." WHERE tags_ids LIKE '%\"{$tags_id}\"%' AND published = '1' ORDER BY featured DESC LIMIT 20");
		
			while ($newGames = $games_query->fetch_array()) {
				$newGame_data = gameData($newGames);
				$themeData['splide_item_url'] = $newGame_data['game_url'];
				$themeData['splide_item_image'] = $newGame_data['image_url'];
				$themeData['splide_item_title'] = $newGame_data['name'];
				$themeData['splide_item_video_url'] = $newGames['video_url'];
			
				$all_splide_item .= \GameMonetize\UI::view('game/splide_item');
			}

			$tags_query = $GameMonetizeConnect->query("SELECT * FROM ".TAGS." WHERE id='{$tags_id}'");
			$tags_data = $tags_query->fetch_array();
		
			$themeData['splide_header_id'] = $index;
			$themeData['splide_header_title'] = "{$tags_data['name']}";
			$themeData['splide_header_url'] = siteUrl() . "/tag/{$tags_data['url']}";
		
			$themeData['splide_items'] = $all_splide_item;
		
			$all_slider_container .= \GameMonetize\UI::view('game/splide_container');
		}

		$index++;
	}

	$sql_cat_query = $GameMonetizeConnect->query("SELECT * FROM " . CATEGORIES . " WHERE show_home='1'");
	$ct_r = '';
	while ($category = $sql_cat_query->fetch_array()) {
		$themeData['category_id'] = $category['id'];
		$themeData['category_name'] = $category['name'];
		$themeData['category_image'] = $category['image'];

		$numbergames = $GameMonetizeConnect->query("SELECT COUNT(*) FROM " . GAMES . " where category=" . $category['id']);
		$numbergames = $numbergames->fetch_array()[0];

		$themeData['category_number'] = $numbergames;
		$themeData['category_url'] = siteUrl() . '/category/'	. slugify($category['name']);
		$ct_r .= \GameMonetize\UI::view('category/categories-list-home');
	}

	$themeData['categories_list_home'] = $ct_r;
	$themeData['category_content'] = \GameMonetize\UI::view('category/categories-list-home');

	$sql_tag_query = $GameMonetizeConnect->query("SELECT * FROM " . TAGS . " WHERE show_home='1'");
	$tag_r = '';
	while ($tag = $sql_tag_query->fetch_array()) {
		$themeData['tag_id'] = $tag['id'];
		$themeData['tag_name'] = $tag['name'];

		$themeData['tag_url'] = siteUrl() . '/tag/'	. slugify($tag['name']);
		$tag_r .= \GameMonetize\UI::view('tags/tags-list-home');
	}

	$themeData['tags_list_home'] = $tag_r;


	// Get setting data
	$settingDataQuery = "SELECT * FROM " . SETTING . " LIMIT 1";
	$settingData = $GameMonetizeConnect->query($settingDataQuery);
	$settingData = $settingData->fetch_array();

	if ($settingData["is_sidebar_enabled"]) {
		$themeData['categories_tags_home'] = "";
	} else {
		$themeData['categories_tags_home'] = \GameMonetize\UI::view('home/categories-tags-home');
	}

	// $themeData['categories_tags_home'] = \GameMonetize\UI::view('home/categories-tags-home');

	$themeData['all_splide_containers'] = $all_slider_container;
	$themeData['new_games'] = \GameMonetize\UI::view('game/new-games');

	$themeData['page_content'] = \GameMonetize\UI::view('home/content');
