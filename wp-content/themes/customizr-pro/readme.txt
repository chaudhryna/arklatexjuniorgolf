# Customizr Pro v2.1.5
![Customizr - Pro](/screenshot.png)

> The pro version of the popular Customizr WordPress theme.

## Copyright
**Customizr Pro** is a WordPress theme designed by Nicolas Guillaume in Nice, France. ([website : Press Customizr](http://presscustomizr.com>))
Customizr Pro is distributed under the terms of the [GNU GPL v2.0 or later](http://www.gnu.org/licenses/gpl-3.0.en.html)

## Demo, Documentation, FAQs and Support
* DEMO : http://demo.presscustomizr.com/
* DOCUMENTATION : http://docs.presscustomizr.com/article/182-getting-started-with-the-customizr-pro-wordpress-theme
* FAQs : http://docs.presscustomizr.com/customizr-pro/faq
* SUPPORT : http://presscustomizr.com/support/

## Changelog
= 2.1.5 June 6th 2018 =
* Fix: radio and checkboxes style that should be applied only to relevant elments to avoid plugin conflits ( contact forms plugins for example ).

= 2.1.4 May 31st 2018 =
* Fix: GDPR compliance comment-form-cookies-consent label and input appearance. fixes #1538
* Fix: search form close icon visual issue in IE10+. fixes #1512
* Fix: no need to enqueue the front placeholders assets when user not logged in. fixes #1513
* Fix: make sure the mfp gallery left arrow is correctly aligned. fixes #1515
* Fix: set word-wrap break-word for whatever text in the page content. This will avoid text overflowing the parent element width, for example in list items
* Imp: make sure .navbar-brand height is set to auto to avoid issues with bootstrap3 based plugins. fixes #1533
* Imp: build/delete retina version of pdf thumbnails. Also improve remove retina images logic. fixes #1387
* Imp: do not load dev classes
* Imp: decrease space above WooCommerce page title. fixes #1521
* Added: options to fine tuning singular featured image height in smartphones
both in modern and classical style fixes #1524

= 2.1.3 March 29th 2018 =
* Fix: author meta link pointing to wrong URL
* Fix: Use of deprecated class constructor not supported since PHP7

= 2.1.2 March 26th 2018 =
* Fix : Contact infos are printed on desktops, even when set to be displayed only on mobiles

= 2.1.1 March 23rd 2018 =
* Fix: wront text-domain for the social links module

= 2.1.0 March 22nd 2018 =
* Fix: do not display post metas for pages in search results
* Fix: make sure our col*auto always have a max-width of 100%.
* Imp: overall css improvements
* Imp: remove post 'hentry' class when current post type is a page since author and published/update metas will not be printed in pages. In blog/archives or single posts showing those metas is up to the user.
* Imp: co-authors plus plugin integration
* Imp: add horizontal widget area before the footer.
* Imp: add early check on php version (min 5.3) and wp version (min 4.5)
* Imp: turn slider .czrs-title from H1 to H2
* Imp: fix various form plugins compatibility
* Imp: improve woocommerce single product tabs style
* Imp: added various dismissable placeholders and notices on front with direct link to the live customizer.

= 2.0.26 February 13th 2018 =
* Fix: tagline visibility in mobiles when shown in desktop topbar.
* Fix: topbar menu not displayed in fresh installs.
* Fix: move hardcoded contact-info CSS rule into theme stylesheed.
* Fix: fix RTL article's hierarchical tax separator.
* Fix: fix checkbox/radio compatibility with WPForms by reverting the webkit appearance override.
* Imp: allow pointer events on scroll if submenu opened(hover) CSS
* Imp: avoid possible issues with very old PHP versions in menu customizer

= 2.0.25 February 1st 2018 =
* Imp: slightly decrease the font-size of the contact info block

= 2.0.24 January 31st 2018 =
* Fix: display the 404 content only when we're really in the 404 context
* Fix: make author bio RTL compliant
* Fix: RTL breadcrumb separator displayed with wrong direction.
* Fix: RTL submenu default opening direction
* Imp: Woocommerce - allow lightbox effect and smartload in product short description
* Imp: RTL submenu compliance 1) submenu animations 2) caret rotation adjustment when is RTL
* Imp: Woocommerce - better u/o list styling for the product short description
* Imp: remove body itemprop itemtype structured data. Fix issues with missing microformats reported by the google-search-console in static pages
* Added: new options to display contact informations : opening hours, phone and email in the topbar
* Added: a new option to choose on which devices ( desktops, mobiles, both ) the topbar should be displayed

= 2.0.23 January 16th 2018 =
* Fix: tagline not displayed in the header. fixes #1389
* Fix: wording typo in the featured pages description placeholder

= 2.0.22 January 16th 2018 =
* Fix: vertical menu items with children and expand on click not styled when using the Font Customizer
* Fix: the number of feat. pages by line option disappearing after selection in the customizer
* Fix: animated underline not removable in some navigation menus. Also do not underline current menu item when the underline hover effect option is disabled
* Fix: WP icon font-family possible override with the pro Font Customizer
* Fix: list in wc product description missing list style type
* Fix: slider caption not centered in ipad Mini
* Fix: clicking menu items with children and no URL bring to 404
* Fix: remove unwanted vertical separator before comments link in single posts
* Improved: improve mobile menu horizontal alignment. fixes #1380
* Improved: full page search form focus/blur on overlay open/close. fixes #1374
* Improved: upgraded the Font Awesome icon set to its latest version

= 2.0.21 December 27th 2017 =
* Fix : possible "headers already sent" error in WordPress admin

= 2.0.20 December 24th 2017 =
* Fix : WooCommerce Terms and Condition checkbox in Checkout page is not really visible
* Fix : dropdown menu-item word-break property set to break-word. Fixes
* Improved : Font Customizer, better UI and improved performances

= 2.0.19 December 20th 2017 =
* Fix: decrease regular submenu top to 15px. fixes #1333
* Fix: apply margin-bottom to the right wrapper element, as part of the fix for #1331
* Fix: avoid adding slider metabox to attachment which are not images. fixes #1317
* Fix: widget categories title transformed to uppercase. apply the uppercase rule only to its list items also remove the bold font-weight. fixes #1309
* Fix: always display the comment form before comment list (if any)
* Fix: slider cta hiding text. fixes #1299
* Fix: related posts height after the content with two sidebars fixes #1304
* Fix: fix search icon not appearing in the topbar. fixes #1324
* added: a boxed layout options for the header, the content and the footer
* added: new option to allow a menu dropdown on click for mobile menu and side menu. Enabled by default on mobile devices

= 2.0.18 November 20th 2017 =
* Fix : WP 4.9 Code Editor issue could impact the custom css customizer option when checking errors in the code

= 2.0.17 November 15th 2017 =
* Fix: post/page layout meta box must show the contextualized default layout
* Fix: fix header/overlay search label not custom skinned
* Fix: fix potential slider captions overlapping on page load
* Imp : more precis title of the topbar menu
* Imp: add new options to control the singular blocks location. author box (single post), related posts (single post), comments ( single posts and pages )
* Imp: add mobile header search location option

= 2.0.16 November 6th 2017 =
* Fix : label padding for comment text area applied to other inputs
* Fix : input white background applied to unwanted selectors
* Fix : mCustomScrollbar => the scrollers are positionned at the bottom on the first instantiation
* Fix : fix grid items opacity 0 not applied on init when infinite scroll is enabled
* Fix : fix better check on whether Customizr is using or not concatenated scripts

= 2.0.15 November 4th 2017 =
* Fix : WooCommerce, make wc-cart header button act like a dropdown only when displaying the wc-cart widget (so not when displayed in the mobile header)fixes #1274
* Imp : style of the comment text area. Fixes #1268
* Added : Doc search in the theme admin page "About Customizr"

= 2.0.14 October 29th 2017 =
* Fix : update notification showing again despite dismiss. Fix #1267

= 2.0.13 October 27th 2017 =
* Fix : fix some woocommerce form layout issues. fixes #1243
* Fix : WP 4.9 compatibility => in WP v4.9, the control options are not wrapped in the params property but passed directly instead
* Fix : image description in attachment not correctly displayed. fixes #1231
* Fix : single attachment date meta should not be a link. fixes #1233
* Fix : added missing retina (x2) placeholder images
* Fix : don't collapse mobile mene when scrolling fixes #1226
* Fix : slider caption text size still too big on mobile. fixes #1235
* Fix : update the way we get woocommerce cart url according to the new api + backward compatibility. fixes #1223
* Fix : fix possible wrong responsive images URL for sliders in multisite installs. fixes #1247
* Fix : remove the active callback of the front page content section in the customizer. Fixes #1252
* Fix : WP4.9 compat customizer additional CSS default. fixes #1255
* Fix : hover style on comment date links. Fixes #1114
* Fix : if comments are displayed by the user ( option is checked ) always show comment history, even if comment are closed. Fixes #1253
* Fix : removed no results and 404 quotations, probably wrongly attributed to authors. Fixes #1142
* Fix : make sure the 404 content is always displayed when is_404() even if have_posts() is true. Fixes #1260
* Imp : better the way to store when the user started using the theme
* Imp : performance improvement by implementing lazyloading for the main slider. Images are loaded when the become visible. New specific option in advanced > performance
* Imp : always close the mobile menu expanded when resizing
* Imp : increased the .comment_link font-size in related posts
* Imp : an anchor 'linear' scroll effect can be set by adding the attribute data-anchor-link="true" to a link
* Imp : add custom page template for the modern style. fixes #1209
* Imp : footer credits translation strings and link title.
* Imp : footer colophon is now text-align:center for smartphones in portrait mode <=> media-breakpoint-down(xs)
* Imp : bulleted and numbered lists formatting. Second line should be indented to the start of the text of the first line. added more space before and around. fixes #1102 #1183 #1224 #1228
* Imp : wraps the_content() in the div.czr-wp-the-content element in the templates printing the wp content : singulars ( including attachment ) and plain post grid
* Imp : add specific model and templates to render the post attachment of type image content
* Imp : slider nav dots made smaller for mobiles @media-breakpoint-down(xs)
* Imp : better styling for post metas. Fixes #1113
* Added : a layout field meta box option for attachments. => if the layout is not set for a particular attachment, it will be inherited from the parent ( which is the current behaviour )
* Added : the anchor scroll for the comment link in single posts
* Added : new option to control the visibility of the slider navigation bullets. Implement #1207
* Added : [pro] head and footer script editor in the customizer > advanced panel > additional scripts. Can be used to add Google Analytics code or any custom scripts or stylesheets
* Added : [pro] Add gallery carousels for gallery post formats in some lists of posts

= 2.0.12 October 12th 2017 =
* Fix : Infinite scroll Uncaught TypeError: Cannot read property 'element' of undefined at Scroller.maybeLoadMejs

= 2.0.11 October 9th 2017 =
* Fix : global skin CSS not printed when no custom header skin. Fixes #1215.
* Fix : various html fixes like duplicated ids or data attributes
* Fix : featured pages not translated by wpml. Fixes #1205
* Fix : don't update the defaults when wp_installing()
* Fix : preview error on singulars. Fixes #1194
* Fix : typo in customizer controls
* Fix : slider caption elements, default Fittext minsizes too high. Fixes #1191
* Fix : link whole slide not including the caption. Fixes #1140
* Fix : colors of the search form in dark overlay. Fixes #1185
* Fix : closing slide's title h1 html tag. Fixes #1188
* Fix : hamburger too dark on hover. fixes #1200
* Fix : hamburger lines taking a 2px height randomly
* Fix : logo / title stays shrinked when slowly scrolling up. fixes #1199. fixes #1192
* Imp : slider bullets closer to the bottom and margin set in em
* Imp : improve ol/ul margins in .tc-content-inner (.entry-content). Fixes #1183. Also slightly improve the cite element style.
* Imp : set shrinked logo height with max-height instead of height => to inherit the animation
* Imp : snaked submenu caret moved and rotated on the relevant side when "snaking"
* Updated : footer credit links to customizr theme page instead of presscustomizr home page
* Added : header custom back/fore-ground color options in modern style

= 2.0.10 September 29th 2017 =
* Fix : typo in customizer controls
* Fix : Slider caption elements, default Fittext minsizes too high. fixes #1191
* Imp : slider bullets closer to the bottom and margin set in em
* Fix: link whole slide not including the caption. Now this option behavior is consistent among classic and new style. fixes #1140
* Fix: colors of the search form in dark overlay. Fixes #1185
* Fix: closing slide's title h1 html tag. Fixes #1188
* Imp: improve ol/ul margins in .tc-content-inner (.entry-content). Fixes #1183 Also slightly improve the cite element style.

= 2.0.9 September 20th 2017 =
* Fix: handle user's singular featured image height in singular
* Fix: correct horizontal positioning of the primary navbar menu
* Fix: fix slider textual fields wrong truncation
* Fix: featured pages, js error when not in home and front fpu js cached with w3tc
* Imp : Gallery img sizes
* Imp : improved customizer js code
* updated : default footer credits link to Customizr Pro
* Updated : about admin page

= 2.0.8 September 14th 2017 =
* Fixed : Infinite items not shown in IE11 (but shown in EDGE) due to a js typo
* Imp : improved customizer js code
* Updated : about admin page

= 2.0.7 September 7th 2017 =
* Fix: menu centered wrongly displayed in ie/edge. Fixes #1163
* Fix: Menu centered in desktop => scrolling up and down close to top is not well handled. Fixes #1161
* Fix: RTL : search icon not properly left aligned on full screen search. Fixes #1159
* Imp: Submenus items on mouse hover - reveal faster. Fixes #1154

= 2.0.6 August 31st 2017 =
* Fix: add menu btn was not shown when secondary menu associated but no sidenav shown. fixes #1125
* Fix: fix singular thumbnail vertical spacing. fixes #1127
* Fix: fix tagline cut off. fixes #1128
* Fix: fp imgs always centered, fix handling slider not js centered
* Fix: js-centering class to the classical grid figure to better target them in js
* Fix: fix author meta displaying nicename instead of displayname. fixes #1148
* Added : [Pro] Infinite Scroll option for home, categories and search results
* Imp: allow search full page close on escape key pressed
* Imp: implement new form style. fixes #1122
* Imp: add entry-media__holder class to the grid figure (homogeneity)

= 2.0.5 August 2nd 2017 =
* Fix: fix woocommerce generatinc php notice
* Fix: fix CSS conflict with ui-datepicker-calendar
* Fix: sticky logo option was not displayed in the customizer
* Fix: display notice for socials in header in the right context

= 2.0.4 July 26th 2017 =
* Fix: fix missing max-width style for logo w forced dims fixes #1101
* Fix: fix sidebars list widgets indentatio
* Fix: CSS handling of screen reader text fixes #1103 bullet 3
* Imp: add menu button if not menu visible in the header
* Imp: various performances improvements when loading front end resources
* Add: an option to make the dark overlay optional in the modern style slider

= 2.0.2 July 23rd 2017 =
* fixed : child theme stylesheet wrongly enqueued
* Imp: main content mobile blocks reorder via flexbox
* Imp: fix comments date alignment fixes #1073

= 2.0.1 July 23rd 2017 =
* Fix : WooCommerce compatibility : Grid title truncation might affect products in product archives ( fixes #996 )
* Fix : Added back the menu locations customizer section
* Fix : grid caption background issue on mouse hover
* Fix : fix non existing function as __ID filter callback
* Added : style option in the customizer

= 1.4.0 June 22nd 2017 =
* Fix: fix menu center resulting aligned to the left on IE
* Fix: remove old theme favicon control - is handled in js
* Add: modern style compatibility for Featured Pages, Font Customizer, Grid Customizer, Menu Customizer, Footer Customizer

= 1.3.8 June 9th 2017 =
* fixed : fix use of the add_editor_style wp function : needs relative paths
* fixed : Featured Pages, do not copy back activation-key related classes in addons mode
* fixed : Font Customizer, avoid double slashes when defining wfc base url. Double slash might hurt caching plugins as when specifying the path of the scripts to treat in a different the double slash might make the match fail. Which is what happens with w3tc for example.
* improved : Featured Pages, fix compatibility with polylang-pro
* updated : Font Customizer, wfc customizer colors
* improved : Font Customizer, removed the requirejs dependency in conflict with WP4.8
* improved : Font Customizer, deactivate the add_editor_style callback on after_setup_theme. Was not used in the editor and could trigger errors. Fix #50
* improved : Footer customizer, fix compatibility with polylang-pro
* improved : Slider, enqueue select2 reseource only when customizr theme need them using the new action hook 'tc_slider_metabox_added'

= 1.3.7 May 30th 2017 =
* fixed : Polylang-Pro compatibility
* fixed : images smart load, fix race condition
* fixed : WooCommerce premium plugins: fix backend conflicts

= 1.3.6 May 5th 2017 =
* Fix: use wp_sprintf to avoid Warning sprintf(): Too few arguments. Fixes #875
* Fix: fix header layout center - menu position deps in the customizer. Fixes #879
* Fix: fix select2.js compatibility issue in woocommerce custom post-types admin
* Fix: don't display the single post featured image if a slider is displayed

= 1.3.5 April 7th 2017 =
* Fix: fix logo centered - menu centered not working non front ( fixes #868 )
* Imp: add theme support for wc gallery zoom lightbox and slider ( fixes #871
see: https://woocommerce.wordpress.com/2017/02/28/adding-support-for-woocommerce-2-7s-new-gallery-feature-to-your-theme/ )

= 1.3.4 March 31st 2017 =
* Fix : fix segfault with php7.1+ when opcache enabled
* Fix : Featured pages, add required post id param to the_title filter
* Imp : allow menu centering when logo/title centered
* Imp : added a utility czr_fn_is_front_help_enabled() in init.php

= 1.3.3 March 17th 2017 =
* Fix : do not show first attachment as thumb in single context fixes #815
* Fix : fix reference to maybe undefined wp in front js fixes #820
* Fix : fix comment bubble bottom arrow when custom color selected fixes #822
* Imp : added aria-label to button - support accessibility
* Imp : enabled accessibility for social icons
* Imp: move theme Custom CSS option to WordPress embedded one fixes #818
* fix : prevdem mode should be turned off when user starts customizing
* fix : user defined WP core settings like show_on_front should be preserved if customizing in prevdem mode
* fix: slider of posts revert - show post attachment if no featured image fixes #850
* fix : bugs on partialRefresh.czr. some placement have no containers
* fix: allow using first attachment as thumb for slider of posts in pages
* Fix: prevent colophon inner container enlarging page in certain vps see #853
* improved czr_fn_get_raw_option : added an option to retrieve the not cached option value from database. Typically used when getting the template name. Should fix #844
* added match Media polyfill.
* Updated the parallax jquery plugin => added a matchMedia option set to 'only screen and (max-width: 768px)' by default
* imp : allow child theme user to override the slide-placeholder image
* updated it_IT translation fix #842
* Imp: display thumbnail in single pages like we do in single posts fixes #738
* added : theme support for selective refresh for widgets
* Imp: display recently update notice in any in grid tiles below the post

= 1.3.2 February 20th 2017 =
* Fix: Featured Pages, always enqueue holder.js in customizer preview
* Imp: Featured Pages, for plugins compatibility apply the_title filter to the fpu title
* Fix: Featured Pages, avoid blank lines before DOCTYPE declaration
* Imp: code improvement for the footer credits customizer
* Imp: customizer improvements for the front slider of posts
* Fix: customizer javascript error when customizing the social links
* Improved: customizer social links module user interface
* Fix: fix potential option inconsistencies with some hosts. fixes #810
* Imp: rtl - fix update notice positioning in admin. fixes #800
* Imp: improve bootstrap plugin compatibility with the slider. fixes #737
* Removed : Swedish translations sv_SE, now part of the translation pack automatically downloaded from wordpress.org.
* Imp: add Array.from js polyfill for customizer control js
* Fix: IE11 (at least) : potential customizer breaks when trying log in the console

= 1.3.1 February 1st 2017 =
* fixed : compatibility issue with PHP<5.5
* fixed: customizer header partial refresh not correctly working

= 1.3.0 January 31st 2017 =
* fixed : fix print of the edit post link in the pro grid customizer
* fixed : trying to get property of non-object php notice when setting up WooCommerce
* Imp : add Customizr Pro compatibility for the yoast SEO breadcrumb
* Imp : improve loading performances for Pro menu customizer and Feat. Pages Unlimited to enqueue only the necessary resources
* Imp :introduced Poppins as the new default Google font
* Imp : changed default body line-height in pixelds to 1.6em
* Imp : new screenshot
* Imp : implemented an enhanced social links module in the customizer
* Imp : improved compatibility with Woothemes Sensei plugin fixes #759
* Imp : use lower tc-page-wrap and tc-sn z-index for compatibility reasons fixes #762
* Imp : button toggle nav positioning improvments
* Imp : added a notice for for freshly created menu not yet visible in the header main location
* Imp: improve side menu positioning depending on the header layout

= 1.2.39 January 22nd 2017 =
* Fix: Featured Pages Unlimited, escape title attributes used in fp round-div and readmore button
* Fix: fix default page menu behavior when dropdown on click submenu open #730
* Imp: fix plugin php7 checker (wrong) compatibility issue #727 , #719
* Imp: improve WooCommerce compatibility + allow shop layout selection #733
* Fix: small tweak to the header cart WooCommerce CSS #733
* Fix: add tc-center-images body class only when tc_center_img option true #735
* Fix: fix superfluous bracket in font-awesome icons inline style #739
* Fix: escape title attributes used in fp round-div and readmore button #743
* Imp: various bootstrap>2.3.2 compatibility improvements fix #742 #737 #746
* Imp: img to smartload must have an src which matches an allowed ext #747

= 1.2.38 January 6th 2017 =
* fixed : correctly handle sizes attribute when smartloading resp imgs
* fixed : back to top arrow position option
* fixed : prevent paging info duplication in wc breadcrumb
* fixed : avoid unbreakable woocommerce product labels ( #713 )
* improved : encode pipes when requesting multiple gfont families
* improved : avoid img smartload php parsing in ajax requests
* improved : rightly handle sizes/data-sizes attribute replacement in php
* improved : use modern window.matchMedia do determine the viewport's width ( #711 )
* improved : removed language packs already translated on translate.wordpress.org. German (Formal) (de_DE_formal), English (Canada) (en_CA), English (UK) (en_GB), Finnish (fi), French (Belgium) (fr_BE), French (France) (fr_FR), French (Canada) (fr_CA), Hebrew (he_IL), Italian (it_IT), Norwegian (Bokmål) (nb_NO), Polish (pl_PL), Romanian (ro_RO), Russian (ru_RU)

= 1.2.37 December 29th 2016 =
* updated : theme screenshot
* fix : Customizr Pro key Blank page with some specific server configurations

= 1.2.36 December 6th 2016 =
* Fix: js and css compatibility fixes for wp 4.7

= 1.2.35 October 28th 2016 =
* fixed : compatibility issue with php7
* fixed : better check for is customize preview()
* fixed : correctly instantiate front classes in admin building slider of posts fixes #662
* improved : add requestAnimationFrame polyfill required by several jquery-plugins fixes #665
* improved : get rid of the outdated menu item first letter styling
* improved : Imp: add back langpacks completed on translate.wordpress.org. Needed for Customizr-Pro. note: german GTE considered our german translation as formal german. The current de_DE.po(mo) is just a copy of the de_DE_formal.po(mo) pack.

= 1.2.34 October 24th 2016 =
* Fix: fatal error when customizing grid, due to wrong method reference after the prefix changes

= 1.2.33 October 19th 2016 =
* Fix: footer-customizr settings not working after latest update

= 1.2.32 October 17th 2016 =
* fixed : compatibility problems with plugins using the WP tinyMCE editor. Example : Black studio tinyMCE

= 1.2.31 October 17th 2016 =
* fixed : added back function tc__f() for retro-compatibility
* updated : grey.css is now the default skin, color #5A5A5A. No impact for previous version users using the old default skin (blue3.css)

= 1.2.30 October 15th 2016 =
* added : in single post, new wp filter : apply_filters( 'tc_single_post_section_class', array( 'entry-content' )
* added : in single post, new wp action : do_action( '__after_single_entry_inner' )
* added : demo singleton class CZR_prevdem
* added : demo images in inc/assets/img/demo
* Imp: group files in 4 main files to load in inc/
* Imp: add fp and round-div comp code with plugins running bootsrap3+ fixes #640
* Fix: fix js issue for pages with no header (dropdown placement)fixes #643
* Imp: replace class prefixes from TC to CZR
* Imp: change function names prefix from tc to czr
* Imp: add parallax classes and js only when slider exists
* Removed : language packs translated on wp.org

= 1.2.28 September 14th 2016 =
* added : a parallax scrolling option for sliders. Enabled by default.
* added : the waypoint js library (v4.0.0)
* changed : slide loader icon is enabled by default
* fixed : center the rectangular thumbs with golden ration only in post lists, not in single posts
* improved : added compatibility code for Super Socializer plugin

= 1.2.27 August 24th 2016 =
* Fix: improve llms plugin compatibility
* Fix : don't add_editor_style to WYSIWIG editor if no google fonts have been picked by user
* Fix: theme activation issue fixed for Unlimited Sites plan

= 1.2.26 August 23rd 2016 =
* Fix: tinymce custom style conflicts (detected with the ACF WP plugin ) with tinymce 4.x api. Fixes #626
* Fix : customizer terms array (categories/tags pickers options) not being updated on term delettion. fixes #620
* Fix: compatibility with  WP Ultimate Recipe plugin
* Fix : compatibility with Lifter LMS
* Improved : use WooCommerce breadcrumb code in wc contexts
* Improved : remove it lang files - will use the ones available on wp.org
* Added : new option - customize back to top - arrow position left or right

= 1.2.25 August 22nd 2016 =
* fixed : fixed blank admin fatal error "Can't use method return value in write context "

= 1.2.24 August 22nd 2016 =
* Fix: Feat. Pages, don't try to use a non existing wmpl function (missing wpml-addon)

= 1.2.23 August 22nd 2016 =
* Fix: Theme activation issue fixed for Unlimited Sites plan
* Checked : WP 4.6 100% compatible

= 1.2.22 May 6th 2016 =
* Fix: Pro Slider : do not try to access a $POST (array) field not set when saving the post meta
* Fix: Pro Grid Customizer : effect6 align the entry-summary to the middle
* Fix: remove grunt live reload script
* Fix: border-collapse specify 'separate' instead of 'initial': Opera fix should work with IE too, needs further tests
* Fix: fix wp media insert in front fixes #605
* Fix: fix woocommerce variation not visible on a product page fixes #601
* Fix : rename Finnish translations to match Wordpress core
* Fix: fix potential issue with the dropdown limit to viewport and some plugins
* Imp: be sure drodpown are displayed when overflowing the header/navbar
* Imp: allow wc-cart in the header ajax update

= 1.2.21 March 14th 2016 =
* Fix : typo in the help page

= 1.2.20 March 14th 2016 =
* Update: translations ru_RU and id_ID
* Fix: calendar widget style in footer with multiple widget instances
* Fix: disable smoothscroll touchpad support by default
* Imp: move the woocommerce header options, option do display the shopping cart on scroll
* Imp: wp 4.5 compatibility better rendering of the rating block in the customizer
* Fix: do not display menu-button when mobile+scrolling+sticky+not_allowed fixes

= 1.2.19 February 17th 2016 =
* Add : 3 new social icons: VKontakte, Yelp, Xing
* Imp: woocommerce icon cart now rendered with font-awesome
* Imp: remove outdated old ie fixes in the head
* Imp: move front and back icons to Font Awesome set
* Add: add customizer setting to optionally load font-awesome resources
* Imp: logo - replace previous upload control in the customizer with cropped images control
* Updated: translation fr_CA
* Fix: slider - avoid caption increasing slides height
* Fix: amend missing comma in the previous commit
* Fix: better rendering of the social-block in sidebar and colophon when they take up more than one line
* Fix: amend typo in the new control css
* Fix: refine compatibility with old customizr versions
* Fix: make icons in singular post / page contexts skin based
* Fix: never display edit links in the customizer fixes #361
* Fix: avoid outline showing up on links click in ff (v44) fixes #538
* Fix: fix potential warning when using custom skins fixes #540

= 1.2.18 February 15th 2016 =
* Fix : lifetime package not properly displayed in admin

= 1.2.17 January 30th 2016 =
* Updated Italian translation plus a typo
* Add: new option - display woocommerce cart in the header when sticky fixes #499
* Fix: fix grid not considering custom max height in left sidebar layout
* Fix: fix smartloaded img not correctly displayed in some browsers fixes #534
* Fix: fix slider link with QTranslate X in pre-path mode thanks to @justinbb fixes #531
* Fix: fix broken link to the header's navigation doc
* Fix: fix broken links to theme's faq
* Fix: fix broken links to the slider docs
* Fix: fix broken links to the docs in class-fire-utils
* Fix: fix grid expanded post edit link not reachable fixes #286
* Fix: refine alignment when tagline not shown
* Fix: Featured Pages, dynamically retrieve the current theme (name/version)
* Fix: Grid Customizer, update gcto the latest Customizr's grid latest changes

= 1.2.16 January 23rd 2016 =
* Add: a few translation tr_TR thanks to @ghost
* Add : Indonesian translation. Thanks to Rio Bermano
* Fix : some Swedish translation strings. Thanks to Mia Oberg.
* Fix: fix post-metas hierarchical tax check when building button class
* Fix: prefer mysqli api to the mysql ones (deprecated) in sys-info fixes #508
* Fix: amend wrong documentation link in sidebar widget placeholder fixes #502
* Fix: fix jetpack's photon - theme smartload compatibility issue
* Fix: fix btt-arrow and scroll-down issue Also use more descriptive variable names. fixes #477
* Fix: fix disabling wc-header-cart to reset tc_user_options_style
* Fix: avoid smartload conflict with buddypress setting avatar img fixes #467 a)
* Fix: better html comments fix rare cases when some html comments were considered as server's directives. fixes #470
* Fix: skip URIS images among imgs to smartload fixes #463
* Fix: smarload preg callback - reverse strpos param order
* Fix: apply border bottom only to theme sidebars widget list item
* Fix: Footer Customizer : fix some strings not translated
* Fix: Font Customizer : remove closing php tag at the end of pure php files

= 1.2.15 December 10th 2015 =
* Added: WooCommerce cart in the header
* Added: Turkish (tr_TR) translation files
* Added: Front js - new js class to better place dropdowns submenus avoiding overflowing the window
* Added: Images compatibility with wp 4.4+. Added customizer options to disable the default responsive images behaviour for slider and thumbnails.
* Fix: allow smartload in wp 4.4 (responsive images)
* Fix: alternate layout is available only if thumb position is right/left. Also fixes the alternate layout thumbnail's height control visibility
* Fix: do not display colophon's back to top text when back to top arrow button is enabled
* Fix: rtl colophon's layout fixes
* Fix: remove unneeded css code for the secondary menu dropdown top arrow
* Fix: allow smartload when jetpack's sharing enabled
* Fix: fix some issues regarding to smartload and centering imgs
* Fix: show second-menu dropdown-submenu arrow anyways
* Fix: WPML customizr option name
* Fix: allow logo transition only when both w,h are available.
* Fix: fix padding for the first menu-item in responsive menu
* Fix: remove unsupported turkish translation files
* Fix: Front-js - new js event triggered for sticky header and sidenav
* Fix: limit dropdown top arrow adjustment to the tc-wc-menu
* Fix: some CSS fixes for parent dropdown submenus in secondary menu
* Fix: allow slides translations in post/pages
* Updated : Dutch translation. Thanks to Helena Handa
* Updated: Brazilian Portuguese translation
* Updated: japanese translation
* Updated Farsi translation
* Improved : disable all front end notices when customizing

= 1.2.14 November 17th 2015 =
* Fix various minor issues for the slider

= 1.2.13 November 16th 2015 =
* WPML compatibility
* Improved: disable the front end notices when customizing
* Updated: Norwegian translation. Thanks to Marvin Wiik.
* Fix: better way to check if the search query has results
* Fix: the-events-calendar: fix showing the content twice in single event pages fixes #396
* Fix: display header in multisite signup/activate pages
* updated Brazilan Portuguese. Thanks to Helena Handa.

= 1.2.12 November 7th 2015 =
* Footer : added Powered by Wordpress. Disabled by default.
* Footer : added options to target the links in a new window/tab

= 1.2.11 November 4th 2015 =
* Add: Added a dismissable help notice on front-end in post lists about using img smart-load
* Add : add Powered by Wordpress in footer credits
* updated : German translation, thanks to Martin Bangermann
* Fix: menu display element, cast element classes to array fixes #372
* Fix: Deep link to customizer menu panel in a control description fixes #244
* Fix: Deep link to the featured page control in the removable front end block fixes #246
* Fix: better handling of the simple-load event triggered on holders fixes #377

= 1.2.10 October 16th 2015 =
* fix : better support for php versions < 5.4.0

= 1.2.9 October 16th 2015 =
* fix : warning error in the customizer

= 1.2.8 October 16th 2015 =
* added : performance help notice on front-end for posts/pages showing more than 2 images
* updated : Italian Translation thanks to Giorgio Riccardi http://www.giorgioriccardi.com/
* fix: better support for Visual Composer, prevent conflicts with anchor links in visual composer elements
* fix: better support for The Events Calendar, events list view fixes #353
* fix: better support for JetPack's photon, load imgs from cdn
updated Polish translation. Thanks to Krzysztof Busłowicz
* fix : better retro compatibility for the customizer preview for WP version under 4.1
* fix: Select a submenu expansion option disappears #340
* fix: allow control deeplink in the customizer
* fix: limit previous fix to ie9 and below
* fix: slider-controls always visible in ie9- In such browsers the opacity+transition doesn't really work fine, let's make them always visible
* Fix: Featured Pages : hide theme's skin style buttons when required
* Fix: Featured Pages : Customizer- hide thumbnails related sub-options when 'Display
* Fix: Featured Pages : Customizer - hide 'slave' options on wp ready when 'master' options

= 1.2.7 September 30th 2015 =
* added: new social link - email
* added: new option, filter home/blog posts by categories
* added : info box at the bottom of the slides table on how to add another slide
* added : in customizer > Front Page > Slider, make some "sub" slider height options dependant on the actual user define slider height
* updated: translation he_IL.po
* fix: let sticky-footer detect the golden-ratio is applied to the grid
* fix: exclude links wrapped in an underlined span from the eligible externals
* fix: sticky-header: logo re-size on scrolling when needed fixes #314
* fix: avoid navbar-wrapper overlapping title/logo in mobiles when no site-description is shown for ww<767px

= 1.2.6 September 21st 2015 =
* added: New feature - display a slider of recent posts on home
* fix: RTL initial position of the small arrow in the accordion(JTS)
* fix: broken update notice in edit attachment page fixes issue #248
* fix: display slider notice only on the demo slider fixes issue #251
* fix: Add back the Google Font img in the Customizer fixes issue #285
* fix: Woocommerce's product tabs not showing if Smooth scroll on click enabled Fixes issue #258
* fix: Allow the expanded grid title to be translated with qtranslate
* fix: include pages in search results when including cpt in post lists Fixes issue #280
* fix: expand last published sticky post in the grid
* fix: disable link smoothscroll in woocommerce contexts See issue #258
* Impr: Footer Customizer better lang plugins compatibility
* Impr: Footer Customizer allow html in copyright and credit text
* Fix: FPU, fix not refreshing thumbnail when post trashed
* Impr: FPU, Allow fp img smartload when relative Customizr option is enabled

= 1.2.5 August 24th 2015 =
* fix : issue #242 https://github.com/Nikeo/customizr/issues/242 : Effects common to regular menu and second horizontal menu where not visible when the regular menu was selected
* fix: qTranslate-X compat code improved (issue : https://wordpress.org/support/topic/featured-pages-and-qtranslate?replies=4 )

= 1.2.4 August 23rd 2015 =
* update : translations files
* fix : replace Customizr favicon by the WP 4.3 site icon. Handle the transition on front end and in the customizer for users already using the Customizr favicon. Retro compat : not applied if WP version < 4.3 + Check if function_exists('has_site_icon')
* fix : for control visibility bug since wp4.3
* add : a boolean filter to control the colophon back to top link
* fix : properly remove box around navbar when no menu is set Bug reported here: https://wordpress.org/support/topic/dispay-menu-in-a-box?replies=6
* fix : do not update the post meta as soon as you enter the add post page
* fix: Featured Pages : less expensive way to grab posts/pages to select as featured page.
* fix : Footer : default footer link was pointing to themesandco.com
* fix : Footer : make the credits filter return the html rather than print it
* fix : Menu Customizer : properly switch on slide-on-top effect when wp_is_mobile()

= 1.2.3 August 4th 2015 =
* fixed : polylang compat code according to the new customizer settings
* fixed : use original sizes (full) for logo and favicon attachments

= 1.2.2 July 31st 2015 =
* fixed : minor css adjustements for the menus
* added : a dismissable help notice under the main regular menu, on front-end, for logged-in admin users (edit options cap)

= 1.2.1 July 30th 2015 =
* fixed : expand on click not working for the secondary menu.

= 1.2.0 July 30th 2015 =
* added : new features for sliders : use a custom link, possibility to link the entire slide and to open the page in a new tab
* added : new default sidenav menu
* added : new optional secondary menu
* added : new default page menu
* added : new feature smoothscroll option in customize > Global Settings
* added : new feature Sticky Footer in customize > Footer
* added : a "sidebars" panel in the customizer including a social links section. (moved from global settings > Social links). Header and Footer social links checkboxes have been also moved into their respective panels.
* added : a theme updated notice that can be dismissed. Automatically removed after 5 prints.
* added : various optional front end help notices and placeholder blocks for first time users.
* added : (Featured paged) the theme skin is now available for the buttons
* fix : avoid blocks reordering when they contain at least one iframe (avoid some reported plugin conflicts)
* fix : video post format, show full content in alternate layout
* fix : display slider-loading-gif only if js enabled
* fix : display a separator after the heading in the page for posts (when not home)
* fix : html5shiv is loaded only for ie9-
* fix : dynamic sidebar reordering of the sidebar was not triggered since latest front js framework implementation improved : used of the tc-resize event for all resize related actions added : secondary menu items re-location for responsivereplaced : (js) 'resize' event by the custom 'tc-resize'
* fix : anchors smooth scroll - exclude ultimate members anchor links
* changed : customize transport of the header layout setting is now 'refresh'
* improved : modernizr upgraded to the latest version
* improved : customizer preview is refreshed faster

= 1.1.13 July 10th 2015 =
* fix : slider creation/edition bug when sorting slides due to latest slider update.

= 1.1.12 July 6th 2015 =
* added : enhancements for Customizr Pro updates ( activation key Api requests )
* fix: do not try to access maybe not existent TC_featured_pages class
* fix : custom link not displayed in admin slides table
added grunt commands
* fix: slider front and backend improvements
* fix: Use the global post variable in place of get_post for wp backward compatibility
* fix: do not justify woocommerce product titles
* fix: better check on wheter display or not attachment content

= 1.1.11 June 25th 2015 =
* fix : re-introduce btt-arrow handling in new front js
* fix : fix external link on multiple occurrences and exclude parents

= 1.1.10 June 19th 2015 =
* fix : drop cap not working with composed words including the '-' character
* fix: allow img smartload in mobiles
* fix: new emoji core script collision with svg tags => falls back to classic smileys if <svg> are loaded on the pages (by holder.js)
* fix: do not add no-effect class to round-divs when center images on
* fix: prevent hiding of selecter dropdown
* fix: use original img sizes
* fix: some ie8 fixes for the new front-js
* fix : reset margin for sticky header was not using the right variable
* fix : close tc-page-wrapper before wp_footer() to avoid issues with wp admin bar
* fix: when unhooking tc_parse_imgs for nextgen compatibility use proper priority
* fix: better rtl slide controls and swiping(js)
* changed : replace load function by loadCzr() => load might be a reserved word
* updated Hebrew translion for V 3.3.26
* updated translations for v 3.3.26
* updated Hebrew translations f v3.3.26
* added : split main front js into parts
* added : js czrapp extendable object
* added : sticky header as a sub class of Czr_Base
* added : js event handlers for sidebar reordering actions
* added : cleaner class inheritance framework for front end js
* added : a div#tc-page-wrap including header, content and footer
* added : oldBrowserCompat.js file including map + object.create
* added : filter method to the Array.prototype for old browsers
* added : a simple event manager set of methods in the front czrapp js
* fix : post-navigation regression introduced while merging rtl code
* fix: better check whether print or not the widget placeholder script
* added : option filter and better contx retro compat to default
* updated : Swedish translation sv_SE.po

= 1.1.9 May 15th 2015 =
* fix: store empty() function bool in a var to fix a PHP version compatibility issue
* fix: use proper priority for tc_parse_imgs callback of the_content filter

= 1.1.8 May 15th 2015 =
* fix: grid customizer: properly pass sanitize_callback, fix issue with wp<4.1
* fix: grid customizer: customize compatible code with old wp versions
* fix : grid customizer: more contrast to the post titles with a darker background
* fix: polylang cast featured pages options to array, needed when no options are set
* fix: when deleting retina images don't forget the original attachment's retina version
* fix: remove btt-arrow inline style, rule moved in the skin css
* fix : fancybox in post images is 100% independant of fancybox in galleries
* added : japanese translation (ja). Thanks to Toshiyuki Tsuchiya.
* fix : remove smartload noscript tag
* fix : theme switcher visibility issue on preview frame ready event
* fix: properly filter get_the_content() for special post formats
* fix : localized params assigned to wrong script handle in dev mode
* fix : hide donate button ajax action not triggered
* fix : change order of elements on RTL sites. using is_rtl() to determine the order of specific elements, instead of creating dedicated rules in CSSFIX : correcting the left/right css rules for RTL sited. Thanks to Yaacov Glezer.
* added : less files updated with new rtl vars and conditional statements
* improved : code handling RTL priority for colophon blocks
* updated : Spanish translation
* improved : donate customizer call to action visibility
* improved : widget placeholder code
* improved : widget placeholder code
* updated : es_ES translation. Thanks to Angel Calzado.

= 1.1.7 May 4th 2015 =
* fix : don't show slider in home when no home slider is set

= 1.1.6 May 3rd 2015 =
* fix : revert private taxonomy not printed. Will be added back after more tests.

= 1.1.5 April 29th 2015 =
* fix : no post thumbnail option was not working for the post grid layout
* added : when the grid customizer is enabled, the user can now set a custom excerpt length without limitations
* added: support for the map method in the array prototype for old ie browsers -ie8
* Fix: use the correct post id when retrieving the grid layout
* improved : jquery.fancybox.js loaded separately when required
* updated : underscore to 1.8.3
* added : helper methods to normalize the front scripts enqueuing args
* updated : name of front enqueue scripts / style callbacks
* Fix: use amatic weight 400 instead of 700, workaround for missing question mark
* Fix: remove reference to the tag, use site-description tag
* Fix: display unknown archive types headings; use if/else statement when retrieving archive headings/classes immediatily return the archive class when asked for and achiFix: amend typo in the last commit
* Fix: disable fade hover links for first level menu items in ie
* Fix: add customize code and fix previous errors
* Fix: add gallery options, remove useless rewrite of gallery code
* Fix: scroll top when no dropdown menu sized to viewport and no back-to-top, don't refer to not existing variable
* Fix: consider both header borders and eventual margins when retrieving its height
* Fix : RTL-ing Pre-Phase : setting the correct direction of arrows
* Fix: disabling global tc_post_metas didn't hide metas
* Fix: cache and use cached common jquery elements
* Fix: don't print private taxonomies in post metas tags
* Fix: display other grid options and jumb to the blog design options in customize
* Fix : grid customizer effect-5 title style
* Add: sensei woothemes addon compatibility
* improved : single options can now be filtered individually with tc_opt_{$option_name}
* Add: optimizepress compatibility
* Add: basic buddypress support (don't show comments in buddypress pages)
* Add: partial nextgen gallery compatibility
* Add: tc-mainwrappers methods for plugin compatibilities
* Updated: class-content-post_navigation.php
changed: method TC___::tc_unset_core_classes set to public
Correcting arrows on tranlated phrases
* Add : Featured Pages php code improvements, new way to filter a single option with "tc_fpc_get_opt_{$option_name}" + some handy filters on featured pages number
* Fix: Featured Pages use stronger selectors for fp spans

= 1.1.4 April 17th 2015 =
* fixed : in the customizer, display back other grid options
* added a filter (boolean, default = true) to disable the footer customizer : 'tc_enable_footer_customizer'

= 1.1.3 April 13th 2015 =
* fixed : Black Studio TinyMCE Plugin issue. Load TC_resource class when tinymce_css callback is fired from the customizer

= 1.1.2 April 11th 2015 =
* added : support for polylang and qtranslate-x
* improved : load only necessary classes depending on the context : admin / front / customize
* changed : class-admin-customize.php and class-admin-meta_boxes.php now loaded from init.php.
* updated site name
* fix: reset navbar-inner padding-right when logo centered
* fix: override bootstrap thumbnails left margin for woocommerce ones
* added : helpers tc_is_plugin_active to avoid the inclusion of wp-admin/includes/plugin.php on front end
* added : new class file dedicated to plugin compatibility class-fire-plugin_compat.php
* updated : copyright dates
* fixed : minor hotcrumble css margin bug fix
* fixed : use the css class instead of h2 and remove duplicate
* fixed : Few corrections in the Italian translation
* fixed : allow customizr slider in the woocommerce shop page
* fixed : collapsed customizer panel
* fixed : gallery - handle the case "link to none" (was previously linked to the attachment page)
* added : sidebar and footer widgets removable placeholders
* added : better customizer options for comments. Allow more controls on comment display. Page comments are disabled by default.
* added : customizer link to ratings
* updated : he_IL.po Hebrew translation
* updated : readme changelog
* fixed : rtl customizer new widths and margins
* fixed : use '===' to compare with '0'.
* fixed : fix logo ratio, apply only when no sticky-logo set
* fixed : avoid plugin's conflicts with the centering slides feature: replace the #customizr-slider's 'slide' class with 'customizr-slide'
* fixed : user defined comments setting for a single page in quick edit mode
* fixed : pre_get_posts as action instead of filter
* fixed : hook post-metas and headings early actions to wp_head instead of wp
* fixed : minor css issues due to the larger width for the customizer controls
* fixed : infinite loop issue with woocommerce compatibility function
* added : tc-smart-loaded class for img loaded with smartloadjs
* added : make grid font-size also dependant of the current layout
* added : css classes filter in index : tc_article_container_class
* added : grid customizer in pro
* added : skin css class to body
* added : disabled WooCommerce default breadcrumb
* improved : better css grid icons
* changed : themesandco to presscustomizr
* updated : Swedish translation. Thanks to Tommy Wikström.
* updated : genericons to v3.3
* changed : attachment in search results is now disabled by default
* updated : layout css class added to body
* changed : .tc-gc class is now attached to the .article-container element
* changed : golden ratio can be overriden (follows the previous commit about this)
* changed : tc__f ( '__ID' ) replaced by TC_utils::tc_id()
* changed : tc__f( '__screen_layout' ) replaced by TC_utils::tc_get_layout( )
* changed : css classes filter 'tc_main_wrapper_classes' and 'tc_column_content_wrapper_classes' now handled as array
* improved : grid thumb golden ratio can be overriden
* updated : disable live icon rendering in post list titles if grid customizer on
* improved : customizer control panel width
* changed : grid controls priorities
* changed : class .tc-grid-excerpt-content to .tc-g-cont
* improved : larger customizer zone + some titles styling
* improved : get the theme name from TC___::$theme_name in system infos
* changed : split the edit link callback. Separate the view and the boolean check into 2 new public methods
* changed : some priority changes in the customizer controls
* improved : grid font sizes now uses ratios
* added :(fp) support for preview context in tc_get_theme_name()
* updated :(fp) site name and copyright dates
* Fix:(fp) use correct callback for fpc_text filter
* Fix:(fp) final lang plugins compatibility
* Add:(fp) initial lang plugins compatibility
* Fix:(fp) properly loading of textdomain when as addon
* Fix:(fp) recenter on fp block class changed
* Fix:(fp) better definition of js deps in front js enqueing 2
* Fix:(fp) italian translation update
* Fix:(fp) better definition of js deps in front js enqueing
* updated :(fc) added missing customizr skins
* updated :(fc) site name and copyright dates
* Fix :(fc) get the right theme name when previewing a non active theme
* Fix :(fc) properly loading of textdomain when used as Customizr Pro addon
* Fix :(fc) extend first-letter fix to customizr-pro
* Fix :(fc) front js register and enqueue jquery if necessary

= 1.1.1 March 27th 2015 =
* added : grid customizer : new options for titles and post backgrounds

= 1.1.0 March 24th 2015 =
* added : Grid Customizer
* updated to Customizr core v3.3.13

= 1.0.17 March 11th 2015 =
* added : customizer previewer filter for custom skins

= 1.0.16 March 9th 2015 =
* fixed : tc_set_post_list_hooks hooked on wp_head. wp was too early => fixes bbpress compatibility
* improved : tc_user_options_style filter now declared in the classes constructor
* fixed : bbpress issue with single user profiles not showing up ( initially reported here : https://wordpress.org/support/topic/bbpress-problems-with-versions-avove-3217?replies=7#post-6669693 )
* replaced in featured pages get_the_excerpt filter by the_excerpt
* fixed : better insertion of font icons and custom css in the custom inline stylesheet
* fixed : bbpress conflict with the post grid
* fixed : the_content and the_excerpt WP filters missing in post list content model
* fixed : smart load issue when .hentry class is missing (in WooCommerce search results for example)
* added : has-thumb class to the grid > figure element
* added : make the expanded class optional with a filter : tc_grid_add_expanded_class
* added : fade background effect for the excerpt in the no-thumbs grid blocks
* changed : TC_post_list_grid::tc_is_grid_enabled shifted from private to public
* improved : jqueryextLinks.js check if the tc-external element already exists before appending it
* fixed : .tc-grid-icon:before better centering

= 1.0.15 March 4th 2015 =
Fix slider img centering bug

= 1.0.14 March 4th 2015 =
Fix: array dereferencing fix for PHP<5.4.0
Fix: typos in webkit transition/transform properties

= 1.0.13 March 2nd 2015 =
* Upgraded to customizr v3.3.6
* added in Featured Pages a dynamic images centering feature as option

= 1.0.12 February 13th 2015 =
* Upgraded to customizr v3.3.1, safe for child theme users : https://github.com/Nikeo/customizr#changelog

= 1.0.11 February 13th 2015 =
* Upgraded to customizr v3.3.0, safe for child theme users
* Font Customizer : hide controls on load when they are not wrapper in zones, since wp 4.1+
* Feat. Pages Fix : use text-domain prefix for translations files
* Feat. Pages Fix : fp button display nothing if empty
* Feat. Pages Fix : perform tc_setup after_setup_theme when as addon in customizr-pro
* Feat. Pages Fix : temporary fix, don't include woocommerce products in fp
* Feat. Pages Fix : use a more proper filter to which pass the ->post_excerpt
* Feat. Pages Fix : don't retrieve already retrieved post/page object when getting the excerpt

= 1.0.10 January 23rd 2015 =
* Fix parse error due to an anonymous function syntax not supported by old version of php

= 1.0.9 January 22nd 2015 =
* Fix warning when attempting to load Google font in the admin editor

= 1.0.8 January 22nd 2015 =
* Updated to the core Customizr theme v3.2.12

= 1.0.7 December 23rd 2014 =
* Safer fix for the hidden font customizer controls. Cross browser compatible and compatible with  WP version 4.1+
* Updated to the core Customizr theme v3.2.10 : http://presscustomizr.com/whats-new-customizr-theme-v3-2-9/

= 1.0.6 December 21st 2014 =
* follow up of the font customizer controls not showing up. Additional delay added to init the sections

= 1.0.5 December 20th 2014 =
* 18db9f3 fix the api.reflowPaneContents bug in the font customizer

= 1.0.4 December 14th 2014 =
* b1e9173 Fix $logos_img instanciation bug in class TC_header_main#148

= 1.0.3 December 12th 2014 =
* 0402f83 fix customizr pro name to customizr-pro in config.json of FPU

= 1.0.2 December 8th 2014 =
* 9ef1370 FPU addon : fix the comment bubble bug. get_the_title() not used anymore.
* f806c18 pages with comments : enable the comment bubble after the title in headings
* 57ac308 admin css : change help buttons and icon to the new set of colors : #27CDA5 #1B8D71
* 786bbbc expand submenus for tablets in landscape mode
* d4bc5eb add a tc-is-mobile class to the body tag if wp_is_mobile()
* d3bb703 Fix the skin dropdown not closing when clicking outside the dropdown
* 094e0b2 Changed author URL to http://presscustomizr.com/
* c6611bb Merge branch 'eri-trabiccolo-android-menu' into dev
* d94fff6 Fix collapsed menu on android devices
* 605a462 Merge branch 'eri-trabiccolo-fp-edit-link' into dev
* 63c6aa0 Featured Pages: fix edit link
* 8e24584 Merge branch 'eri-trabiccolo-parent-menu-item' into dev
* 708b7b1 Merge branch 'eri-trabiccolo-hammer-issue' into dev
* Fix click on slide's call to action buttons in mobile devs
* 7b31410 Merge branch 'eri-trabiccolo-dev' for the sticky logo into dev
* cb77df3 add the title and notice to TC_Customize_Upload_Control
* 62bce18 add the title rendering for some control types
* 48891bb fix the $default_title_length hard coded value

= 1.0.1 December 8th 2014 =
* dd4bfe6 v1.0.1 grunt build
* 90d292e change gitignore settings
* fd10bbf setup the automatic updates and the activation key page admin
* 4b5df34 addons updates

= 1.0.0 December 6th 2014 =
* Initial release