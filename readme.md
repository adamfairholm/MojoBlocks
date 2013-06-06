# MojoBlocks for MojoMotor v1.2.4

MojoMotor is an open source add-on for [MojoMotor](http://ellislab.com/mojomotor) that allows you to create editable "blocks" for special content. Got a headline that needs to be an h3? Use the H block. Need to add a Twitter stream? Use the Twitter stream block. With over 13 ready to use blocks and an API to easily create more, MojoBlocks is your Swiss Army knife for MojoMotor.

## Installation

Installation consists of just dropping the mb folder into **system/mojomotor/third_party**. That’s it! The necessary database table is created automatically. You are now ready to use MojoBlocks.

MojoBlocks automatically creates a table if it doesn’t find one when it installs. If for some reason your setup doesn’t allow MojoBlocks to do this, you can do it manually with this MySQL command:

	CREATE TABLE `mojo_blocks` (
		`id` int(9) NOT NULL AUTO_INCREMENT,
		`created` datetime DEFAULT NULL,
		`updated` datetime DEFAULT NULL,
		`block_reach` enum('local','global') DEFAULT 'local',
		`block_type` varchar(50) DEFAULT NULL,
		`block_id` varchar(100) DEFAULT NULL,
		`block_content` blob, `page_url_title` varchar(100) DEFAULT NULL,
		`layout_id` int(5) DEFAULT NULL,
		`tag_settings` blob,
		`cache` longblob,
		`cache_process` varchar(200) DEFAULT NULL,
		`cache_expire` varchar(60) DEFAULT NULL,
		`tag_data` blob,
		PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

_Note: If you are using something other than "mojo" as your table prefix, make sure to change it in the above code so it matches the rest of your MojoMotor tables._

## Basic Usage

A MojoBlock is created using a similar syntax to region MojoMotor regions. Here is what you need to tell MojoBlocks:

* The ID of the block. This is something unique that you make up
* The type of block you want to create
* Whether you want to create a local or global block

Here is an example of a local Vimeo block in the layout:

	<div id="home_video" name="vimeo" class="mojoblock_region">
	{mojo:mb:block type="vimeo" id="home_video"}
	</div>

Here is an example of a global Vimeo block in the layout:

	<div id="home_video" name="vimeo" class="mojoblock_global_region">
	{mojo:mb:block type="vimeo" id="home_video" reach="global"}
	</div>

As you can see, the only difference is the global block has a class of **mojoblock\_global\_region** instead of **mojoblock\_region** and the MojoBlocks tag has the reach="global" parameter set. Other than that you need to put the block slug in the name parameter of the div and the type parameter of the tag. You also need to put the id you create for the block in the id parameter of the div, and the id parameter of the tag. The reach parameter is required in the tag only for a global block.

Once that is in place, that’s all you need to do. Refresh the page, and you’ll see a block block area. Local blocks are in blue, global blocks will show up in green. Click on the block, and you’ll get the update interface.

Enter the necessary information, click submit, and you've created your first block!

## Setting Defaults

Every block comes with different variables to provide the block with custom data. For the Twitter block, for example, you provide the desired user’s Twitter handle, and the number of tweets you want to show. For the YouTube block, you provide the video ID (or URL) and the height and width you want the embed to be.

In some cases, however, you want to pass information directly to the block without having to worry about the person editing the site’s content either being confused by it in the editor or worse: changing the value and damaging the design.

That’s why MojoBlocks allows you pass data directly into the block in the code. Once you have passed that data, it simply won’t show up in the editor.

Let’s take a look at a simple example:

We have a header, but we want it to be an H1 tag. The H block allows you a drop down to choose H1-H6, but why would we want someone to change that?

To make this foolproof, we simply find the variable for the H tag number in the documentation, and insert it into the tag as a parameter.

	<div id="page_header" name="h" class="mojoblock_region"> {mojo:mb:block type="h" id="page_header" header_type="h1"} </div>

## Working With Layouts

Very simple blocks don’t require customized output. A YouTube embed is a YouTube embed give or take a few parameters. An H1 tag is an H1 tag.

However, some tags require a higher degree of control. You may not want to show how long ago a tweet was, but you may want to show what software the tweet was sent with.

That’s why some blocks support MojoMotor layouts and can pass variables to them. To create a new layout for a block, make a new layout just like you would any other one in MojoMotor.

After creating your new layout, refresh the page, and edit you block details. You’ll see a list of layouts to choose from, with “Block Default” being the top choice. Choose the layout you just created, and the data from the block will be passed to that layout.

Each block that supports layouts has a list of variables in its documentation. Each single variable can be accessed by putting the variable in curly brackets. For example:

	{user_name}

For layouts that have multiple instances of something, like multiple tweets, you can cycle through them by using an opening and closing bracket:

	{tweets}
		<p>{text}</p>
		<small>Posted {how_long_ago}</small>
	{/tweets}

Layouts give you an easy way to make sure that you can take the data from a more complex block and format it the way you need to.

## Blocks

MojoMotor comes with the following block:

<table> 
<tr> 
	<th>Block</th> 
	<th>Description</th> 
</tr>
<tr> 
	<td>Evernote</td> 
	<td>Displays a "save to Evernote" button.</td> 
</tr>
<tr> 
	<td>Facebook Like</td> 
	<td>Displays a Facebook like button.</td> 
</tr>
<tr> 
	<td>Flickr</td> 
	<td>Show Flickr images from a user or set.</td> 
</tr>
<tr> 
	<td>H</td> 
	<td>Show an H tag with text content.</td> 
</tr>
<tr> 
	<td>HTML</td> 
	<td>Render HTML.</td> 
</tr>
<tr> 
	<td>Pages</td> 
	<td>Display pages in a list with access to content.</td> 
</tr>
<tr> 
	<td>RSS</td> 
	<td>Show content from an RSS feed.</td> 
</tr>
<tr> 
	<td>Sub Page</td> 
	<td>Display sub pages from a given page.</td> 
</tr>
<tr> 
	<td>Text</td> 
	<td>Display text.</td> 
</tr>
<tr> 
	<td>Twitter Button</td> 
	<td>Display a tweet button.</td> 
</tr>
<tr> 
	<td>Twitter Search</td> 
	<td>Display twitter search results.</td> 
</tr>
<tr> 
	<td>Twitter User</td> 
	<td>Display tweets from auser.</td> 
</tr>
<tr> 
	<td>Vimeo</td> 
	<td>Display a Vimeo embed.</td> 
</tr>
<tr> 
	<td>YouTube</td> 
	<td>Display a YouTube embed.</td> 
</tr>
</table>

Most of these block types are self-explanatory, but a few have some specific instructions.

### RSS Block

The RSS block utilizes layouts. Below is a table of variables available in your layout.

#### Single Variables</strong></p> 

Below are a table of single variables available to the template.

<table cellpadding="0" cellspacing="0"> 
<tr> 
	<th width="150">Variable Slug</th> 
	<th>Description</th> 
</tr>
<tr> 
	<td>&#123;feed_title&#125;</td> 
	<td>Title of the feed</td> 
</tr>
<tr> 
	<td>&#123;copyright&#125;</td> 
	<td>Feed Copyright</td> 
</tr>
<tr> 
	<td>&#123;description&#125;</td> 
	<td>Description of the feed</td> 
</tr>
<tr> 
	<td>&#123;encoding&#125;</td> 
	<td>Feed character encoding</td> 
</tr>
<tr> 
	<td>&#123;total_items&#125;</td> 
	<td>Total number of items found in the feed</td> 
</tr>
<tr> 
	<td>&#123;language&#125;</td> 
	<td>Language of the feed (ex: "en" for English)</td> 
</tr>
<tr> 
	<td>&#123;feed_url&#125;</td> 
	<td><span class="caps">URL</span> of the feed</td> 
</tr>
</table>

#### &#123;items&#125;

RSS items that are found are put into the RSS item variable pair. Also included are link URLs to bookmark the item on social media sites.

	{items}
		{example_variable}
	{/items}

<table cellpadding="0" cellspacing="0" class="docs_table"> 
<tr> 
	<th width="150">Variable Slug</th> 
	<th>Description</th> 
</tr>
<tr> 
	<td>&#123;title&#125;</td> 
	<td>Item title</td> 
</tr>
<tr> 
	<td>&#123;description&#125;</td> 
	<td>Item description</td> 
</tr>
<tr> 
	<td>&#123;content&#125;</td> 
	<td>Item content</td> 
</tr>
<tr> 
	<td>&#123;content_plain&#125;</td> 
	<td>Item content with tags stripped (except for strong and em tags)</td> 
</tr>
<tr> 
	<td>&#123;preview&#125;</td> 
	<td>A short preview of the content with tags removed</td> 
</tr>
<tr> 
	<td>&#123;permalink&#125;</td> 
	<td>The permalink to the item</td> 
</tr>
<tr> 
	<td>&#123;date_posted&#125;</td> 
	<td>Date the item was posted</td> 
</tr>
<tr> 
	<td>&#123;id&#125;</td> 
	<td>Item ID</td> 
</tr>
<tr> 
	<td>&#123;author_name&#125;</td> 
	<td>Name of the item author (if available)</td> 
</tr>
<tr> 
	<td>&#123;author_email&#125;</td> 
	<td>Email address of the author (if available)</td> 
</tr>
<tr> 
	<td>&#123;author_link&#125;</td> 
	<td>Author link <span class="caps">URL</span> (if available)</td> 
</tr>
<tr> 
	<td>&#123;blinklist_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Blinklist</td> 
</tr>
<tr> 
	<td>&#123;blogmarks_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Blogmarks</td> 
</tr>
<tr> 
	<td>&#123;delicious_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Delicious</td> 
</tr>
<tr> 
	<td>&#123;digg_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Digg</td> 
</tr>
<tr> 
	<td>&#123;furl_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Furl</td> 
</tr>
<tr> 
	<td>&#123;magnolia_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Magnolia</td> 
</tr>
<tr> 
	<td>&#123;newsvine_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Newsvine</td> 
</tr> 
<tr> 
	<td>&#123;reddit_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Reddit</td> 
</tr>
<tr> 
	<td>&#123;segnalo_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Segnalo</td> 
</tr>
<tr> 
	<td>&#123;simpy_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Simpy</td> 
</tr>
<tr> 
	<td>&#123;spurl_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Spurl</td> 
</tr>
<tr> 
	<td>&#123;wists_link&#125;</td> 
	<td>Link <span class="caps">URL</span> to bookmark on Wists</td> 
</tr>
</table>

### Twitter User Block

The Twitter User block utilizes layouts. Below is a table of variables available in your layout

Below are a table of single variables available to the template. 

<table cellpadding="0" cellspacing="0"> 
<tr> 
	<th width="150">Variable Slug</th> 
	<th>Description</th> 
</tr>
<tr> 
	<td>&#123;name&#125;</td> 
	<td>Name of the Tweeter</td> 
</tr>
<tr> 
	<td>&#123;id&#125;</td> 
	<td>ID of the Tweeter</td> 
</tr>
<tr> 
	<td>&#123;screen_name&#125;</td> 
	<td>Twitter handle of the Tweeter</td> 
</tr>
<tr> 
	<td>&#123;description&#125;</td> 
	<td>Description of Tweeter from their profile</td> 
</tr>
<tr> 
	<td>&#123;lang&#125;</td> 
	<td>Language of the Tweeter (ie: en for English)</td> 
</tr>
<tr> 
	<td>&#123;followers_count&#125;</td> 
	<td>Number of followers the Tweeter has</td> 
</tr>
<tr> 
	<td>&#123;friends_count&#125;</td> 
	<td>Number of users the Tweeter is following</td> 
</tr>
<tr> 
	<td>&#123;listed_count&#125;</td> 
	<td>Number of lists the Tweeter appears in</td> 
</tr>
<tr> 
	<td>&#123;statuses_count&#125;</td> 
	<td>Number of tweets the Tweeter has posted</td> 
</tr>
<tr> 
	<td>&#123;profile_background_image_url&#125;</td> 
	<td><span class="caps">URL</span> of the background image of the Tweeter&#8217;s profile</td> 
</tr>
<tr> 
	<td>&#123;favourites_count&#125;</td> 
	<td>Number of tweets the Tweeter has marked as their favorite</td> 
</tr>
<tr> 
	<td>&#123;profile_image_url&#125;</td> 
	<td><span class="caps">URL</span> of the profile image of the Tweeter</td> 
</tr>
</table>

#### &#123;tweets&#125;

Contains data for the tweets retrieved by the block, including some basic information about the tweeter for use if necessary.

	&#123;tweets&#125;
		&#123;example_variable&#125;
	&#123;/tweets&#125;

<table cellpadding="0" cellspacing="0"> 
<tr> 
	<th width="150">Variable Slug</th> 
	<th>Description</th> 
</tr>
<tr> 
	<td>&#123;text&#125;</td> 
	<td>Text of the tweet.</td> 
</tr>
<tr> 
	<td>&#123;tweet_url&#125;</td> 
	<td>Twitter <span class="caps">URL</span> to the tweet.</td> 
</tr>
<tr> 
	<td>&#123;how_long_ago&#125;</td> 
	<td>How long ago the tweet was posted (ex: "2 hours ago")</td> 
</tr>
<tr> 
	<td>&#123;text_no_links&#125;</td> 
	<td>Text of the tweet but without any hyperlinks for hashtags, @, etc.</td> 
</tr>
<tr> 
	<td>&#123;username_link&#125;</td> 
	<td><span class="caps">URL</span> to the profile of the tweeter.</td> 
</tr>
<tr> 
	<td>&#123;id&#125;</td> 
	<td>ID of the tweet.</td> 
</tr>
<tr> 
	<td>&#123;retweet_count&#125;</td> 
	<td>Number of times the tweet has been re-tweeted.</td> 
</tr>
<tr> 
	<td>&#123;source&#125;</td> 
	<td>Program used to post tweet with hyperlink.</td> 
</tr>
<tr> 
	<td>&#123;in_reply_to_user_id&#125;</td> 
	<td>If applicable, the user ID of the tweeter that the tweet was in reply to.</td> 
</tr>
<tr> 
	<td>&#123;in_reply_to_status_id&#125;</td> 
	<td>If applicable, the status ID of the tweet that the tweet was in reply to.</td> 
</tr>
<tr> 
	<td>&#123;in_reply_to_screen_name&#125;</td> 
	<td>If applicable, the Twitter handle of the tweeter that the tweet was in reply to.</td> 
</tr>
<tr> 
	<td>&#123;user_screen_name&#125;</td> 
	<td>Twitter handle of the tweeter.</td> 
</tr>
<tr> 
	<td>&#123;user_name&#125;</td> 
	<td>Full name of the tweeter.</td> 
</tr>
<tr> 
	<td>&#123;user_url&#125;</td> 
	<td><span class="caps">URL</span> from the profile of the tweeter.</td> 
</tr>
<tr> 
	<td>&#123;user_profile_image_url&#125;</td> 
	<td><span class="caps">URL</span> to the profile image of the tweeter.</td> 
</tr>
</table>

## Developing Blocks

Blocks follow a simple structure that can be used to make very simple blocks to much more complicated ones. The following is an overview of the basic structure of a block.

Each block has a **slug**, or short name with no spaces in all lower case. In this case, our slug is h for the H tag block. It could "twitter" or "bananas" or anything you want.

Each block is a folder with the same name as the slug, a file that is named block.block_slug.php, and a 32×32 icon named icon.png. For example:

	- h
		- block.h.php
		- icon.png

Save this folder in the third_party folder in the mb MojoBlocks addon folder.

A block is a class that follows a basic structure. Here's an [example](https://gist.github.com/adamfairholm/1335663).

#### Class Variables

You must provide the following class variables for the block class:

<table cellpadding="0" cellspacing="0" class="docs_table"> 
<tr> 
	<th width="150">Variable Slug</th> 
	<th>Description</th> 
</tr> </p>

<p>							<tr> 
	<td>block_name</td> 
	<td>A name for the block (you don&#8217;t need to include the word &#8220;Block&#8221;)</td> 
</tr> 
<tr> 
	<td>block_version</td> 
	<td>A version for your block (ex: &#8220;v1.0&#8221;)</td> 
</tr> 
<tr> 
	<td>block_slug</td> 
	<td>The slug for your block</td> 
</tr> 
<tr> 
	<td>block_desc</td> 
	<td>A short, one line description of what your block does</td> 
</tr> 
<tr> 
	<td>block_fields</td> 
	<td>The information your block needs. For more information, see <a href="mojoblocks/documentation/block-data-types">Block Data Types</a></td> 
</tr> 
</table>

#### Sending Output With render()

**render()** is the only function a block is required to have. It is provided with an associative array of saved data for the block for you to parse and use as you see fit. Just remember to return your output and not echo it.

#### Using CodeIgniter

MojoMotor is build on [CodeIgniter](), which means you can use all the CodeIgniter libraries and goodies that it offers. For full documentation on CodeIgniter, see the [CI User Docs](). To get a copy of the CI super object, use the following code in your constructor and reference objects as <var>$this->block</var>:

	$this->block =& get_instance();

#### Block Data Types

One of the best things about MojoBlocks is you get a built-in method to save data for a block! Neat-o!

To define that data, you need to create a class variable called <var>block_fields</var>. Each key needs to be the slug of the field, and each node needs to be an array of configuration. Here’s an example of a field from the Vimeo block:

	'width'	 => array(
		'label'	 => "Width of video",
		'validation'	=> "trim|numeric"
	)

The following options are accepted as field configuration options:

<table cellpadding="0" cellspacing="0" class="docs_table"> 
<tr> 
	<th width="150">Option</th> 
	<th>Description</th> <br />
<th>Value</th> 
</tr> </p>

<p>							<tr> 
	<td>label</td> 
	<td>Label for the editor form</td> 
<td>Any text string</td> 
</tr> </p>

<p>							<tr> 
	<td>validation</td> 
	<td>A validation string</td> 
<td>See <a href="http://codeigniter.com/user_guide/libraries/form_validation.html#rulereference">CodeIgniter docs</a> for acceptable values. Separated by a pipe character</td> 
</tr> 
<tr> 
	<td>type</td> 
	<td>The type of field.</td> 
<td>Defaults to &#8220;input&#8221;. Can be any one of the values detailed in &#8220;Data Types&#8221; below</td> 
</tr> 
<tr> 
	<td>values</td> 
	<td>For use with the dropdown data type</td> 
<td>As associative array of possible values</td> 
</tr>
</table>

MojoBlocks can save certain types of data and display the proper form for input. This can be set as an array node named type which is assumed to be input if it isn’t set.

The following are the current data types that MojoBlocks supports:

<table cellpadding="0" cellspacing="0" class="docs_table"> 
<tr> 
	<th width="150">Type Slug</th> 
	<th>Description</th> 
</tr>
<tr> 
	<td>input</td> 
	<td>This is the default data type and is just a normal form input</td> 
</tr> 
<tr> 
	<td>dropdown</td> 
	<td>This is a simple drop down select box. It is populated by the <strong>values</strong> array</td> 
</tr> 
<tr> 
	<td>textbox</td> 
	<td>This is a multi-line textbox</td> 
</tr> 
<tr> 
	<td>layout</td> 
	<td>This creates a dropdown that gives the user an option to choose an external layout.</td> 
</tr> 
</table>

#### Caching Data

MojoBlocks comes with two built in caching options. To use either, you need to add three class variables to your block:

	var $cache_output	 = true;
	var $cache_expire	 = '+1 hour';
	var $cache_data		 = '';

<table cellpadding="0" cellspacing="0" class="docs_table"> 							
	<tr> 
		<td>cache_ouput</td> 
		<td>A boolean variable that controls whether the block will be cached or not. Other cache variables are ignored if this is set to <span class="caps">FALSE</span>.</td> 
	</tr> 
	<tr> 
		<td>cache_expire</td> 
		<td>A string of how far in the future the cache should expire. This string needs to be readable by <a href="http://php.net/manual/en/function.strtotime.php">strtotime()</a></td> 
	</tr> 
	<tr> 
		<td>cache_data</td> 
		<td>MojoBlocks uses this to store cache data. Should be set to an empty string</td> 
	</tr> 
</table> 

The simplest way to cache a block is to simply cache the entire block output. If you have added the three variables above, congratulations! You have just cached the block output and it will be refreshed in the interval of time you have specified.

Sometimes, however, you don’t want to simply cache the entire output of a block. Sometimes, you want to cache something like an API data call so variables that are being calculated based on current conditions (such as how long ago a tweet was) can still be used.

To cache a single function in a block, just name it cache\_data\_call. All you need to do is check for the cache\_data variable and return the data that you want cached if the cache is empty. For example:

	function cache_data_call($block_data)
	{
		if ($this->cache_data) {
			return $this->cache_data;
		} else {
			// Return data to be cached
		}
	}

Make sure you do not serialize arrays or objects you are returning yourself, or else MojoBlocks will not know to unserialize it before giving it to your render function.