=== WordPress Bucket List ===
Contributors: yourusername
Tags: bucket list, goals, achievements, progress tracker, personal goals
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A beautiful, Apple-inspired bucket list plugin to track your life goals with category-specific details.

== Description ==

WordPress Bucket List helps you create and track your life goals with a stunning, modern interface. Perfect for bloggers who want to share their journey and track progress on books, movies, travel, fitness goals, and more.

= Features =

* **Beautiful Grid Display** - Apple-inspired card design with hover effects
* **Category-Specific Details** - Special fields for Books, Movies, Music Albums, TV Series, Video Games, Podcasts, and Workouts
* **Progress Tracking** - Visual progress bars and completion percentage
* **IMDb-Style Technical Cards** - Display detailed information on blog posts
* **Bilingual Support** - English and Spanish interface
* **Image Support** - Cover art for each item type
* **Filtering** - Category-based filtering on frontend
* **Pagination** - Configurable items per page
* **Customizable** - Multiple shortcode parameters

= Supported Item Types =

* 📚 Books (author, ISBN, pages, quotes, etc.)
* 🎬 Movies (director, cast, runtime, IMDb rating, etc.)
* 🎵 Music Albums (artist, tracks, release year, etc.)
* 📺 TV Series (seasons, episodes, network, etc.)
* 🎮 Video Games (platform, developer, hours played, etc.)
* 🎙️ Podcasts (host, episodes, platform, etc.)
* 💪 Workouts (type, frequency, goals, etc.)

= Usage =

1. Create bucket list items from WordPress admin
2. Select item type and fill in details
3. Display with shortcode: `[bucket_list]`
4. Link items to blog posts for automatic technical details display

= Shortcode Parameters =

* `category` - Filter by category slug
* `columns` - Number of columns (1-4, default: 3)
* `per_page` - Items per page (default: 12)
* `show_filter` - Show category filter (yes/no)
* `pagination` - Show pagination (yes/no)

Example: `[bucket_list columns="2" per_page="9" category="books"]`

== Installation ==

1. Upload `wordpress-bucket-list` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu
3. Go to 'Bucket List' in WordPress admin to create items
4. Use shortcode `[bucket_list]` on any page/post

== Frequently Asked Questions ==

= How do I display the bucket list? =

Use the shortcode `[bucket_list]` on any page or post.

= Can I customize the number of columns? =

Yes, use `[bucket_list columns="2"]` to change the grid layout.

= How do I add technical details to blog posts? =

When editing a regular post, use the "Bucket List Item" meta box in the sidebar to select an item. Its details will automatically appear at the end of your post.

= Is it translation-ready? =

Yes, includes English and Spanish translations. The plugin is ready for additional translations.

= Can I add custom item types? =

Currently supports 7 item types. Custom types would require code modification.

== Screenshots ==

1. Beautiful grid display of bucket list items
2. Category-specific detail fields in admin
3. IMDb-style technical card on blog posts
4. Admin settings page
5. Progress tracking with visual indicators

== Changelog ==

= 1.0.0 =
* Initial release
* Grid display with Apple-inspired design
* 7 item types with specific fields
* Progress tracking
* Bilingual support (English/Spanish)
* IMDb-style technical details
* Shortcode with multiple parameters
* Settings page

== Upgrade Notice ==

= 1.0.0 =
Initial release of WordPress Bucket List plugin.
