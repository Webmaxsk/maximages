# maximages
Images for any data object 

## Installation
```bash
composer require "webmaxsk/maximages:*"
```

You can add images to any Page via CMS. You can disable images for any Page subclass by adding config to mysite/_config.php:
```php
Blog::config()->allow_images = false;
Calendar::config()->allow_images = false;
CalendarEvent::config()->allow_images = false;
ErrorPage::config()->allow_images = false;
RedirectorPage::config()->allow_images = false;
UserDefinedForm::config()->allow_images = false;
VirtualPage::config()->allow_images = false;
```

You can add images to any DataObject too, just extend DataObject with ObjectImagesExtension.

## Usage
Add images to your template

```html
<% if SortedImages %>
	<ul class="small-block-grid-3">
	    <% loop SortedImages %>
	        <li>
	            <a href="$Link" title="$Title">
	               $CroppedImage(200,200)
	            </a>
	        </li>
	    <% end_loop %>
	</ul>
<% end_if %>
```

Add any lightbox you like, it is not included in this module!

## Example usage
check https://github.com/Webmaxsk/silverstripe-intranet-plate
