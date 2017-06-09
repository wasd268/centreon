# Coding Style Guide

## HTML

* All tags and attributes are lowercase.
```html
//bad
<!DOCTYPE HTML>
<div CLASS="menu">

//good
<!doctype html>
<div class="menu">
```
* Close all HTML elements.
```html
//bad
<section>
  <p>This is a paragraph.
  <p>This is a paragraph.
</section>

//good
<section>
  <p>This is a paragraph.</p>
  <p>This is a paragraph.</p>
</section>
```
* Close empty HTML elements.
```html
//bad
<meta charset="utf-8">
<input type="text">

//good
<meta charset="utf-8" />
<input type="text" />
```
* Always add the "alt" attribute to images.
```html
//bad
<img src="centreon.gif">

//good
<img src="centreon.gif" alt="centreon">
```
* Groups entities around equal signs
```html
//bad
<link rel = "stylesheet" href = "styles.css">

//good
<link rel="stylesheet" href="styles.css">
```
* The limit on line length must be 120 characters, 80 is better.
* Indentation
    * Do not add blank lines without a reason.
    * For readability, add blank lines to separate large or logical code blocks.
    * For readability, add 4 spaces of indentation. Do not use the tab key.
    * For readability, indent block elements, inline elements indentation is unnecessary
```html
//bad
<body>

    <h1></h1>
    <div>
    
    <h2></h2>
    <div></div>
    </div>
   
    <p></p>

</body>

//good
<body>
    <h1></h1>
    <div>
        <h2></h2>
        <div>
        </div>
    </div>
    <p>
    </p>
</body>
```

**[⬆ back to top](#Coding-Style-Guide)**

**[← back to summary](https://github.com/centreon/centreon)**