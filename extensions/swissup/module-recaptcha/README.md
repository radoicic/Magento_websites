# reCAPTCHA for M2

## Installation

### For clients

There are several ways to install extension for clients:

 1. If you've bought the product at Magento's Marketplace - use
    [Marketplace installation instructions](https://docs.magento.com/marketplace/user_guide/buyers/install-extension.html)
 2. Otherwise, you have two options:
    - Install the sources directly from [our repository](https://docs.swissuplabs.com/m2/extensions/recaptcha/installation/composer/) - **recommended**
    - Download archive and use [manual installation](https://docs.swissuplabs.com/m2/extensions/recaptcha/installation/manual/)

### For developers

Use this approach if you have access to our private repositories!

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-recaptcha
bin/magento module:enable Swissup_Recaptcha Swissup_Core
bin/magento setup:upgrade
```

## Notice

Current version of this module does not provide security CAPTCHAs to any other form than your Magento instance. It replaces default Magento CAPTCHA. So if your store has a disabled CAPTCHA, then you have to enable it first. Please, read Magento User Guide how to do that - [Security CAPTCHA](https://docs.magento.com/m2/ce/user_guide/stores/security-captcha.html).

Read more at [docs.swissuplabs.com](http://docs.swissuplabs.com/m2/extensions/recaptcha/).
