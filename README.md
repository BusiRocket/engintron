![Engintron](https://engintron.com/images/logo/v1.1/engintron_logo_v1.1_900x320_24.png)

_Engintron for cPanel/WHM is the easiest way to integrate Nginx on your cPanel/WHM server. Engintron will improve the performance & web serving capacity of your server, while reducing CPU/RAM load at the same time. It does that by installing & configuring the popular Nginx webserver to act as a reverse caching proxy for static files (like CSS, JS, images etc.) with an additional micro-cache layer to significantly improve performance of dynamic content generated by CMSs like WordPress, Joomla or Drupal as well as forum software like vBulletin, phpBB, SMF or e-commerce solutions like WooCommerce, Magento, OpenCart, PrestaShop and others._

---

### Engintron v2.0 (Build 20220117) / Updated on January 17th, 2022

**Please have a look at the [CHANGELOG](https://engintron.com/docs/#/pages/Changelog) for additional information.**

Quick Links: [Engintron.com](https://engintron.com) | [Documentation](https://engintron.com/docs/) | [Support & Feedback](https://github.com/engintron/engintron/issues) | [Newsletter](https://tinyletter.com/engintron) | Follow on [Twitter](https://twitter.com/engintron) or [Facebook](https://www.facebook.com/engintron)

---

![Engintron v2 WHM App (kudos to Anthony Boyd Graphics for the IBM Desktop mockup)](https://user-images.githubusercontent.com/1301787/147956182-e2e11894-32e9-4b45-92b4-b85e47e52a7e.jpg)

## Engintron is Nginx on cPanel, done right!
[Nginx®](https://nginx.org) is a powerful open source web server that was built to scale websites to millions of visitors. [cPanel®](https://cpanel.net) is the leading hosting control panel worldwide.

Engintron integrates Nginx into cPanel so you can enjoy amazing performance for your sites, without having to sacrifice important hosting features found in cPanel.

_And best of all? Engintron is totally free to use!_

### But why should you use Nginx in your cPanel server?
cPanel uses the Apache webserver to serve websites by default. Apache however is not known to perform well under heavy web traffic (especially traffic spikes) and it's also CPU/RAM hungry. So how can you mitigate these issues? The answer is simple: by installing Engintron, which deploys Nginx (the most popular web server software) as a reverse caching proxy in front of Apache.

Nginx will then directly serve all static assets like CSS, JS, images etc. instead of Apache & also add a 1 second micro-cache layer to significantly improve performance for dynamic HTML content generated by CMSs (like WordPress, Joomla, Drupal), forum software (like vBulletin, phpBB, SMF) or e-commerce software (like WooCommerce, Magento, OpenCart, PrestaShop) & generally any dynamic web application that is hosted on your cPanel server. Subsequently, this will significantly reduce the CPU/RAM resources consumed by Apache (and PHP, Ruby, Node.js etc.), leaving your server with more available resources for other tasks or, better still, with room for more websites to host.

The way Engintron sets up Nginx inside your cPanel is a lot like how the popular CloudFlare CDN works. Nginx (like CloudFlare) directly serves all static content like CSS, JS, images etc. instead of your actual web server, thus lowering the load on your cPanel server. But unlike CloudFlare which requires that all your domains are set up with that service, you do everything inside your cPanel server. And better still? You also get to have an additional caching layer for dynamic HTML content for when your traffic spikes. And not just for select websites, but for your entire server!

This additional caching layer (the 1 second micro-cache as previously mentioned) will only cache GET & HEAD requests (never POST requests) which means that it is possible to use it on any type of website, either a small dynamic WordPress or Joomla corporate website or blog to a more complex news portal or forum or e-commerce websites that require users to log in (to post content or shop).

Engintron is therefore ideal for any type of website and it can raise the number of concurrent requests served by your cPanel server from a few hundred per second (using just Apache) to thousands (using Nginx in front of Apache).

Not only will your serving capacity increase, but the load on your server will also significantly drop :)

If you are facing performance issues with your cPanel server, Engintron is your go-to solution. And in fact it's really a "set & forget" solution as you'll set it up once and then it will just run on your server without any additional maintenance on your side.

If you can sign up for a cPanel/WHM server on any hosting company and work your way through WHM, then setting up Engintron should be a piece of cake for you. If you don't manage your cPanel server, then you can always (kindly) ask your hosting company or system administrator to have a look at Engintron and deploy it on your cPanel server. It really only takes a few minutes and there is zero configuration afterwards to get the standard optimizations offered by Nginx.

### OK, I'm sold! How do I install Engintron on my cPanel server?
Installation is a process that lasts only a few minutes.

You'll need root SSH access to your cPanel server. Also check the current requirements (listed lower).

If everything is ok, log in as root and type the following command:

```
curl -sSL https://raw.githubusercontent.com/engintron/engintron/master/engintron.sh | bash -s -- install
```

If cURL is not available on your system, you can use wget like so:

```
wget --no-check-certificate -O - https://raw.githubusercontent.com/engintron/engintron/master/engintron.sh | bash -s -- install
```

The process will take a couple of minutes to complete and after that, Engintron will be installed on your cPanel server. Engintron has a nice & simple user interface which is activated inside WHM, under the Plugins section. After installation, refresh WHM in your browser and you should see Engintron in the Plugins section (it's the absolute last section in WHM's sidebar).

In there, you'll find basic options to control Nginx, Apache and MySQL, all in one convenient place. Additionally, you can edit all of Nginx's configuration files (as well as some from Apache & MySQL) to get even more from Engintron (e.g. configure Engintron for use with CloudFlare). If however all you want is to accelerate both static & dynamic content delivery, then Engintron is already setup for you and you don't need to do anything more.

Inside the Engintron app dashboard you'll also find some handy small utilities that make managing your server more productive.

And if for some reason you find that Engintron does not meet your needs, you can always [uninstall it](https://engintron.com/docs/#/pages/remove) and it will revert your system as it were, before you installed Engintron in the first place.

_For more information regarding setup & configuration, release changelog, FAQ as well as cPanel/server optimization guides & more please visit the project's documentation pages at: [https://engintron.com/docs/](https://engintron.com/docs/)_

### Worth reading

* [Why is Engintron a better solution compared to other Nginx (or performance related) plugins for cPanel?](https://engintron.com/docs/#/pages/ten-reasons-why)
* [Not so fast buddy... cPanel officially added support for Nginx in 2021. Why should I use Engintron?](https://engintron.com/docs/#/pages/engintron-vs-cpanel-nginx)

![Engintron_v2_20220103_2048x1072](https://user-images.githubusercontent.com/1301787/147961377-766f2c64-0042-424c-bd51-7b130a537d65.jpg)

---

## GENERAL RESOURCES

### Compatibility & Requirements
Engintron is tested only on platforms that are actively supported by cPanel itself.

As such, as of January 2022, Engintron is fully compatible with CentOS 6 with CloudLinux (the only actively supported Enterprise Linux variant by cPanel as CentOS 6 is officially EOL since 2020), CentOS 7 and all EL 8 variants such as AlmaLinux, Rocky Linux & CentOS 8 (for which support by Red Hat stopped at the end of 2021).

Engintron should also work on other EL 8 variants (e.g. Oracle Linux, Amazon Linux etc.) that can run cPanel (albeit "unofficially")...

Additionally, once cPanel is officially released to also support Ubuntu (in the near future), Engintron will also be released to support Ubuntu as well.

### Documentation
For more information regarding setup & configuration, release changelog, FAQ as well as cPanel/server optimization guides & more please visit the project's documentation pages at: [https://engintron.com/docs/](https://engintron.com/docs/)

### Support & Feedback
For support or general feedback (feature requests, suggestions etc.) head over to the project's issue tracker: https://github.com/engintron/engintron/issues (GitHub Issues)

### License
Engintron is released under the [GNU/GPL license](https://www.gnu.org/copyleft/gpl.html).

---

## ENGINTRON ELSEWHERE

### Social
You can follow Engintron on [Twitter](https://twitter.com/engintron) or [Facebook](https://www.facebook.com/engintron).

### Newsletter / Mailing List
It's easy to miss an Engintron update on social media. If you want to know for sure when the latest version of Engintron is released, sign up here https://tinyletter.com/engintron to get notified directly to your inbox. We will never spam you.

### cPanel Applications Directory
If you use Engintron, please take a moment to post a review and/or rating in the cPanel Applications Directory at: https://applications.cpanel.com/listings/view/Engintron-Nginx-on-cPanel

---

## COMMERCIAL SUPPORT & SERVER OPTIMIZATION SERVICES
Engintron will greatly improve your cPanel server's performance, but it will only get you halfway through to what your hardware can actually support, especially when all crucial server components like Apache, MySQL/MariaDB or PHP use "stock" configurations (or worse, badly optimized configurations), unsuitable for your server's hardware specifications.

And although we do provide optimization guides in the Engintron documentation site (see "BEYOND ENGINTRON - OPTIMIZATION GUIDES" here [https://engintron.com/docs/](https://engintron.com/docs/)), it takes experience to fine tune any configuration to match a server's specifications.

The performance optimization package we offer involves tuning the most essential services:
* Apache, MySQL/MariaDB and PHP
* the system's network throughput and disk I/O
* installing a new optimized EasyApache 4 profile with support for PHP versions 5.6, 7.x and 8.x & switching the server to PHP-FPM exclusively while setting up caching options like APCu, Opcache & Memcached (with support for PHP code)
* properly configuring the server's firewall for basic DoS protection

At the end you get a full report of what has been optimized.

So, if you wish to go the "extra mile" and optimize your cPanel server both through Engintron as well as through the services that directly affect the server's performance, feel free to use the contact options from within Engintron's WHM app to get in touch with us. Or you can simply email us at: engintron [at] gmail [dot] com

---

**Copyright &copy; 2014 - 2022 [Kodeka OÜ](https://kodeka.io)**
