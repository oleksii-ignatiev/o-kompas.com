
=== WP Tournament Registration ===
Contributors: archaeopath
Tags: events, registration, event registration, competition, tournament, tournaments, competitions, sport, chess, hobby sport
Requires at least: 5.3
Tested up to: 5.7
Requires PHP: 7.0
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://ingram-braun.net/erga/donations/

A simple but highly configurable tournament registration form providing shortcodes for form, list, editor and export views. It is mainly intended for registering players with hobby sport events.

== Description ==

WP Tournament Registration is a plugin that provides shortcodes intended for registering players with hobby sport events. As a matter of fact, it was made for the championship of my [local chess club](https://schach-goettingen.de/). There are four shortcodes for different views:

* `[wptournregedit tournament_id="myID" /]` is an editor for the players' data.
* `[wptournregexport tournament_id="myID"]...[/wptournregexport]` is a plain text export filter
* `[wptournregform tournament_id="myID"]...[/wptournregform]` is a form where people can register with the competition. It covers some `[wptournregfield field="fieldname" /]` shortcodes which customize the desired inputs.
* ` [wptournreglist tournament_id="myID" /]` is a sortable list view of all approved participants.

All these shortcodes are customizable through a set of optional attributes. 

= Available fields =
* `address` [text]: self-explanatory.
* `affiliation` [text]: club or association.
* `approved` [checkbox]: entries become visible in in the list view or through the export filter only afer approved was checked.
* `birthyear` [integer]: self-explanatory
* `city` [text]: self-explanatory.
* `custom1`, `custom2`, `custom3`, `custom4`, `custom5` [text]: if anything is missing.
* `email` [text]: self-explanatory.
* `fee_is_paid` [checkbox]:  self-explanatory.
* `firstname` [text]: self-explanatory.
* `id` [read-only]: an integer that serves as primary key and is incremented with every registration. Use it to separate entries if people register twice.
* `ip` [read-only]: The remote IP of the submitter through `wptournregform`. May be useful for security checks.
* `lastname` [text]: self-explanatory.
* `message` [textarea]: self-explanatory
* `phone1` [text]: self-explanatory
* `phone2` [text]: self-explanatory
* `postcode` [integer]: self-explanatory
* `protected` [checkbox]:  if checked, all contact data of the particiapant are suppressed in list view.
* `rating1` [integer]: a rating that is used to calculate ranking lists.
* `rating2` [integer]: another rating that is used to calculate ranking lists.
* `time` [read-only]: a timestamp that records the exact date and time of submitting.
It is advisable not to misuse fields since there are some internal checks on the values. Use the custom fields instead. It is hard to say what the length of input fields is in characters as this depends on what is going to. But all are of reasonable length for their purpose.

= Attributes =
`wptournregedit`:

* `tournament_id` (required).
* `display_fields` a comma-separated list of field names in the order of view.

`wptournregexport`:

* `tournament_id` (required).
* `all` if set to any value the list shows all enries, otherwise the ones with a checked `approved` flag only.
* `class` adds a space separated list of custom CSS classes to the form.
* `css` adds a style attribute with custom CSS to the form.
* `id` adds a custom CSS id to the form.
* `fields_set` a comma-separated list of fields which get ignored if empty.
* `filename` the name of the export file.
* `format` a plain text string where all field names preceded by a percent sign (`%fieldname`) get replaced by the respective field values.

`wptournregform`:

* `tournament_id` (required).
* `backlink` adds a backlink to the plugin's project page.
* `class` adds a space separated list of custom CSS classes to the form.
* `css` adds a style attribute with custom CSS to the form.
* `id` adds a custom CSS id to the form.
* `disabled` submit and reset buttons are disabled if set to any value.
* `email` a comma-separated list of login names who get an E-mail notification if somebody registeres.

`wptournreglist`:

* `tournament_id` (required).
* `all` if set to any value the list shows all enries, otherwise the ones with a checked `approved` flag only.
* `backlink` adds a backlink to the plugin's project page if set to any value.
* `class` adds a space separated list of custom CSS classes to the form.
* `css` adds a style attribute with custom CSS to the form.
* `id` adds a custom CSS id to the list.
* `display_fields` a comma-separated list of fields on view in that particular order.
* `headings` a comma-separated list of column headings in that particular order.
* `protected_fields` a comma-separated list of fields that are suppressed if the `protected` flag is set in the database for a certain user.
* `notsortable` the columns are sorttable by default. This is suppressed if set to any value.

`wptournregfield`:

* `checked` checks a checkbox if set to any value.
* `class` adds a space separated list of custom CSS classes to the form.
* `css` adds a style attribute with custom CSS to the form.
* `id` adds a custom CSS id to the form.
* `disabled` disables the field if set to any value.
* `field` one of the field names (cf. list above).
* `label` the label of the field.
* `placeholder` the placeholder is shown in an empty field.
* `required` if set to any value the field is marked as required.

= Usage =

The first example is a registration form. Several `wptournregfield` shortcodes are wrapped by a `wptournregfield` one. You can put HTML elements between (fi. fieldsets) in order to design your form:

`[wptournregform tournament_id="my_tournament" css_id="my_tournament" email="subscription@example.com"]<p>Red labels indicate required fields!</p><fieldset><legend>Who you are</legend>[wptournregfield field="lastname" label="Family name" required="1" /][wptournregfield field="firstname" label="Christian name" required="1" /][wptournregfield field="affiliation" label="Club" required="1" placeholder="or 'free agent'" /][wptournregfield field="rating1" label="DWZ"  /]</fieldset><fieldset><legend>Your contact data (not to be published)</legend>[wptournregfield field="email" label="E-mail" /][wptournregfield field="phone1" label="Phone 1" required="1" /][wptournregfield field="phone2" label="Phone 2" /]</fieldset>[wptournregfield field="message" label="Your message" placeholder="Whatever you like to tell us." /][/wptournregform]`

The next instance is an editor for the data of the tournament. The non-approved players are highlihted in the selection list:

`[wptournregedit tournament_id="my_tournament" display_fields="approved,firstname,lastname,affiliation,email,id,time,ip,rating1,phone1,phone2,protected,custom1,message" /]`

Next is a list view. Normally you will make an password restricted full list for internal use and a small one for the public. Only approved players are on view:

`[wptournreglist tournament_id="my_tournament" display_fields="lastname,firstname,affiliation,email,phone1,phone2,message" headings="Last Name,First Name,Club,E-mail,Phone 1, Phone 2, Message" /]`

The next shortcode exports all participants into a csv list which than the is loaded into a tournament manager app (Swiss-Chess in this case). Here also non-approved players get exported. Wrap the field names in sections signs (`§`) in order to output the respective value.

`[wptournregexport tournament_id="my_tournament" all="1" format='"§lastname§, §firstname§";"§affiliation§";"";"";"§rating1§";""' linebreak="1" filename="swiss-chess.txt"]Download Swiss-Chess list[/wptournregexport]`

The following shortcode exports a list of all approved participants who have provided you with a mail address in a way you can directly copy and paste into a mail client. See FAQ for issues with some characters!

`[wptournregexport tournament_id="my_tournament" format='"§firstname§ §lastname§" LOWER_THAN§email§>,' fields_set="email" filename="mails.txt"]Download mail list (use BCC!)[/wptournregexport]`

== Installation ==

1. In your wp-admin (WordPress dashboard), go to Plugins Menu > Add New
2. Search for 'wp-tournament-registration' in search field on top right.
3. In the search results, click on 'Install Now' button next to WP Tournament Registration.
4. Once the installation is complete, click Activate button.

You can also install the plugin manually by following these steps:
1. Download the plugin zip file from https://wordpress.org/plugins/wp-tournament-registration/
2. In your wp-admin (WordPress dashboard), go to Plugins Menu > Add New
3. Click the 'Upload Plugin' button at the top.
4. Upload the zip file you downloaded in Step 1.
5. Once the upload is finish, click on Activate.

The development repository is hosted on [GitHub](https://github.com/CarlOrff/wp-tournament-registration).

== Screenshots ==

1. The input form
2. The editor
3. List view and two export buttons for different formats

== Frequently Asked Questions ==

= Is it compatible with PHP 8? =

Yes!

= How to escape characters in export formats? =

The shortcode is rendered as HTML which means that there are some protected characters. Fi. if you need tabsops, provide them as HTML entities (`&#9;` in this case).
A special issue is the `<`. The format method strips HTML tags (and some other things) in order to avoid injection of evil scripts, even as HTML entity. `LOWER_THAN` will be substituded by a `<`.

= Where is the settings page? =

No settings page at all! Everything is done with shortcodes.

= How about multisite? =

WP Tournament Registration employs a single database table on multisite installs. This is
desired behaviour. Fi., if a multisite provides different language versions of the same site, 
then you can provide a registration form in every language for the same tournament. For protecting
a site against others it is probably a good idea to namespace your tournament IDs somehow, eg. 
`mysite_mytournamentid` 

= Why does it work with JavaScript enabled ony? =

WP Tournament Registration does some checks to avoid spam.

== Changelog ==

= 1.1.2 =

* tested with WordPress 5.7
* small documentation fixes

= 1.1.1 =

* minor fix

= 1.1.0 =

* New feature: duplicate check on new participants.
* Removed deprecated `array_key_exists()` for PHP 8 compatibility.
* Removed deprecated array and string offset access syntax with curly braces.
* `css` attribute now works in `wptournregfield`.

= 1.0.0 =

* Initial Public Release