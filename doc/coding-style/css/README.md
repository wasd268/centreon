# Coding Style Guide
## CSS
### OOCSS and BEM

We encourage some combination of OOCSS and BEM for these reasons:

* It helps create clear, strict relationships between CSS and HTML
* It helps us create reusable, composable components
* It allows for less nesting and lower specificity
* It helps in building scalable stylesheets

OOCSS, or “Object Oriented CSS”, is an approach for writing CSS that encourages you to think about your stylesheets as a collection of “objects”: reusable, repeatable snippets that can be used independently throughout a website.
BEM, or “Block-Element-Modifier”, is a naming convention for classes in HTML and CSS. It was originally developed by Yandex with large codebases and scalability in mind, and can serve as a solid set of guidelines for implementing OOCSS.

```html
<div class="centreon centreon-block">
    <h1 class="centreon-title">Adorable 2BR in the sunny Mission</h1>
    <div class="centreon-body_1">
        <p>Vestibulum id ligula porta felis euismod semper.</p>
    </div>
    <div class="centreon-body_2">
        <p>Vestibulum id ligula porta felis euismod semper.</p>
    </div>
</div>
```
* .ListingCard is the “block” and represents the higher-level component
* .ListingCard__title is an “element” and represents a descendant of .ListingCard that helps compose the block as a whole.
* .ListingCard--featured is a “modifier” and represents a different state or variation on the .ListingCard block.

### Formatting

* Use 4 spaces for indentation
* Prefer dashes over camelCasing in class names.
    * class: .some-class-name
    * id: #some-id-to-an-element
* Do not use ID selectors
* When using multiple selectors in a rule declaration, give each selector its own line.
* Put a space before the opening brace { in rule declarations
* In properties, put a space after, but not before, the : character.
* Put closing braces } of rule declarations on a new line
* Put blank lines between rule declarations

```css
/* bad */
.avatar{
  border-radius:50%;
  border:2px solid white; }
.one,.selector,.per-line {
    // ...
}

/* good */
.avatar {
  border-radius: 50%;
  border: 2px solid white;
}

.one,
.selector,
.per-line {
  // ...
}
```

### Border

Use 0 instead of none to specify that a style has no border.

```css
/* bad */
.foo {
  border: none;
}

/* good */
.foo {
  border: 0;
}
```
### Module

To set a css for a module, use underscore [_] to make the separation of the module identifier and the class

```css
.module-name_class-name {
  color: green;
}
```

**[⬆ back to top](#Coding-Style-Guide)**

**[← back to summary](https://github.com/centreon/centreon)**