# Drupal module drupal/iframe_cookie_consent

Website content loaded in iframes from third party content providers, for example YouTube may set cookies and thereby
require a visitor's consent.

https://support.cookiebot.com/hc/en-us/articles/360003790854-Iframe-cookie-consent-with-YouTube-example

https://support.cookiebot.com/hc/en-us/articles/360003812053-Hide-and-show-content-based-on-the-visitor-s-consent

## In this module

This module adds Ckeditor plugin which convert Youtube iframe embeds to support cookie consent's. You can choose one of
these values: `preferences`, `statistics`, or `marketing` as data-cookieconsent attribute.

You will find these settings under Content authoring / Iframe Cookie Consent:

```
/admin/config/content/iframe_cookie_consent
```

## How to use

Go Configuration / Content authoring / Text formats and editors and edit text format which you want to use Iframe Cookie
Consent module.

```
/admin/config/content/formats/
```

Now under Enabled filters you will see Iframe cookie consent which you just enable

### Field support

YouTube video can be embedded to content in many way so the easiest way to support fields are create or modify field
template file.

Here is an example.

```twig
<iframe
  {{ attributes }}
  {% if url is not empty %}
    data-cookieblock-src={{ url }}
    data-cookieconsent="marketing"
    class=" cookieconsent-optin-marketing"

    {% if query is not empty %}
      ?{{ query | url_encode }}
    {% endif %}

    {% if fragment is not empty %}
      #{{ fragment }}
    {% endif %}"
  {% endif %}
  >
</iframe>
<div class="cookieconsent-optout-marketing">
   {{ 'Please <a href="javascript:Cookiebot.renew()">accept marketing-cookies</a> to watch this video'|t }}
</div>
```
