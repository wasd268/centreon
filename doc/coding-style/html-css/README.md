# Coding Style Guide

## HTML

All tags and attributes are lowercase.

## CSS

Definition ideally as dashed name:
    class: .some-class-name
    id: #some-id-to-an-element

Both with lowercase characters (although classes are not case-sensitive, id's are!), the separator is minus [-]. You can use underscore [_] if it makes the separation of the identifier and the record id easier. E.g. my-id_33. It will become necessary to do so if you use UUIDs (which contain minus chars).

```css
span.success {
    color: green;
}
```