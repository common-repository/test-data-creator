<?php

/*
Plugin Name: Test Data Creator
Plugin URI: http://skyhookmarketing.com
Description: Quickly add all the test data you need. Use the default options to quickly insert a bunch of posts, or tweak them for more control. Perfect for WP developers and theme creators. Planned updates include more data types (pages, comments, authors, etc), even more control over variables, and more html tags for fully testing your themes!.
Author: Josh Nichols
Version: 0.7
Author URI: http://skyhookmarketing.com
*/

add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu() {
  add_management_page('Test Data Creator', 'Test Data Creator', 'administrator', 'test-data-creator', 'setup_tdc');
}

function setup_tdc() {
	if($_POST['tdc_num_posts']){
		// set up vars
		$numPosts = $_POST['tdc_num_posts'];
		$dayRange = $_POST['tdc_num_days'];
		$comments = false;
		$authors = array(1);
		$numCats = $_POST['tdc_num_cats'];
		$categories = array(1);
		$numTags = $_POST['tdc_num_tags'];
		$alltags = array();
		$allwords = explode(' ', $_POST['tdc_string']);
		
		// generate categories
		for($i=1; $i<=$numCats; $i++){
			$cat_name = '';
			$title = rand(1, 3);
			for($t=1; $t<=$title; $t++){
				$cat_name .= $allwords[rand(1, count($allwords))].' ';
			}
			$cat_name = ucfirst(strtolower(substr($cat_name, 0, -1)));
			$newcat = wp_create_category($cat_name);
			$categories[] = $newcat;
		}
		
		// generate tags
		for($i=1; $i<=$numTags; $i++){
			$tag_name = $allwords[rand(1, count($allwords))].' ';
			$tag_name = ucfirst(strtolower(substr($tag_name, 0, -1)));
			$alltags[] = $tag_name;
		}
		
		//loop to insert posts
		for($i=1; $i<=$numPosts; $i++){
			
			// generate post_date
			$daysago = rand(0, $dayRange);
			$tdc_date = strtotime('-'.$daysago.' day') ;
			$tdc_date = date('Y-m-d H:i:s', $tdc_date);
			
			// generate post_tags
			$tdc_tags = '';
			foreach($alltags as $tag){
				$bit = rand(0,1);
				if($bit) $tdc_tags .= $tag.', ';
			}
			
			// generate post_content
			$tdc_post_content = '<p>';
			$paragraphs = rand(1, 10);
			for($p=1; $p<=$paragraphs; $p++){
				$words = rand(20, 100);
				for($w=1; $w<=$words; $w++){
					$tdc_post_content .= $allwords[rand(1, count($allwords))].' ';
				}
				$tdc_post_content .= '</p><p>';
			}
			$tdc_post_content .= '</p>';
			$tdc_post_content = ucfirst(strtolower($tdc_post_content));
			
			// generate post_title
			$tdc_post_title = '';
			$title = rand(1, 15);
			for($t=1; $t<=$title; $t++){
				$tdc_post_title .= $allwords[rand(1, count($allwords))].' ';
			}
			$tdc_post_title = ucfirst(strtolower(substr($tdc_post_title, 0, -1)));
			
			// insert post
			$post = array(
				'post_title' => $tdc_post_title,
				'post_content' => $tdc_post_content,
				'post_date' => $tdc_date,
				'post_category' => array($categories[array_rand($categories)]),
				'post_status' => 'publish', // 'draft' | 'publish' | 'pending'
				'post_password' => '',
				'post_author' => 1,
				'comment_status' => '', // 'closed' or 'open'
				'tags_input' => $tdc_tags,
				'post_type' => 'post'
			);
			wp_insert_post($post);
		}
		echo '<div id="message" class="updated fade"><p><strong>'.__('Test data created!').'</strong></p></div>';
	}
	?>
	<div class="wrap">
    <div id="icon-edit-pages" class="icon32"><br /></div>
    <h2>Add Test Posts</h2>
    <div id="poststuff" class="ui-sortable">
    	<p>Use each of the sections below to create randomly generated test data including posts, pages, categories, tags, and authors.</p>
    	
      <div class="postbox">
	      <h3>Create Posts</h3>
        <div class="inside">
        	<form name="test-data-creator" method="post">
          <table class="form-table">
          <tr valign="top">
            <th scope="row"><label for="tdc_num_posts">How many posts do you want?</label></th>
            <td><div id="tdc_num_posts-e"></div><input type="text" class="regular-text" id="tdc_num_posts" name="tdc_num_posts"  value="<?php echo (empty($_POST['tdc_num_posts'])) ? '50' : $_POST['tdc_num_posts']; ?>" /><br />
            Create as many randomly generated posts as you need.</td>
          </tr>
          <tr valign="top">
            <th scope="row"><label for="tdc_num_cats">How many categories do you want?</label></th>
            <td><div id="tdc_num_cats-e"></div><input type="text" class="regular-text" id="tdc_num_cats" name="tdc_num_cats"  value="<?php echo (empty($_POST['tdc_num_cats'])) ? '6' : $_POST['tdc_num_cats']; ?>" /><br />
            Posts will be randomly added to one of as many categories as you need.</td>
          </tr>
          <tr valign="top">
            <th scope="row"><label for="tdc_num_days">How far back (in days) should the first post be published?</label></th>
            <td><div id="tdc_num_days-e"></div><input type="text" class="regular-text" id="tdc_num_days" name="tdc_num_days"  value="<?php echo (empty($_POST['tdc_num_days'])) ? '365' : $_POST['tdc_num_days']; ?>" /><br />
            Posts will be published on a random day in the past, up to the amount of days you put here. In other words, entering 365 here will publish your test posts within the last year.</td>
          </tr>
          <tr valign="top">
            <th scope="row"><label for="tdc_num_tags">How many tags do you want?</label></th>
            <td><div id="tdc_num_tags-e"></div><input type="text" class="regular-text" id="tdc_num_tags" name="tdc_num_tags"  value="<?php echo (empty($_POST['tdc_num_tags'])) ? '20' : $_POST['tdc_num_tags']; ?>" /><br />
            Posts will be tagged randomly out of a pool of total possible tags.</td>
          </tr>
          <!--<tr valign="top">
            <th scope="row"><label for="tdc_num_auths">How many authors do you want?</label></th>
            <td><div id="tdc_num_auths-e"></div><input type="text" class="regular-text" id="tdc_num_auths" name="tdc_num_auths"  value="<?php echo (empty($_POST['tdc_num_auths'])) ? '1' : $_POST['tdc_num_auths']; ?>" /><br />
            Posts will be randomly assigned to one of as many authors as you want.</td>
          </tr>-->
          <tr valign="top">
            <th scope="row"><label for="tdc_string">Text string</label></th>
            <td><div id="tdc_string-e"></div><textarea id="tdc_string" name="tdc_string" class="large-text" rows="10"></textarea><br />
						Or use one of these: <a href="#" onclick="change_text_string('lorem');">Lorem Ipsum</a>, <a href="#" onclick="change_text_string('oz');">Wizard of Oz</a>, <a href="#" onclick="change_text_string('alice');">Alice In Wonderland</a>, <a href="#" onclick="change_text_string('moby');">Moby Dick</a>, <a href="#" onclick="change_text_string('war');">The War of the Worlds</a>
						<br />
            This text will be broken up and used to create all randomly generated strings of varying lengths.</td>
          </tr>
          <tr valign="top">
            <th scope="row">&nbsp;</th>
            <td><input type="submit" class="button-primary" name="Submit" value="<?php _e('Add Test Posts!') ?>" /></td>
          </tr>
          </table>
          </form>
        </div>
      </div>
      
    </div>
  </div>
	<script type="text/javascript">
		change_text_string('lorem');
		function change_text_string(t){
			switch(t){
				case 'lorem' : jQuery("#tdc_string").html('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi vulputate tempus libero, quis placerat purus hendrerit accumsan. Ut dictum, neque eu gravida facilisis, eros arcu mattis sem, aliquam sagittis tellus dolor scelerisque mi. Donec ac nibh urna. Praesent vestibulum orci accumsan eros congue sodales. Mauris consequat luctus nunc id dignissim. Nunc vel enim libero, a fermentum nisl. Sed aliquam, magna ac facilisis pulvinar, magna lectus facilisis elit, vel dictum arcu metus sit amet ipsum. Fusce adipiscing dapibus lectus a vehicula. Pellentesque egestas dignissim odio nec pellentesque. Fusce semper sollicitudin lectus id imperdiet. Donec id adipiscing leo. Fusce ac accumsan magna. Donec accumsan sem id nisl malesuada faucibus. Aenean hendrerit commodo ornare. In dignissim eros augue, in suscipit est. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vivamus aliquam, lacus vitae iaculis ornare, turpis dui aliquet eros, a hendrerit lacus turpis ut erat. Fusce lorem urna, auctor et placerat pharetra, euismod eget sem. Ut mattis neque vitae libero convallis tincidunt. Sed feugiat, nisi at congue dignissim, ipsum lectus pretium mauris, et scelerisque est nisi at risus. Vivamus tincidunt nisl vitae neque luctus cursus. Nulla facilisi. Aenean porta euismod massa, nec laoreet felis venenatis sed. Aliquam dictum, ante et mollis luctus, lectus arcu ultrices ante, in pretium lorem orci viverra ante. Donec ligula nunc, faucibus sit amet eleifend nec, laoreet vel erat. Nam id neque vitae ante sollicitudin gravida. Aliquam erat volutpat. Duis in turpis nisl. Aliquam ligula est, rutrum id feugiat ac, imperdiet id libero. Aenean metus eros, varius eu bibendum id, gravida vel neque. Maecenas lacinia gravida justo at aliquam. Nullam porttitor ante sit amet risus suscipit facilisis. Quisque sed sagittis sem. Quisque ligula nunc, convallis quis volutpat vitae, vestibulum at est. Cras at lorem elit, ac accumsan magna. Duis non libero metus. Duis ultricies vestibulum euismod. Etiam gravida, ante et vulputate lobortis, nisl elit imperdiet tortor, sit amet imperdiet erat neque nec purus. Suspendisse potenti. Donec urna risus, egestas tincidunt consectetur a, tincidunt elementum nibh. Quisque in viverra lorem. Aliquam non nisl ac turpis gravida sagittis. Etiam est quam, rutrum eget lacinia sit amet, ultrices id quam. Donec feugiat consequat egestas. Nam pulvinar lectus eget dolor porta rhoncus. Aliquam erat volutpat. Donec vestibulum ullamcorper lectus faucibus dictum. Morbi felis turpis, euismod id pretium vitae, ultrices vel sapien. Quisque ultricies odio non magna ullamcorper id tempor tortor porttitor. Curabitur tincidunt, nulla sit amet porttitor mollis, lorem ipsum imperdiet dolor, sed semper mi nibh id tortor. Vivamus ligula magna, sollicitudin in condimentum vel, pellentesque vel neque. Ut dignissim rhoncus tellus sed placerat. Curabitur massa turpis, elementum eu lacinia et, eleifend pellentesque massa. Nulla facilisi. Fusce et consectetur nibh. Vestibulum feugiat erat ac libero pellentesque lacinia. In mattis cursus purus, at accumsan ante venenatis sed. Phasellus eu eros orci, id aliquet nisl. Sed sit amet arcu augue, a fermentum nisl. Curabitur eget mi quis risus elementum tincidunt in vel ante. Suspendisse eu tellus sem. Etiam at metus ac eros rutrum sodales sed in metus. Duis mattis justo non magna iaculis iaculis at sit amet turpis. Cras arcu nisi, blandit vel tempus id, pulvinar vitae tortor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vestibulum posuere ornare augue eget iaculis. Mauris rhoncus odio massa, in porttitor massa. Maecenas ut quam nisi, vel tincidunt tortor. Nullam ligula mauris, luctus sit amet ultrices eget, rhoncus sit amet diam. Pellentesque tempor pellentesque orci, viverra gravida tortor facilisis nec. Nam tempus gravida augue ut pharetra. Integer adipiscing tincidunt felis sit amet tristique. Mauris dignissim elementum odio. Curabitur ante nisi, gravida a tempus non, fringilla eget tellus. Duis vel magna dui, sed vehicula leo. Sed luctus mi id mi porta varius. Nam tellus erat, gravida vel consectetur in, luctus et lacus. Phasellus ultricies sodales varius. Mauris ac tristique risus. Cras accumsan molestie commodo. Donec id augue ut urna tristique adipiscing ut id mi. Suspendisse potenti. Donec ut dui eget ante fringilla luctus quis non tellus. Ut quis diam ac nulla luctus viverra a vitae magna. Proin a erat libero, sit amet tempor libero. Sed nunc odio, aliquam id lacinia eu, sodales tincidunt risus. Suspendisse facilisis dignissim sapien, quis blandit tortor semper sit amet. Praesent ut ipsum ligula, vitae porttitor risus. Phasellus elementum nibh non enim tincidunt interdum. Sed tincidunt odio sed dolor laoreet a fringilla turpis consectetur. Cras et metus iaculis ante dictum semper at et erat. Etiam tincidunt ante at sem tristique sit amet convallis risus ornare. Sed blandit diam eget lorem tempor blandit. Duis fringilla imperdiet.'); break;
				case 'oz' : jQuery("#tdc_string").html('"That is a first-rate idea," said the Lion.  "One would almost suspect you had brains in your head, instead of straw." The Woodman set to work at once, and so sharp was his axe that the tree was soon chopped nearly through.  Then the Lion put his strong front legs against the tree and pushed with all his might, and slowly the big tree tipped and fell with a crash across the ditch, with its top branches on the other side. They had just started to cross this queer bridge when a sharp growl made them all look up, and to their horror they saw running toward them two great beasts with bodies like bears and heads like tigers. "They are the Kalidahs!" said the Cowardly Lion, beginning to tremble. "Quick!" cried the Scarecrow.  "Let us cross over." So Dorothy went first, holding Toto in her arms, the Tin Woodman followed, and the Scarecrow came next.  The Lion, although he was certainly afraid, turned to face the Kalidahs, and then he gave so loud and terrible a roar that Dorothy screamed and the Scarecrow fell over backward, while even the fierce beasts stopped short and looked at him in surprise. But, seeing they were bigger than the Lion, and remembering that there were two of them and only one of him, the Kalidahs again rushed forward, and the Lion crossed over the tree and turned to see what they would do next.  Without stopping an instant the fierce beasts also began to cross the tree.  And the Lion said to Dorothy: "We are lost, for they will surely tear us to pieces with their sharp claws.  But stand close behind me, and I will fight them as long as I am alive." "Wait a minute!" called the Scarecrow.  He had been thinking what was best to be done, and now he asked the Woodman to chop away the end of the tree that rested on their side of the ditch.  The Tin Woodman began to use his axe at once, and, just as the two Kalidahs were nearly across, the tree fell with a crash into the gulf, carrying the ugly, snarling brutes with it, and both were dashed to pieces on the sharp rocks at the bottom. "Well," said the Cowardly Lion, drawing a long breath of relief, "I see we are going to live a little while longer, and I am glad of it, for it must be a very uncomfortable thing not to be alive.  Those creatures frightened me so badly that my heart is beating yet." "Ah," said the Tin Woodman sadly, "I wish I had a heart to beat." This adventure made the travelers more anxious than ever to get out of the forest, and they walked so fast that Dorothy became tired, and had to ride on the Lion\'s back.  To their great joy the trees became thinner the farther they advanced, and in the afternoon they suddenly came upon a broad river, flowing swiftly just before them.  On the other side of the water they could see the road of yellow brick running through a beautiful country, with green meadows dotted with bright flowers and all the road bordered with trees hanging full of delicious fruits.  They were greatly pleased to see this delightful country before them. "How shall we cross the river?" asked Dorothy. "That is easily done," replied the Scarecrow.  "The Tin Woodman must build us a raft, so we can float to the other side." So the Woodman took his axe and began to chop down small trees to make a raft, and while he was busy at this the Scarecrow found on the riverbank a tree full of fine fruit.  This pleased Dorothy, who had eaten nothing but nuts all day, and she made a hearty meal of the ripe fruit. But it takes time to make a raft, even when one is as industrious and untiring as the Tin Woodman, and when night came the work was not done. So they found a cozy place under the trees where they slept well until the morning; and Dorothy dreamed of the Emerald City, and of the good Wizard Oz, who would soon send her back to her own home again. Our little party of travelers awakened the next morning refreshed and full of hope, and Dorothy breakfasted like a princess off peaches and plums'); break;
				case 'alice' : jQuery("#tdc_string").html("The twelve jurors were all writing very busily on slates. 'What are they  doing?' Alice whispered to the Gryphon. 'They can't have anything to put  down yet, before the trial's begun.' 'They're putting down their names,' the Gryphon whispered in reply, 'for  fear they should forget them before the end of the trial.' 'Stupid things!' Alice began in a loud, indignant voice, but she stopped  hastily, for the White Rabbit cried out, 'Silence in the court!' and the  King put on his spectacles and looked anxiously round, to make out who  was talking. Alice could see, as well as if she were looking over their shoulders,  that all the jurors were writing down 'stupid things!' on their slates,  and she could even make out that one of them didn't know how to spell  'stupid,' and that he had to ask his neighbour to tell him. 'A nice  muddle their slates'll be in before the trial's over!' thought Alice. One of the jurors had a pencil that squeaked. This of course, Alice  could not stand, and she went round the court and got behind him, and  very soon found an opportunity of taking it away. She did it so quickly  that the poor little juror (it was Bill, the Lizard) could not make out  at all what had become of it; so, after hunting all about for it, he was  obliged to write with one finger for the rest of the day; and this was  of very little use, as it left no mark on the slate. 'Herald, read the accusation!' said the King. On this the White Rabbit blew three blasts on the trumpet, and then  unrolled the parchment scroll, and read as follows:&mdash; 'The Queen of Hearts, she made some tarts, All on a summer day: The Knave of Hearts, he stole those tarts, And took them quite away!' 'Consider your verdict,' the King said to the jury. 'Not yet, not yet!' the Rabbit hastily interrupted. 'There's a great  deal to come before that!' 'Call the first witness,' said the King; and the White Rabbit blew three  blasts on the trumpet, and called out, 'First witness!' The first witness was the Hatter. He came in with a teacup in one  hand and a piece of bread-and-butter in the other. 'I beg pardon, your  Majesty,' he began, 'for bringing these in: but I hadn't quite finished  my tea when I was sent for.' 'You ought to have finished,' said the King. 'When did you begin?' The Hatter looked at the March Hare, who had followed him into the  court, arm-in-arm with the Dormouse. 'Fourteenth of March, I think it  was,' he said. 'Fifteenth,' said the March Hare. 'Sixteenth,' added the Dormouse. 'Write that down,' the King said to the jury, and the jury eagerly  wrote down all three dates on their slates, and then added them up, and  reduced the answer to shillings and pence. 'Take off your hat,' the King said to the Hatter. 'It isn't mine,' said the Hatter. 'Stolen!' the King exclaimed, turning to the jury, who instantly made a  memorandum of the fact. 'I keep them to sell,' the Hatter added as an explanation; 'I've none of  my own. I'm a hatter.' Here the Queen put on her spectacles, and began staring at the Hatter,  who turned pale and fidgeted. 'Give your evidence,' said the King; 'and don't be nervous, or I'll have  you executed on the spot.' This did not seem to encourage the witness at all: he kept shifting  from one foot to the other, looking uneasily at the Queen, and in  his confusion he bit a large piece out of his teacup instead of the  bread-and-butter. Just at this moment Alice felt a very curious sensation, which puzzled  her a good deal until she made out what it was: she was beginning to  grow larger again, and she thought at first she would get up and leave  the court; but on second thoughts she decided to remain where she was as  long as there was room for her. 'I wish you wouldn't squeeze so.' said the Dormouse, who was sitting  next to her. 'I can hardly breathe.' 'I can't help it,' said Alice very meekly: 'I'm growing.' 'You've no right to grow here,' said the"); break;
				case 'moby' : jQuery("#tdc_string").html('"Aye, sir, I think I can; all seams and dents but one." "Look ye here, then," cried Ahab, passionately advancing, and leaning with both hands on Perth\'s shoulders; "look ye here&mdash;HERE&mdash;can ye smoothe out a seam like this, blacksmith," sweeping one hand across his ribbed brow; "if thou could\'st, blacksmith, glad enough would I lay my head upon thy anvil, and feel thy heaviest hammer between my eyes. Answer! Can\'st thou smoothe this seam?" "Oh! that is the one, sir! Said I not all seams and dents but one?" "Aye, blacksmith, it is the one; aye, man, it is unsmoothable; for though thou only see\'st it here in my flesh, it has worked down into the bone of my skull&mdash;THAT is all wrinkles! But, away with child\'s play; no more gaffs and pikes to-day. Look ye here!" jingling the leathern bag, as if it were full of gold coins. "I, too, want a harpoon made; one that a thousand yoke of fiends could not part, Perth; something that will stick in a whale like his own fin-bone. There\'s the stuff," flinging the pouch upon the anvil. "Look ye, blacksmith, these are the gathered nail-stubbs of the steel shoes of racing horses." "Horse-shoe stubbs, sir? Why, Captain Ahab, thou hast here, then, the best and stubbornest stuff we blacksmiths ever work." "I know it, old man; these stubbs will weld together like glue from the melted bones of murderers. Quick! forge me the harpoon. And forge me first, twelve rods for its shank; then wind, and twist, and hammer these twelve together like the yarns and strands of a tow-line. Quick! I\'ll blow the fire." When at last the twelve rods were made, Ahab tried them, one by one, by spiralling them, with his own hand, round a long, heavy iron bolt. "A flaw!" rejecting the last one. "Work that over again, Perth." This done, Perth was about to begin welding the twelve into one, when Ahab stayed his hand, and said he would weld his own iron. As, then, with regular, gasping hems, he hammered on the anvil, Perth passing to him the glowing rods, one after the other, and the hard pressed forge shooting up its intense straight flame, the Parsee passed silently, and bowing over his head towards the fire, seemed invoking some curse or some blessing on the toil. But, as Ahab looked up, he slid aside. "What\'s that bunch of lucifers dodging about there for?" muttered Stubb, looking on from the forecastle. "That Parsee smells fire like a fusee; and smells of it himself, like a hot musket\'s powder-pan." At last the shank, in one complete rod, received its final heat; and as Perth, to temper it, plunged it all hissing into the cask of water near by, the scalding steam shot up into Ahab\'s bent face. "Would\'st thou brand me, Perth?" wincing for a moment with the pain; "have I been but forging my own branding-iron, then?" "Pray God, not that; yet I fear something, Captain Ahab. Is not this harpoon for the White Whale?" "For the white fiend! But now for the barbs; thou must make them thyself, man. Here are my razors&mdash;the best of steel; here, and make the barbs sharp as the needle-sleet of the Icy Sea." For a moment, the old blacksmith eyed the razors as though he would fain not use them. "Take them, man, I have no need for them; for I now neither shave, sup, nor pray till&mdash;but here&mdash;to work!" Fashioned at last into an arrowy shape, and welded by Perth to the shank, the steel soon pointed the end of the iron; and as the blacksmith was about giving the barbs their final heat, prior to tempering them, he cried to Ahab to place the water-cask near. "No, no&mdash;no water for that; I want it of the true death-temper. Ahoy, there! Tashtego, Queequeg, Daggoo! What say ye, pagans! Will ye give me as much blood as will cover this barb?" holding it high up. A cluster of dark nods replied, Yes. Three punctures were made in the heathen flesh, and the White Whale\'s barbs were then tempered. "Ego non baptizo te in nomine patris, sed in nomine diaboli!" deliriously howled Ahab, as the malignant iron scorchingly devoured the baptismal blood. Now, mustering the spare poles from below, and selecting one of hickory, with the bark still investing it, Ahab fitted the end to the socket of the iron. A coil of new tow-line was then unwound, and'); break;
				case 'war' : jQuery("#tdc_string").html('I felt foolish and angry.  I tried and found I could not tell them what I had seen.  They laughed again at my broken sentences. "You\'ll hear more yet," I said, and went on to my home. I startled my wife at the doorway, so haggard was I.  I went into the dining room, sat down, drank some wine, and so soon as I could collect myself sufficiently I told her the things I had seen.  The dinner, which was a cold one, had already been served, and remained neglected on the table while I told my story. "There is one thing," I said, to allay the fears I had aroused; "they are the most sluggish things I ever saw crawl.  They may keep the pit and kill people who come near them, but they cannot get out of it. . . .  But the horror of them!" "Don\'t, dear!" said my wife, knitting her brows and putting her hand on mine. "Poor Ogilvy!" I said.  "To think he may be lying dead there!" My wife at least did not find my experience incredible.  When I saw how deadly white her face was, I ceased abruptly. "They may come here," she said again and again. I pressed her to take wine, and tried to reassure her. "They can scarcely move," I said.  The atmosphere of the earth, we now know, contains far more oxygen or far less argon (whichever way one likes to put it) than does Mars. The invigorating influences of this excess of oxygen upon the Martians indisputably did much to counterbalance the increased weight of their bodies.  And, in the second place, we all overlooked the fact that such mechanical intelligence as the Martian possessed was quite able to dispense with muscular exertion at a pinch. But I did not consider these points at the time, and so my reasoning was dead against the chances of the invaders.  With wine and food, the confidence of my own table, and the necessity of reassuring my wife, I grew by insensible degrees courageous and secure. "They have done a foolish thing," said I, fingering my wineglass. "They are dangerous because, no doubt, they are mad with terror. Perhaps they expected to find no living things--certainly no intelligent living things." "A shell in the pit" said I, "if the worst comes to the worst will kill them all." The intense excitement of the events had no doubt left my perceptive powers in a state of erethism.  I remember that dinner table with extraordinary vividness even now.  My dear wife\'s sweet anxious face peering at me from under the pink lamp shade, the white cloth with its silver and glass table furniture--for in those days even philosophical writers had many little luxuries--the crimson-purple wine in my glass, are photographically distinct.  At the end of it I sat, tempering nuts with a cigarette, regretting Ogilvy\'s rashness, and denouncing the shortsighted timidity of the Martians. So some respectable dodo in the Mauritius might have lorded it in his nest, and discussed the arrival of that shipful of pitiless sailors in want of animal food.  "We will peck them to death tomorrow, my dear." I did not know it, but that was the last civilised dinner I was to eat for very many strange and terrible days. The most extraordinary thing to my mind, of all the strange and wonderful things that happened upon that Friday, was the dovetailing of the commonplace habits of our social order with the first beginnings of the series of events that was to topple that social order headlong.  If on Friday night you had taken a pair of compasses and drawn a circle with a radius of five miles round the Woking sand pits, I doubt if you would have had one human being outside it, unless it were some relation of Stent or of the three or four cyclists or London people lying dead on the common, whose emotions or habits were at all affected by the new-comers.  Many people had heard of the cylinder, of course, and talked about it in their leisure, but it certainly did not make the sensation that an ultimatum to Germany would have done. In London that night poor Henderson\'s telegram describing the gradual unscrewing of the shot was judged to be a canard, and his evening paper, after wiring for authentication from him'); break;
			}
			return false;
		}
	</script>
	<?
}

?>